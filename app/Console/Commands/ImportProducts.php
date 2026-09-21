<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportProducts extends Command
{
    protected $signature = 'import:products {file} {--dry-run : Preview import without saving} {--update-existing : Update existing products by SKU}';

    protected $description = 'Import products from CSV file';

    public function handle(): int
    {
        $file = $this->argument('file');
        $dryRun = $this->option('dry-run');
        $updateExisting = $this->option('update-existing');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("Could not open file: {$file}");
            return self::FAILURE;
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            $this->error('Empty CSV file');
            return self::FAILURE;
        }

        $expectedHeaders = [
            'ID', 'Name', 'SKU', 'Barcode', 'Scanned', 'Linked',
            'Category', 'Brand', 'Unit', 'Description', 'Specifications',
            'Cost Price', 'Selling Price', 'Quantity', 'Reorder Level',
            'Expiry Date', 'Batch Number', 'Status', 'Available Online', 'Created At'
        ];

        $missingHeaders = array_diff($expectedHeaders, $headers);
        if ($missingHeaders) {
            $this->warn('Missing columns: ' . implode(', ', $missingHeaders));
        }

        $headerMap = array_flip($headers);

        $bar = $this->output->createProgressBar();
        $bar->start();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $bar->advance();

                $data = $this->mapRow($row, $headerMap);
                if (!$data) {
                    $skipped++;
                    continue;
                }

                $result = $this->processProduct($data, $updateExisting, $dryRun);

                if ($result === 'created') {
                    $imported++;
                } elseif ($result === 'updated') {
                    $updated++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            }

            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            fclose($handle);
            $bar->finish();
            $this->newLine();
        }

        $this->info("Import complete: {$imported} created, {$updated} updated, {$skipped} skipped");

        if ($errors) {
            $this->error('Errors:');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        return self::SUCCESS;
    }

    private function mapRow(array $row, array $headerMap): ?array
    {
        $get = fn(string $col) => $row[$headerMap[$col]] ?? null;

        $sku = $get('SKU');
        $name = $get('Name');

        if (!$name) {
            return null;
        }

        return [
            'sku' => $sku,
            'name' => $name,
            'barcode' => $get('Barcode'),
            'barcode_linked_at' => $get('Linked') ? $this->parseDate($get('Linked')) : null,
            'category_name' => $get('Category'),
            'brand_name' => $get('Brand'),
            'unit_name' => $get('Unit'),
            'description' => $get('Description'),
            'specifications' => $get('Specifications'),
            'cost_price' => $this->parseDecimal($get('Cost Price')),
            'selling_price' => $this->parseDecimal($get('Selling Price')),
            'quantity' => $this->parseInt($get('Quantity')),
            'reorder_level' => $this->parseInt($get('Reorder Level')),
            'expiry_date' => $this->parseDate($get('Expiry Date')),
            'batch_number' => $get('Batch Number'),
            'is_active' => $this->parseStatus($get('Status')),
            'is_available_online' => $this->parseBool($get('Available Online')),
        ];
    }

    private function processProduct(array $data, bool $updateExisting, bool $dryRun): string
    {
        $product = null;

        if ($data['sku']) {
            $product = Product::where('sku', $data['sku'])->first();
        }

        if ($product && !$updateExisting) {
            return 'skipped';
        }

        $category = $this->findOrCreateCategory($data['category_name']);
        $brand = $this->findOrCreateBrand($data['brand_name']);
        $unit = $this->findOrCreateUnit($data['unit_name']);

        $attributes = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'sku' => $data['sku'],
            'barcode' => $data['barcode'],
            'barcode_linked_at' => $data['barcode_linked_at'],
            'category_id' => $category?->id,
            'brand_id' => $brand?->id,
            'unit_id' => $unit?->id,
            'description' => $data['description'],
            'specifications' => $data['specifications'],
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'quantity' => $data['quantity'],
            'reorder_level' => $data['reorder_level'],
            'expiry_date' => $data['expiry_date'],
            'batch_number' => $data['batch_number'],
            'is_active' => $data['is_active'],
            'is_available_online' => $data['is_available_online'],
            'tax_code' => 1,
        ];

        if ($dryRun) {
            return $product ? 'updated' : 'created';
        }

        if ($product) {
            $product->update($attributes);
            return 'updated';
        }

        Product::create($attributes);
        return 'created';
    }

    private function findOrCreateCategory(?string $name): ?Category
    {
        if (!$name) return null;

        return Category::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name), 'is_active' => true]
        );
    }

    private function findOrCreateBrand(?string $name): ?Brand
    {
        if (!$name) return null;

        return Brand::firstOrCreate(
            ['name' => $name],
            ['description' => '', 'is_active' => true]
        );
    }

    private function findOrCreateUnit(?string $name): ?Unit
    {
        if (!$name) return null;

        return Unit::firstOrCreate(
            ['name' => $name],
            ['short_name' => Str::upper(substr($name, 0, 3)), 'description' => '', 'is_active' => true]
        );
    }

    private function parseDecimal(?string $value): float
    {
        if (!$value) return 0;
        return (float) str_replace([',', ' '], '', $value);
    }

    private function parseInt(?string $value): int
    {
        if (!$value) return 0;
        return (int) $value;
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function parseStatus(?string $value): bool
    {
        if (!$value) return true;
        $value = strtolower(trim($value));
        return in_array($value, ['active', '1', 'true', 'yes', 'enabled']);
    }

    private function parseBool(?string $value): bool
    {
        if (!$value) return true;
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', 'enabled', 'active']);
    }
}