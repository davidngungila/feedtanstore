<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShareholderSampleExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john@example.com',
                '+255712345678',
                '123 Main Street, Dar es Salaam',
            ],
            [
                'Jane Smith',
                'jane@example.com',
                '+255787654321',
                '456 Oak Avenue, Mwanza',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'address',
        ];
    }
}
