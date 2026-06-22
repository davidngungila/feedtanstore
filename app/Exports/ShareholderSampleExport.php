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
            ['FEEDTANSTORE-26-01', 'John Doe', 'john@example.com', '+255712345678', '123 Main Street, Dar es Salaam'],
            ['', 'Jane Smith', 'jane@example.com', '+255787654321', '456 Oak Avenue, Arusha'],
        ];
    }

    public function headings(): array
    {
        return ['shareholding_number', 'name', 'email', 'phone', 'address'];
    }

    public function title(): string
    {
        return 'Shareholders Sample';
    }
}
