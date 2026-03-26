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
            'time-entries.show.own' => 'Ver Registros Próprios',
            'time-entries.create.own' => 'Criar Registros Próprios',
            'time-entries.update.own' => 'Editar Registros Próprios',
            'time-entries.delete.own' => 'Excluir Registros Próprios',

            'time-entries.show.all' => 'Ver Registros de Todos',
            'time-entries.create.all' => 'Criar Registros de Todos',
            'time-entries.update.all' => 'Editar Registros de Todos',
            'time-entries.delete.all' => 'Excluir Registros de Todos',

            'users.show.own' => 'Ver Usuário Próprio',
            'users.update.own' => 'Editar Usuário Próprio',

            'users.show.all' => 'Ver Usuários',
            'users.update.all' => 'Editar Usuários',
            'users.delete.all' => 'Excluir Usuários',

            'profiles.show.all' => 'Ver Perfis',
            'profiles.update.all' => 'Editar Perfis',
            'profiles.delete.all' => 'Excluir Perfis',

            'permissions.show.all' => 'Ver Permissões',
            'permissions.update.all' => 'Editar Permissões',
            'permissions.delete.all' => 'Excluir Permissões',

            'clients.show.all' => 'Ver Clientes',
            'clients.create.all' => 'Criar Clientes',
            'clients.update.all' => 'Editar Clientes',
            'clients.delete.all' => 'Excluir Clientes',

            'activity_types.show.all' => 'Ver Tipos de Atividade',
            'activity_types.create.all' => 'Criar Tipos de Atividade',
            'activity_types.update.all' => 'Editar Tipos de Atividade',
            'activity_types.delete.all' => 'Excluir Tipos de Atividade',
        ];

        foreach ($permissions as $code => $name) {
            Permission::updateOrCreate(
                ['code' => $code],
                ['name' => $name]
            );
        }
    }
}
