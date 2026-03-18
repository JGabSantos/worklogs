<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = Profile::where('code', 'employee')->first();
        $admin = Profile::where('code', 'admin')->first();
        $manager = Profile::where('code', 'manager')->first();

        User::updateOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name' => 'Funcionário Teste',
                'password' => Hash::make('password'),
                'profile_id' => $employee?->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Teste',
                'password' => Hash::make('password'),
                'profile_id' => $admin?->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Gestor Teste',
                'password' => Hash::make('password'),
                'profile_id' => $manager?->id,
                'is_active' => true,
            ]
        );
    }
}
