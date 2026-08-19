<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraVfdService
{
    public const VERSION = '2.0.0';
    
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
     * 1-18%(Standard Rated), 3-0%(Zero rated), 4-0%(Special Relief), 5-0%(Exempted)
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

        // Default to 18% if tax_rate is 0 or null
        $taxPercent = (int) ($settings->tax_rate ?? 18);
        if ($taxPercent <= 0) {
            $taxPercent = 18;
        }

        // Build items XML and calculate per-item VAT
        // Selling prices are VAT-inclusive. TRA expects VAT-inclusive amounts
        // in <PRICE> and <AMT>. VAT is extracted: AMT * rate / (100 + rate)
        $itemsXml = '';
        $itemIndex = 1;
        $totalVatAmt = 0.00;
        $totalGrossAmt = 0.00;

        foreach ($items as $item) {
            $product = $item->product;
            $taxCode = $this->getTraTaxCode((int) ($product->tax_code ?? 1));
            $desc = $this->escapeXml($product->name ?? 'Item');
            $qty = (int) $item->quantity;

            // Selling prices are already VAT-inclusive, use as-is
            $price = round((float) $item->unit_price, 2);
            $amt = round((float) $item->total, 2);

            // Extract VAT from inclusive amount using TRA formula
            // VAT = AMT * 18 / 118
            $itemVat = 0.00;
            if ($taxCode == 1) {
                $itemVat = round($amt * $taxPercent / (100 + $taxPercent), 2);
            }

            $totalVatAmt += $itemVat;
            $totalGrossAmt += $amt;

            $priceStr = number_format($price, 2, '.', '');
            $amtStr = number_format($amt, 2, '.', '');

            $itemsXml .= "<ITEM><ID>{$itemIndex}</ID><DESC>{$desc}</DESC><QTY>{$qty}</QTY><TAXCODE>{$taxCode}</TAXCODE><PRICE>{$priceStr}</PRICE><AMT>{$amtStr}</AMT></ITEM>";
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

        // vatAmt = exact sum of per-item rounded VAT (never recalculate independently)
        $vatAmt = number_format(round($totalVatAmt, 2), 2, '.', '');
        // grossAmt = sum of all item amounts (already VAT-inclusive)
        $grossAmt = number_format(round($totalGrossAmt, 2), 2, '.', '');

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

        Log::info('TRA VFD Posting v' . self::VERSION, [
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

            Log::info('TRA VFD Parsed Response', [
                'command_code' => $result['command_code'],
                'verification_link' => $result['verification_link'],
                'qr_code' => $result['qr_code'],
                'receipt_number' => $result['receipt_number'],
                'raw_body' => $body,
            ]);

            $commandCode = $result['command_code'];

            // 502 = Success, 59 = Duplicate (already posted - still success)
            if ($commandCode == '502' || $commandCode == '59') {
                $isDuplicate = ($commandCode == '59');
                $verificationLink = $result['verification_link'] ?? '';
                $qrCode = $result['qr_code'] ?? '';
                $receiptNumber = $result['receipt_number'] ?? '';

                // If receipt number not in response, extract from verification link
                if (empty($receiptNumber) && !empty($verificationLink)) {
                    $parts = explode('/', rtrim($verificationLink, '/'));
                    $receiptNumber = end($parts) ?: $sale->invoice_number;
                }
                if (empty($receiptNumber)) {
                    $receiptNumber = $sale->invoice_number;
                }

                // Save TRA response to sale
                $sale->update([
                    'tra_receipt_number' => $receiptNumber,
                    'tra_verification_link' => $verificationLink,
                    'tra_qr_code' => $qrCode,
                    'tra_status' => 'posted',
                ]);

                return [
                    'success' => true,
                    'duplicate' => $isDuplicate,
                    'message' => $isDuplicate ? 'Receipt was already posted to TRA' : 'Receipt posted to TRA successfully',
                    'verification_link' => $verificationLink,
                    'qr_code' => $qrCode,
                    'receipt_number' => $receiptNumber,
                    'raw_response' => $body,
                ];
            } else {
                $errorMsg = $this->getErrorMessage($commandCode);
                return [
                    'success' => false,
                    'error' => $errorMsg,
                    'command_code' => $commandCode,
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
            $xml = trim($xml, "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x20");
            
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadXML($xml);
            libxml_clear_errors();

            $commandCodeNodes = $doc->getElementsByTagName('Command_Code');
            if ($commandCodeNodes->length > 0) {
                $result['command_code'] = $commandCodeNodes->item(0)->nodeValue;
            }

            // Try multiple tag names for verification link
            foreach (['VerificationLink', 'Link', 'VerLink', 'verificationlink', 'VerifyLink'] as $tag) {
                $nodes = $doc->getElementsByTagName($tag);
                if ($nodes->length > 0 && !empty(trim($nodes->item(0)->nodeValue))) {
                    $result['verification_link'] = trim($nodes->item(0)->nodeValue);
                    break;
                }
            }

            // Try multiple tag names for QR code
            foreach (['QRCode', 'QR', 'qr', 'qrcode', 'QRUrl'] as $tag) {
                $nodes = $doc->getElementsByTagName($tag);
                if ($nodes->length > 0 && !empty(trim($nodes->item(0)->nodeValue))) {
                    $result['qr_code'] = trim($nodes->item(0)->nodeValue);
                    break;
                }
            }

            // Try multiple tag names for receipt number
            foreach (['ReceiptNum', 'ReceiptNo', 'ReceiptNumber', 'receiptnum', 'RctNum'] as $tag) {
                $nodes = $doc->getElementsByTagName($tag);
                if ($nodes->length > 0 && !empty(trim($nodes->item(0)->nodeValue))) {
                    $result['receipt_number'] = trim($nodes->item(0)->nodeValue);
                    break;
                }
            }

            // Regex fallback if XML parsing failed or tags not found
            if (empty($result['command_code'])) {
                if (preg_match('/<Command_Code>(\d+)<\/Command_Code>/', $xml, $m)) {
                    $result['command_code'] = $m[1];
                }
            }
            if (empty($result['verification_link'])) {
                if (preg_match('/<(?:VerificationLink|Link|VerLink)>([^<]+)<\/(?:VerificationLink|Link|VerLink)>/i', $xml, $m)) {
                    $result['verification_link'] = trim($m[1]);
                }
            }
            if (empty($result['qr_code'])) {
                if (preg_match('/<(?:QRCode|QR|qr)>([^<]+)<\/(?:QRCode|QR|qr)>/i', $xml, $m)) {
                    $result['qr_code'] = trim($m[1]);
                }
            }
            if (empty($result['receipt_number'])) {
                if (preg_match('/<(?:ReceiptNum|ReceiptNo|ReceiptNumber|RctNum)>([^<]+)<\/(?:ReceiptNum|ReceiptNo|ReceiptNumber|RctNum)>/i', $xml, $m)) {
                    $result['receipt_number'] = trim($m[1]);
                }
            }
        } catch (\Exception $e) {
            Log::error('TRA VFD XML Parse Error', ['error' => $e->getMessage()]);
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
            '115' => 'Items VAT amount summation does not match with the Provided Total VAT Amount',
            '116' => 'Items amount summation does not match with the Provided Total Amount',
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
