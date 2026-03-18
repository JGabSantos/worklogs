<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Reunião', 'sort_order' => 1],
            ['name' => 'Entrevista', 'sort_order' => 2],
            ['name' => 'Gestão', 'sort_order' => 3],
            ['name' => 'Auditorias', 'sort_order' => 4],
            ['name' => 'Formação', 'sort_order' => 5],
            ['name' => 'Certificações', 'sort_order' => 6],
            ['name' => 'Operacionais', 'sort_order' => 7],
            ['name' => 'Marketing', 'sort_order' => 8],
            ['name' => 'Desenvolvimento', 'sort_order' => 9],
            ['name' => 'Suporte', 'sort_order' => 10],
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
