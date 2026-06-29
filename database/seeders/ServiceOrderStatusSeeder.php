<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Popula os status possíveis de uma Ordem de Serviço.
 *
 * A ordem dos IDs é significativa: o ServiceOrderService usa o ID 1
 * (Recebida) como status inicial obrigatório de toda nova ordem.
 * Ao adicionar novos status, mantenha IDs sequenciais e documente aqui.
 */
class ServiceOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'Recebida'],
            ['id' => 2, 'name' => 'Em diagnóstico'],
            ['id' => 3, 'name' => 'Aguardando aprovação'],
            ['id' => 4, 'name' => 'Em execução'],
            ['id' => 5, 'name' => 'Finalizada'],
            ['id' => 6, 'name' => 'Entregue'],
        ];

        foreach ($statuses as $status) {
            DB::table('service_order_status')->insertOrIgnore([
                'id'          => $status['id'],
                'name'        => $status['name'],
                'status'      => 'ativo',
                'create_date' => now(),
            ]);
        }
    }
}
