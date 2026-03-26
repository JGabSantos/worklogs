<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfilePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $map = [
            'employee' => [
                'time-entries.show.own',
                'time-entries.create.own',
                'time-entries.update.own',
                'time-entries.delete.own',

                'users.show.own',
                'users.update.own',

                'clients.show.all',
                'activity_types.show.all',
            ],

            'admin' => [],

            'manager' => [
                'time-entries.show.own',
                'time-entries.create.own',
                'time-entries.update.own',
                'time-entries.delete.own',

                'time-entries.show.all',

                'users.show.own',
                'users.show.all',

                'clients.show.all',
                'activity_types.show.all',
            ]
        ];

        foreach ($map as $profileCode => $permissionCodes) {
            $profile = Profile::where('code', $profileCode)->first();

            if (! $profile) {
                continue;
            }

            $permissionCodes = $profileCode === 'admin'
                ? Permission::pluck('code')->toArray()
                : $permissionCodes;

            $permissionIds = Permission::whereIn('code', $permissionCodes)->pluck('id');

            $profile->permissions()->sync($permissionIds);
        }
    }
}
