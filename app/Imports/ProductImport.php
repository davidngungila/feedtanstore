<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Factory;

class ProductImport implements OnEachRow, WithHeadingRow, SkipsOnFailure
{
    protected $importedCount = 0;
    protected $updatedCount = 0;
    protected $failures = [];

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();
        $index = $row->getRowIndex();

        $normalized = $this->normalizeRow($rowData);

        if (empty($normalized['name'])) {
            $this->failures[] = "Row " . $index . ": product name is required";
            return;
        }

        $validator = app(Factory::class)->make($normalized, $this->rules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->failures[] = "Row " . $index . ": " . $error;
            }
            return;
        }

        DB::beginTransaction();

        try {
            $category = $this->resolveCategory($normalized['category']);
            $brand = $this->resolveBrand($normalized['brand']);
            $unit = $this->resolveUnit($normalized['unit']);

            $payload = [
                'name'                 => $normalized['name'],
                'sku'                  => $normalized['sku'] ?: $this->generateUniqueSku($normalized['name']),
                'barcode'              => $normalized['barcode'] ?: null,
                'barcode_linked_at'     => $normalized['barcode'] ? now() : null,
                'category_id'          => $category->id,
                'brand_id'             => $brand ? $brand->id : null,
                'unit_id'              => $unit->id,
                'description'          => $normalized['description'],
                'specifications'       => $normalized['specifications'],
                'cost_price'           => $normalized['cost_price'],
                'selling_price'        => $normalized['selling_price'],
                'quantity'             => $normalized['quantity'],
                'reorder_level'        => $normalized['reorder_level'],
                'expiry_date'          => $normalized['expiry_date'],
                'batch_number'         => $normalized['batch_number'],
                'is_active'            => $normalized['is_active'],
                'is_available_online'  => $normalized['is_available_online'],
            ];

            $existing = null;
            if ($payload['sku']) {
                $existing = Product::where('sku', $payload['sku'])->first();
            }
            if (!$existing && $payload['barcode']) {
                $existing = Product::where('barcode', $payload['barcode'])->first();
            }

            if ($existing) {
                $updatePayload = $existing->barcode
                    ? array_merge($payload, ['sku' => $existing->sku, 'barcode' => $existing->barcode, 'barcode_linked_at' => $existing->barcode_linked_at])
                    : array_merge($payload, ['sku' => $existing->sku]);

                $existing->update($updatePayload);
                $this->updatedCount++;
            } else {
                Product::create($payload);
                $this->importedCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->failures[] = "Row " . $index . ": " . $e->getMessage();
        }
    }

    protected function normalizeRow(array $row): array
    {
        $expiry = $row['expiry_date'] ?? null;
        if ($expiry !== null && $expiry !== '') {
            if (is_numeric($expiry)) {
                $expiry = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($expiry)->format('Y-m-d');
            } else {
                $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d', 'd M Y', 'd F Y'];
                $parsed = null;
                foreach ($formats as $format) {
                    $dt = \DateTime::createFromFormat($format, (string) $expiry);
                    if ($dt !== false) {
                        $parsed = $dt->format('Y-m-d');
                        break;
                    }
                }
                if ($parsed === null && strtotime((string) $expiry) !== false) {
                    $parsed = date('Y-m-d', strtotime((string) $expiry));
                }
                $expiry = $parsed;
            }
        }

        $cartons = isset($row['cartons']) ? (int) $row['cartons'] : 0;
        $piecesPerUnit = isset($row['pcs']) ? (int) $row['pcs'] : 0;

        if ($cartons > 0 && $piecesPerUnit > 0) {
            $quantity = $cartons * $piecesPerUnit;
        } elseif ($cartons > 0) {
            $quantity = $cartons;
        } else {
            $quantity = isset($row['quantity']) ? (int) $row['quantity'] : 0;
        }

        $unit = isset($row['unit']) ? trim((string) $row['unit']) : 'pcs';

        $costPrice = 0;
        if (isset($row['price_per_carton_tzs'])) {
            $costPrice = (float) $row['price_per_carton_tzs'];
        } elseif (isset($row['cost_price'])) {
            $costPrice = (float) $row['cost_price'];
        }

        $sellingPrice = 0;
        if (isset($row['price_per_item'])) {
            $sellingPrice = (float) $row['price_per_item'];
        } elseif (isset($row['selling_price'])) {
            $sellingPrice = (float) $row['selling_price'];
        }

        if ($cartons > 0 && $piecesPerUnit > 0 && $costPrice > 0) {
            $costPrice = $costPrice / $piecesPerUnit;
            $costPrice = round($costPrice, 2);
        }

        return [
            'name'                => isset($row['product']) ? trim((string) $row['product']) : (isset($row['name']) ? trim((string) $row['name']) : null),
            'sku'                 => isset($row['sku']) ? trim((string) $row['sku']) : null,
            'barcode'             => isset($row['barcode']) ? trim((string) $row['barcode']) : null,
            'category'            => isset($row['category']) ? trim((string) $row['category']) : 'General',
            'brand'               => isset($row['brand']) ? trim((string) $row['brand']) : null,
            'unit'                => $unit,
            'description'         => isset($row['description']) ? trim((string) $row['description']) : null,
            'specifications'      => isset($row['specifications']) ? trim((string) $row['specifications']) : null,
            'cost_price'          => $costPrice,
            'selling_price'       => $sellingPrice,
            'quantity'            => $quantity,
            'reorder_level'       => isset($row['reorder_level']) ? (int) $row['reorder_level'] : 0,
            'expiry_date'         => $expiry,
            'batch_number'        => isset($row['batch_number']) ? trim((string) $row['batch_number']) : null,
            'is_active'           => isset($row['is_active']) ? $this->toBool($row['is_active']) : true,
            'is_available_online' => isset($row['is_available_online']) ? $this->toBool($row['is_available_online']) : false,
        ];
    }

    protected function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'active', 'enabled'], true);
    }

    protected function resolveCategory(?string $name): Category
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '') {
            $trimmed = 'General';
        }
        return Category::firstOrCreate(
            ['name' => $trimmed],
            ['description' => 'Auto-created from product import']
        );
    }

    protected function resolveBrand(?string $name): ?Brand
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '') {
            return null;
        }
        return Brand::firstOrCreate(
            ['name' => $trimmed],
            ['description' => 'Auto-created from product import']
        );
    }

    protected function resolveUnit(?string $name): Unit
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '') {
            $trimmed = 'pcs';
        }
        return Unit::firstOrCreate(
            ['name' => $trimmed],
            ['short_name' => substr($trimmed, 0, 3), 'description' => 'Auto-created from product import']
        );
    }

    protected function generateUniqueSku(?string $name = null): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $name ?? 'PRD') ?: 'PRD', 0, 4));

        do {
            $sku = $base . '-' . now()->format('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'cost_price'           => 'nullable|numeric|min:0',
            'selling_price'        => 'nullable|numeric|min:0',
            'quantity'             => 'nullable|numeric|min:0',
            'reorder_level'        => 'nullable|numeric|min:0',
            'expiry_date'          => 'nullable|date',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = "Row " . $failure->row() . ": " . implode(", ", $failure->errors());
        }
    }
}