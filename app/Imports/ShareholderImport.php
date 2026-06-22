<?php

namespace App\Imports;

use App\Models\Shareholder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class ShareholderImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Shareholder([
            'name'                => $row['name'],
            'email'               => $row['email'] ?? null,
            'phone'               => $row['phone'] ?? null,
            'address'             => $row['address'] ?? null,
            'shareholding_number' => $row['shareholding_number'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'shareholding_number' => 'nullable|string|max:255|unique:shareholders,shareholding_number',
        ];
    }
}
