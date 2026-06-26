<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'All electronic devices and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'All food and drink items',
                'is_active' => true,
            ],
            [
                'name' => 'Clothing',
                'description' => 'All clothing and apparel',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Household items, utensils, and kitchen tools',
                'is_active' => true,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Cosmetics, skincare, and personal hygiene products',
                'is_active' => true,
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Medical, supplements, and wellness products',
                'is_active' => true,
            ],
            [
                'name' => 'Agriculture Products',
                'description' => 'Farm produce, seeds, and farming supplies',
                'is_active' => true,
            ],
            [
                'name' => 'Stationery & Office',
                'description' => 'Office supplies, books, and stationery items',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'is_active' => true,
            ],
            [
                'name' => 'Electronics Accessories',
                'description' => 'Chargers, cables, headphones, and gadgets extras',
                'is_active' => true,
            ],
            [
                'name' => 'Baby & Kids',
                'description' => 'Baby products, toys, and children essentials',
                'is_active' => true,
            ],
            [
                'name' => 'Automotive',
                'description' => 'Vehicle parts and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Other Categories',
                'description' => 'Additional system-defined categories',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_active' => $category['is_active'],
                ]
            );
        }
    }
}
