<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'João Santos',
                'username' => 'admin',
                'email' => 'departamento.tecnologia@senilife.pt',
                'password' => Hash::make('password'),
                'profile_code' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Pedro Santos',
                'username' => 'pedro_santos',
                'email' => 'pedro.santos@senilife.pt',
                'password' => Hash::make('!p28Xi48E\%:'),
                'profile_code' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Cristina Santos',
                'username' => 'cristina_santos',
                'email' => 'cristina.santos@senilife.pt',
                'password' => Hash::make('4C4s-~.1"wsA'),
                'profile_code' => 'manager',
                'is_active' => true,
            ],
            [
                'name' => 'Flávia Espada',
                'username' => 'flavia_espada',
                'email' => 'flavia.espada@senilife.pt',
                'password' => Hash::make('?U7KBl56Cs[3'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Joana Silva',
                'username' => 'joana_silva',
                'email' => 'joana.silva@senilife.pt',
                'password' => Hash::make('Yi7/9nC60~OR'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Ana Monteiro',
                'username' => 'ana_monteiro',
                'email' => 'formacao@senilife.pt',
                'password' => Hash::make('9Nj`9Kt2kcQ-'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Deusa Carina',
                'username' => 'deusa_carina',
                'email' => 'departamento.medico@senilife.pt',
                'password' => Hash::make('Wl0yPaS5%6<:'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Maria João',
                'username' => 'maria_joao',
                'email' => 'departamento.servicos@senilife.pt',
                'password' => Hash::make('89&NjN52VRKa'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Erica Amorin',
                'username' => 'erica_amorin',
                'email' => 'ims@senilife.pt',
                'password' => Hash::make('1X.0@Jy7}M-S'),
                'profile_code' => 'employee',
                'is_active' => true,
            ],
            [
                'name' => 'Inês Azevedo',
                'username' => 'ines_azevedo',
                'email' => 'inespaazevedo@gmail.com',
                'password' => Hash::make('3Vr2<8\_9VBe'),
                'profile_code' => 'manager',
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employeeData) {
            $profile = Profile::where('code', $employeeData['profile_code'])->first();

            User::updateOrCreate(
                ['email' => $employeeData['email']],
                [
                    'name' => $employeeData['name'],
                    'username' => $employeeData['username'],
                    'password' => $employeeData['password'],
                    'profile_id' => $profile ? $profile->id : null,
                    'is_active' => $employeeData['is_active'],
                ]
            );
        }
    }
}
