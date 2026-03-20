<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Meeting', 'sort_order' => 1],
            ['name' => 'Interview', 'sort_order' => 2],
            ['name' => 'Management', 'sort_order' => 3],
            ['name' => 'Audits', 'sort_order' => 4],
            ['name' => 'Training', 'sort_order' => 5],
            ['name' => 'Certifications', 'sort_order' => 6],
            ['name' => 'Operational', 'sort_order' => 7],
            ['name' => 'Marketing', 'sort_order' => 8],
            ['name' => 'Development', 'sort_order' => 9],
            ['name' => 'Support', 'sort_order' => 10],
        ];

        foreach ($items as $item) {
            ActivityType::updateOrCreate(
                ['name' => $item['name']],
                [
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
