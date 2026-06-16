<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'cashier@feedtan.com'],
            [
                'name' => 'Feedtan Cashier',
                'email' => 'cashier@feedtan.com',
                'password' => bcrypt('password'),
                'role' => 'cashier'
            ]
        );
    }
}
