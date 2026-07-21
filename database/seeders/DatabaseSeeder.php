<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Store Settings first
        $this->call(StoreSettingSeeder::class);
        
        // Seed Chart of Accounts
        $this->call(ChartOfAccountsSeeder::class);
        
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@feedtan.co.tz',
            'password' => bcrypt('password'),
        ]);

        // Seed Categories
        $this->call(CategorySeeder::class);

        // Seed Brands
        Brand::create(['name' => 'Apple', 'description' => 'Apple products']);
        Brand::create(['name' => 'Samsung', 'description' => 'Samsung products']);
        Brand::create(['name' => 'Nike', 'description' => 'Nike apparel']);

        // Seed Units
        Unit::create(['name' => 'Piece', 'short_name' => 'pc', 'description' => 'Single item']);
        Unit::create(['name' => 'Kilogram', 'short_name' => 'kg', 'description' => 'Weight in kilograms']);
        Unit::create(['name' => 'Gram', 'short_name' => 'g', 'description' => 'Weight in grams']);
        Unit::create(['name' => 'Liter', 'short_name' => 'L', 'description' => 'Volume in liters']);
        Unit::create(['name' => 'Milliliter', 'short_name' => 'mL', 'description' => 'Volume in milliliters']);
        Unit::create(['name' => 'Meter', 'short_name' => 'm', 'description' => 'Length in meters']);
        Unit::create(['name' => 'Centimeter', 'short_name' => 'cm', 'description' => 'Length in centimeters']);
        Unit::create(['name' => 'Box', 'short_name' => 'box', 'description' => 'Packaged in a box']);
        Unit::create(['name' => 'Carton', 'short_name' => 'ctn', 'description' => 'Packaged in a carton']);
        Unit::create(['name' => 'Pack', 'short_name' => 'pk', 'description' => 'Packaged in a pack']);
        Unit::create(['name' => 'Dozen', 'short_name' => 'doz', 'description' => 'Twelve items']);
        Unit::create(['name' => 'Pair', 'short_name' => 'pr', 'description' => 'Two items as a pair']);
        Unit::create(['name' => 'Set', 'short_name' => 'set', 'description' => 'Multiple items as a set']);

        // Seed Locations
        Location::create(['name' => 'Main Warehouse', 'type' => 'warehouse', 'address' => '123 Main St, City']);
        Location::create(['name' => 'Retail Store 1', 'type' => 'store', 'address' => '456 Market St, City']);

        // Seed Suppliers
        Supplier::create(['name' => 'Tech Supplies Inc.', 'email' => 'contact@techsupplies.com', 'phone' => '1234567890', 'contact_person' => 'John Doe']);
        Supplier::create(['name' => 'Food Distributors Ltd.', 'email' => 'info@fooddist.com', 'phone' => '0987654321', 'contact_person' => 'Jane Smith']);

        // Seed Products
        Product::create([
            'name' => 'iPhone 15',
            'sku' => 'IPHONE-15',
            'barcode' => '1234567890123',
            'category_id' => 1,
            'brand_id' => 1,
            'unit_id' => 1,
            'description' => 'Latest Apple smartphone',
            'cost_price' => 2000000,
            'selling_price' => 3000000,
            'quantity' => 50,
            'reorder_level' => 10,
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Samsung Galaxy S24',
            'sku' => 'GALAXY-S24',
            'barcode' => '1234567890124',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_id' => 1,
            'description' => 'Latest Samsung smartphone',
            'cost_price' => 1800000,
            'selling_price' => 2700000,
            'quantity' => 45,
            'reorder_level' => 8,
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Nike Air Max 270',
            'sku' => 'NIKE-AM270',
            'barcode' => '1234567890125',
            'category_id' => 3,
            'brand_id' => 3,
            'unit_id' => 1,
            'description' => 'Comfortable running shoes',
            'cost_price' => 80000,
            'selling_price' => 150000,
            'quantity' => 30,
            'reorder_level' => 5,
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Maize Flour (2kg)',
            'sku' => 'MAIZE-2KG',
            'barcode' => '1234567890126',
            'category_id' => 2,
            'brand_id' => null,
            'unit_id' => 1,
            'description' => 'Premium quality maize flour',
            'cost_price' => 3000,
            'selling_price' => 5000,
            'quantity' => 100,
            'reorder_level' => 20,
            'expiry_date' => '2026-12-31',
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Cooking Oil (1L)',
            'sku' => 'OIL-1L',
            'barcode' => '1234567890127',
            'category_id' => 2,
            'brand_id' => null,
            'unit_id' => 3,
            'description' => 'Vegetable cooking oil',
            'cost_price' => 4500,
            'selling_price' => 7500,
            'quantity' => 80,
            'reorder_level' => 15,
            'expiry_date' => '2027-03-15',
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Rice (5kg)',
            'sku' => 'RICE-5KG',
            'barcode' => '1234567890128',
            'category_id' => 2,
            'brand_id' => null,
            'unit_id' => 2,
            'description' => 'Long grain rice',
            'cost_price' => 12000,
            'selling_price' => 20000,
            'quantity' => 60,
            'reorder_level' => 10,
            'expiry_date' => '2027-01-01',
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Sugar (1kg)',
            'sku' => 'SUGAR-1KG',
            'barcode' => '1234567890129',
            'category_id' => 2,
            'brand_id' => null,
            'unit_id' => 2,
            'description' => 'White granulated sugar',
            'cost_price' => 2500,
            'selling_price' => 4000,
            'quantity' => 120,
            'reorder_level' => 25,
            'expiry_date' => '2027-06-01',
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Milk (1L)',
            'sku' => 'MILK-1L',
            'barcode' => '1234567890130',
            'category_id' => 2,
            'brand_id' => null,
            'unit_id' => 3,
            'description' => 'Fresh pasteurized milk',
            'cost_price' => 1800,
            'selling_price' => 3000,
            'quantity' => 90,
            'reorder_level' => 20,
            'expiry_date' => '2026-06-20',
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Apple MacBook Air',
            'sku' => 'MAC-AIR-M2',
            'barcode' => '1234567890131',
            'category_id' => 1,
            'brand_id' => 1,
            'unit_id' => 1,
            'description' => 'M2 chip laptop',
            'cost_price' => 3500000,
            'selling_price' => 5000000,
            'quantity' => 15,
            'reorder_level' => 3,
            'is_active' => true
        ]);
        Product::create([
            'name' => 'Adidas T-Shirt',
            'sku' => 'ADIDAS-TSHIRT',
            'barcode' => '1234567890132',
            'category_id' => 3,
            'brand_id' => null,
            'unit_id' => 1,
            'description' => 'Cotton t-shirt',
            'cost_price' => 25000,
            'selling_price' => 45000,
            'quantity' => 40,
            'reorder_level' => 8,
            'is_active' => true
        ]);
    }
}
