<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Senilife'],
            ['name' => 'IMS'],
            ['name' => 'Sobreiras Valley'],
        ];

        foreach ($items as $item) {
            Client::updateOrCreate(
                ['name' => $item['name']],
                [
                    'is_active' => true,
                ]
            );
        }
    }
}
