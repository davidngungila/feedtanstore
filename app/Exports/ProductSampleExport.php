<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductSampleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            [
                'Organic Banana', 'BAN-260916-001', '8971000123456', 'Fruits', 'Chiquita', 'kg',
                'Fresh organic bananas', 'Grade A, box of 10kg', 12000, 15000, 50, 10,
                '2026-10-01', 'BATCH-2026-P01', 'true', 'true',
            ],
            [
                'Full Cream Milk', 'MLK-260916-001', '8971000654321', 'Dairy', 'FreshPack', 'L',
                '500ml full cream milk', 'UHT, shelf life 9 months', 1800, 2500, 100, 20,
                '2026-12-31', 'BATCH-2026-P02', 'true', 'false',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name', 'sku', 'barcode', 'category', 'brand', 'unit',
            'description', 'specifications', 'cost_price', 'selling_price',
            'quantity', 'reorder_level', 'expiry_date', 'batch_number',
            'is_active', 'is_available_online',
        ];
    }

    public function title(): string
    {
        return 'Products Sample';
    }
}