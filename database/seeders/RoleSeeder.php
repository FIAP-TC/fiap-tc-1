<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\User\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeder de perfis de acesso.
 *
 * Os IDs são fixos e correspondem às constantes em RoleEntity:
 *   ID 1 = Administrador
 *   ID 2 = Gerente
 *   ID 3 = Mecânico
 *
 * Os middlewares de autorização usam esses IDs para verificar permissões.
 * Alterar estes IDs requer atualizar as constantes em RoleEntity.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Administrador', 'status' => 'ativo', 'create_date' => now()],
            ['id' => 2, 'name' => 'Gerente',        'status' => 'ativo', 'create_date' => now()],
            ['id' => 3, 'name' => 'Mecânico',       'status' => 'ativo', 'create_date' => now()],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
