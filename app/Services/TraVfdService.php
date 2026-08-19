<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraVfdService
{
    private ?StoreSetting $settings;
    private string $endpoint;
    private string $username;
    private string $password;
    private string $tinNumber;
    private string $vfdSerial;
    private string $licence;

    public function __construct()
    {
        $this->settings = StoreSetting::first();
        $this->endpoint = $this->settings->tra_api_endpoint ?? 'http://162.55.181.173:8080/TRA_VFD/Operations';
        $this->username = $this->settings->tra_api_username ?? '';
        $this->password = $this->settings->tra_api_password ?? '';
        $this->tinNumber = $this->settings->tra_tin_number ?? '';
        $this->vfdSerial = $this->settings->tra_vfd_serial ?? '';
        $this->licence = $this->settings->tra_licence ?? '';
    }

    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password) && !empty($this->tinNumber) && !empty($this->vfdSerial) && !empty($this->licence);
    }

    /**
     * Map product tax_code to TRA tax code
     * 1=18% Standard, 3=0% Zero rated, 4=0% Special Relief, 5=0% Exempted
     */
    private function getTraTaxCode(int $productTaxCode): int
    {
        $validCodes = [1, 3, 4, 5];
        return in_array($productTaxCode, $validCodes) ? $productTaxCode : 1;
    }

    /**
     * Calculate signature: SHA1 of <data>payload</data>@username@password
     */
    private function calculateSignature(string $dataXml): string
    {
        $payload = '<data>' . $dataXml . '</data>@' . $this->username . '@' . $this->password;
        return strtoupper(sha1($payload));
    }

    /**
     * Build the data XML block (everything inside <data>...</data>)
     */
    private function buildDataXml(Sale $sale): string
    {
        $settings = $this->settings;
        $items = $sale->items()->with('product')->get();

        // Build items XML
        $itemsXml = '';
        $itemIndex = 1;
        foreach ($items as $item) {
            $product = $item->product;
            $taxCode = $this->getTraTaxCode($product->tax_code ?? 1);
            $desc = $this->escapeXml($product->name ?? 'Item');
            $qty = (int) $item->quantity;
            $price = number_format($item->unit_price, 2, '.', '');
            $amt = number_format($item->total, 2, '.', '');

            $itemsXml .= "<ITEM><ID>{$itemIndex}</ID><DESC>{$desc}</DESC><QTY>{$qty}</QTY><TAXCODE>{$taxCode}</TAXCODE><PRICE>{$price}</PRICE><AMT>{$amt}</AMT></ITEM>";
            $itemIndex++;
        }

        // Customer info
        $customer = $sale->customer;
        $cusName = $this->escapeXml($customer->name ?? 'Walk-in Customer');
        $custTIN = '6@NIL';
        if ($customer && !empty($customer->tin_number)) {
            $custTIN = '1@' . $customer->tin_number;
        }

        $custVRN = $customer->vrn_number ?? 'null';

        // Calculate VAT amount (18% standard rate)
        $vatAmt = number_format($sale->tax ?? 0, 2, '.', '');
        if (($sale->tax ?? 0) == 0 && $settings->tax_enabled) {
            // If tax is 0 but tax is enabled, calculate from total
            $totalExclVat = ($sale->total ?? 0) / 1.18;
            $vatAmt = number_format(($sale->total ?? 0) - $totalExclVat, 2, '.', '');
        }

        $grossAmt = number_format($sale->total ?? 0, 2, '.', '');

        // Payment breakdown
        $cash = '0.00';
        $cheque = '0.00';
        $ccard = '0.00';
        $emoney = '0.00';
        $invoice = '0.00';

        switch ($sale->payment_method ?? 'cash') {
            case 'cash':
                $cash = $grossAmt;
                break;
            case 'card':
                $ccard = $grossAmt;
                break;
            case 'mobile':
            case 'clickpesa':
                $emoney = $grossAmt;
                break;
            case 'credit':
                $invoice = $grossAmt;
                break;
            default:
                $cash = $grossAmt;
        }

        // Date/time in East Africa timezone (EAT = UTC+3)
        $dateTime = now('Africa/Nairobi')->format('Y/m/d H:i:s');

        $permitNum = $sale->invoice_number;

        $dataXml = "<ITEMS>{$itemsXml}</ITEMS>"
            . "<Serial_num>{$this->vfdSerial}</Serial_num>"
            . "<TIN_num>{$this->tinNumber}</TIN_num>"
            . "<DocTin>{$this->tinNumber}</DocTin>"
            . "<cusname>{$cusName}</cusname>"
            . "<custTIN>{$custTIN}</custTIN>"
            . "<custVRN>{$custVRN}</custVRN>"
            . "<permitNum>{$permitNum}</permitNum>"
            . "<vatAmt>{$vatAmt}</vatAmt>"
            . "<grossAmt>{$grossAmt}</grossAmt>"
            . "<CASH>{$cash}</CASH>"
            . "<CHEQUE>{$cheque}</CHEQUE>"
            . "<CCARD>{$ccard}</CCARD>"
            . "<EMONEY>{$emoney}</EMONEY>"
            . "<INVOICE>{$invoice}</INVOICE>"
            . "<DateTime>{$dateTime}</DateTime>";

        return $dataXml;
    }

    /**
     * Build the full XML document for posting
     */
    public function buildXml(Sale $sale): string
    {
        $dataXml = $this->buildDataXml($sale);
        $signature = $this->calculateSignature($dataXml);

        $xml = '<TransData>'
            . '<Command_Code>50</Command_Code>'
            . '<Command_Descr>Saving Sale Transaction</Command_Descr>'
            . '<data>' . $dataXml . '</data>'
            . '<Auth>'
            . '<username>' . $this->escapeXml($this->username) . '</username>'
            . '<password>' . $this->escapeXml($this->password) . '</password>'
            . '<licence>' . $this->escapeXml($this->licence) . '</licence>'
            . '<signature>' . $signature . '</signature>'
            . '</Auth>'
            . '</TransData>';

        return $xml;
    }

    /**
     * Post receipt to TRA VFD API
     */
    public function postReceipt(Sale $sale): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'TRA VFD API is not configured. Please set up TIN, Serial, Licence, Username and Password in Settings > TRA VFD.',
            ];
        }

        $xml = $this->buildXml($sale);

        Log::info('TRA VFD Posting', [
            'sale_id' => $sale->id,
            'invoice' => $sale->invoice_number,
            'endpoint' => $this->endpoint,
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'Application/xml',
                ])
                ->withBody($xml, 'Application/xml')
                ->post($this->endpoint);

            $body = $response->body();

            Log::info('TRA VFD Response', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            // Parse XML response
            $result = $this->parseResponse($body);

            if ($result['command_code'] == '502') {
                // Success - receipt posted
                $verificationLink = $result['verification_link'] ?? '';
                $qrCode = $result['qr_code'] ?? '';

                // Save TRA response to sale
                $sale->update([
                    'tra_receipt_number' => $result['receipt_number'] ?? $sale->invoice_number,
                    'tra_verification_link' => $verificationLink,
                    'tra_qr_code' => $qrCode,
                    'tra_status' => 'posted',
                ]);

                return [
                    'success' => true,
                    'verification_link' => $verificationLink,
                    'qr_code' => $qrCode,
                    'receipt_number' => $result['receipt_number'] ?? $sale->invoice_number,
                    'raw_response' => $body,
                ];
            } else {
                $errorMsg = $this->getErrorMessage($result['command_code']);
                return [
                    'success' => false,
                    'error' => $errorMsg,
                    'command_code' => $result['command_code'],
                    'raw_response' => $body,
                ];
            }
        } catch (\Exception $e) {
            Log::error('TRA VFD Error', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to connect to TRA VFD API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse XML response from TRA API
     */
    private function parseResponse(string $xml): array
    {
        $result = [
            'command_code' => '',
            'verification_link' => '',
            'qr_code' => '',
            'receipt_number' => '',
        ];

        try {
            // Remove any BOM or whitespace before XML
            $xml = trim($xml, "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x20");
            
            // Try to parse as XML
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            libxml_clear_errors();

            // Get Command_Code
            $commandCodeNodes = $doc->getElementsByTagName('Command_Code');
            if ($commandCodeNodes->length > 0) {
                $result['command_code'] = $commandCodeNodes->item(0)->nodeValue;
            }

            // Get Verification Link (may be in different tags depending on API version)
            $linkNodes = $doc->getElementsByTagName('VerificationLink');
            if ($linkNodes->length > 0) {
                $result['verification_link'] = $linkNodes->item(0)->nodeValue;
            } else {
                // Try alternate tag name
                $linkNodes = $doc->getElementsByTagName('Link');
                if ($linkNodes->length > 0) {
                    $result['verification_link'] = $linkNodes->item(0)->nodeValue;
                }
            }

            // Get QR Code URL
            $qrNodes = $doc->getElementsByTagName('QRCode');
            if ($qrNodes->length > 0) {
                $result['qr_code'] = $qrNodes->item(0)->nodeValue;
            } else {
                $qrNodes = $doc->getElementsByTagName('QR');
                if ($qrNodes->length > 0) {
                    $result['qr_code'] = $qrNodes->item(0)->nodeValue;
                }
            }

            // Get Receipt Number
            $receiptNodes = $doc->getElementsByTagName('ReceiptNum');
            if ($receiptNodes->length > 0) {
                $result['receipt_number'] = $receiptNodes->item(0)->nodeValue;
            }
        } catch (\Exception $e) {
            Log::error('TRA VFD XML Parse Error', ['error' => $e->getMessage()]);
            // If XML parsing fails, try to extract command code with regex
            if (preg_match('/<Command_Code>(\d+)<\/Command_Code>/', $xml, $matches)) {
                $result['command_code'] = $matches[1];
            }
        }

        return $result;
    }

    /**
     * Get human-readable error message from TRA response code
     */
    private function getErrorMessage(string $code): string
    {
        $errors = [
            '531' => 'Invalid Username and Password Combination',
            '431' => 'Invalid Signature',
            '432' => 'Unknown Signature',
            '131' => 'Licence Expired',
            '132' => 'Invalid Serial Number / Invalid Licence',
            '133' => 'Invalid TIN Number',
            '121' => 'Incorrect date supplied',
            '119' => 'Previous date provided',
            '120' => 'Future date provided',
            '115' => 'Items VAT amount summation does not match provided total VAT amount',
            '116' => 'Items amount summation does not match provided total amount',
            '117' => 'Invalid Tax Code',
            '59' => 'Duplicate - Receipt/Invoice number has been signed already',
            '555' => 'Incorrect XML format',
        ];

        return $errors[$code] ?? "TRA Error Code: {$code}";
    }

    /**
     * Escape XML special characters
     */
    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get store settings for EFD receipt display
     */
    public function getSettings(): ?StoreSetting
    {
        return $this->settings;
    }
}
