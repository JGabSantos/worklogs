<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.manage',

            'clients.manage',
            'activity-types.manage',

            'time-entries.read.own',
            'time-entries.create.own',
            'time-entries.update.own',
            'time-entries.delete.own',

            'time-entries.read.all',
            'time-entries.create.all',
            'time-entries.update.all',
            'time-entries.delete.all',
        ];

        foreach ($permissions as $code) {
            Permission::updateOrCreate(
                ['code' => $code],
                ['name' => $code]
            );
        }
    }
}
