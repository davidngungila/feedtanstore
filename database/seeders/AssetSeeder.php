<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            // Create a default user if none exists
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
            $this->command->info('Created default user: admin@example.com / password');
        }

        $assets = [
            [
                'name' => 'Office Laptop - Dell Latitude 7420',
                'type' => 'Electronics',
                'description' => 'High-performance laptop for accounting department',
                'purchase_date' => '2023-01-15',
                'depreciation_start_date' => '2023-02-01',
                'purchase_cost' => 2500000,
                'salvage_value' => 250000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
                'location' => 'Main Office - Accounting Dept',
                'status' => 'active',
                'serial_number' => 'DELL-LAT-7420-001',
                'manufacturer' => 'Dell',
                'model' => 'Latitude 7420',
                'warranty_expiry' => '2025-01-15',
                'assigned_to' => 'John Doe - Accountant',
                'maintenance_notes' => 'Annual maintenance scheduled for January 2024',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Delivery Truck - Toyota Hilux',
                'type' => 'Vehicle',
                'description' => 'Double cab pickup for deliveries',
                'purchase_date' => '2022-06-01',
                'depreciation_start_date' => '2022-07-01',
                'purchase_cost' => 45000000,
                'salvage_value' => 5000000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 10,
                'depreciation_method' => 'declining_balance',
                'location' => 'Warehouse - Parking Bay 3',
                'status' => 'active',
                'serial_number' => 'TOY-HILUX-2022-001',
                'manufacturer' => 'Toyota',
                'model' => 'Hilux 2.8L 4x4',
                'warranty_expiry' => '2024-06-01',
                'assigned_to' => 'Delivery Team',
                'maintenance_notes' => 'Oil change every 5000km. Last service: 15,000km',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Office Furniture - Executive Desk',
                'type' => 'Furniture',
                'description' => 'Solid wood executive desk for CEO office',
                'purchase_date' => '2021-03-10',
                'depreciation_start_date' => '2021-04-01',
                'purchase_cost' => 1500000,
                'salvage_value' => 150000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 7,
                'depreciation_method' => 'straight_line',
                'location' => 'CEO Office',
                'status' => 'active',
                'serial_number' => 'FURN-EXEC-001',
                'manufacturer' => 'Local Crafts',
                'model' => 'Executive Series',
                'warranty_expiry' => null,
                'assigned_to' => 'CEO Office',
                'maintenance_notes' => 'Polish monthly',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Production Machine - CNC Lathe',
                'type' => 'Machinery',
                'description' => 'Computer numerical control lathe for precision manufacturing',
                'purchase_date' => '2020-09-15',
                'depreciation_start_date' => '2020-10-01',
                'purchase_cost' => 85000000,
                'salvage_value' => 10000000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 15,
                'depreciation_method' => 'double_declining_balance',
                'location' => 'Factory Floor - Zone A',
                'status' => 'active',
                'serial_number' => 'CNC-LATHE-2020-001',
                'manufacturer' => 'Haas Automation',
                'model' => 'ST-20',
                'warranty_expiry' => '2022-09-15',
                'assigned_to' => 'Production Manager',
                'maintenance_notes' => 'Quarterly maintenance required. Last service: December 2023',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Office Printer - HP LaserJet',
                'type' => 'Electronics',
                'description' => 'Network laser printer for general office use',
                'purchase_date' => '2023-08-20',
                'depreciation_start_date' => '2023-09-01',
                'purchase_cost' => 850000,
                'salvage_value' => 85000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
                'location' => 'Main Office - Reception',
                'status' => 'active',
                'serial_number' => 'HP-LJ-PRO-001',
                'manufacturer' => 'HP',
                'model' => 'LaserJet Pro M404dn',
                'warranty_expiry' => '2024-08-20',
                'assigned_to' => 'Reception',
                'maintenance_notes' => 'Replace toner when low. Clean rollers monthly.',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Warehouse Forklift',
                'type' => 'Equipment',
                'description' => 'Electric forklift for warehouse operations',
                'purchase_date' => '2021-11-01',
                'depreciation_start_date' => '2021-12-01',
                'purchase_cost' => 12000000,
                'salvage_value' => 1200000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 8,
                'depreciation_method' => 'declining_balance',
                'location' => 'Warehouse',
                'status' => 'maintenance',
                'serial_number' => 'FLT-ELEC-2021-001',
                'manufacturer' => 'Toyota',
                'model' => '8FBEU15',
                'warranty_expiry' => '2023-11-01',
                'assigned_to' => 'Warehouse Team',
                'maintenance_notes' => 'Currently under maintenance - battery replacement scheduled',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Server Rack - Dell PowerEdge',
                'type' => 'Electronics',
                'description' => 'Server rack for hosting company applications',
                'purchase_date' => '2022-02-15',
                'depreciation_start_date' => '2022-03-01',
                'purchase_cost' => 15000000,
                'salvage_value' => 1500000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 7,
                'depreciation_method' => 'double_declining_balance',
                'location' => 'Server Room',
                'status' => 'active',
                'serial_number' => 'SRV-PE-2022-001',
                'manufacturer' => 'Dell',
                'model' => 'PowerEdge R740',
                'warranty_expiry' => '2024-02-15',
                'assigned_to' => 'IT Department',
                'maintenance_notes' => '24/7 monitoring. Backup daily.',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Air Conditioning Unit - Central',
                'type' => 'Equipment',
                'description' => 'Central AC system for main office building',
                'purchase_date' => '2020-05-01',
                'depreciation_start_date' => '2020-06-01',
                'purchase_cost' => 8000000,
                'salvage_value' => 800000,
                'accumulated_depreciation' => 0,
                'useful_life_years' => 10,
                'depreciation_method' => 'straight_line',
                'location' => 'Main Building',
                'status' => 'active',
                'serial_number' => 'AC-CENT-2020-001',
                'manufacturer' => 'Daikin',
                'model' => 'VRV System',
                'warranty_expiry' => '2022-05-01',
                'assigned_to' => 'Facilities Management',
                'maintenance_notes' => 'Quarterly filter cleaning. Annual compressor check.',
                'user_id' => $user->id,
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }

        $this->command->info('Sample assets seeded successfully.');
    }
}
