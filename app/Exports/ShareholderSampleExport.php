<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShareholderSampleExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            ['John Doe', 'john@example.com', '+255712345678', '123 Main Street, Dar es Salaam', 100, 10000, '2026-06-22', 'Initial shares'],
            ['Jane Smith', 'jane@example.com', '+255787654321', '456 Oak Avenue, Arusha', 50, 10000, '2026-06-22', 'Initial shares'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'email', 'phone', 'address', 'number_of_shares', 'share_price', 'date', 'description'];
    }

    public function title(): string
    {
        return 'Shareholders Sample';
    }
}
