<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    protected $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Product::with(['category', 'brand', 'unit'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('brand', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Barcode',
            'Category',
            'Brand',
            'Unit',
            'Description',
            'Specifications',
            'Cost Price',
            'Selling Price',
            'Quantity',
            'Reorder Level',
            'Expiry Date',
            'Batch Number',
            'Status',
            'Available Online',
            'Created At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku ?? '-',
            $product->barcode ?? '-',
            $product->category->name ?? '-',
            $product->brand->name ?? '-',
            $product->unit->name ?? ($product->unit->short_name ?? '-'),
            $product->description,
            $product->specifications,
            $product->cost_price,
            $product->selling_price,
            $product->quantity,
            $product->reorder_level,
            $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '',
            $product->batch_number ?? '',
            $product->is_active ? 'Active' : 'Inactive',
            $product->is_available_online ? 'Yes' : 'No',
            $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function title(): string
    {
        return 'Products';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}