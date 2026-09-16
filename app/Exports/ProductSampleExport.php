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
                'Organic Banana', '10', '12', '120000', '1500',
            ],
            [
                'Full Cream Milk 500ml', '5', '24', '43200', '2500',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Product', 'Cartons', 'Quantity', 'Price per carton (TZS)', 'Price per item',
        ];
    }

    public function title(): string
    {
        return 'Products Sample';
    }
}