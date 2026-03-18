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
                'time-entries.read.own',
                'time-entries.create.own',
                'time-entries.update.own',
                'time-entries.delete.own',
            ],

            'admin' => [
                'users.manage',
                'clients.manage',
                'activity-types.manage',
                'time-entries.read.all',
            ],

            'manager' => Permission::pluck('code')->toArray(), // tudo
        ];

        foreach ($map as $profileCode => $permissions) {

            $profile = Profile::where('code', $profileCode)->first();

            $permissionIds = Permission::whereIn('code', $permissions)->pluck('id');

            $profile->permissions()->sync($permissionIds);
        }
    }
}
