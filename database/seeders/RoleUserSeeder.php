<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Storekeeper user
        $storekeeper = User::firstOrCreate(
            ['email' => 'storekeeper@feedtan.co.tz'],
            [
                'name' => 'Storekeeper User',
                'password' => Hash::make('password'),
                'role' => 'storekeeper',
                'phone' => '+255 123 456 788',
            ]
        );

        // Create Marketing Officer user
        $marketingOfficer = User::firstOrCreate(
            ['email' => 'marketing@feedtan.co.tz'],
            [
                'name' => 'Marketing Officer User',
                'password' => Hash::make('password'),
                'role' => 'marketing_officer',
                'phone' => '+255 123 456 789',
            ]
        );

        $this->command->info('Storekeeper user created: storekeeper@feedtan.co.tz / password');
        $this->command->info('Marketing Officer user created: marketing@feedtan.co.tz / password');
    }
}
