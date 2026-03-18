<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            ['code' => 'employee', 'name' => 'Funcionário'],
            ['code' => 'admin', 'name' => 'Admin'],
            ['code' => 'manager', 'name' => 'Gestor'],
        ];

        foreach ($profiles as $profile) {
            Profile::updateOrCreate(
                ['code' => $profile['code']],
                [
                    'name' => $profile['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
