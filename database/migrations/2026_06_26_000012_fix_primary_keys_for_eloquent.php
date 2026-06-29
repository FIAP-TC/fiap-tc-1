<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige PKs de role e users para compatibilidade com Eloquent.
 *
 * O schema original (gerado pelo MySQL Workbench) usa PKs compostas em users/role
 * que são incompatíveis com o Eloquent, que espera PK simples e auto-incrementada.
 *
 * Cadeia de dependências:
 *   users → role
 *   service_order → users  (FK recriada após mudança de PK)
 */
class FixPrimaryKeysForEloquent extends Migration
{
    public function up(): void
    {
        // 1. Dropar FK de service_order → users (referencia PK composta antiga)
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_users1`');

        // 2. Dropar FK de users → role
        DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_role`');

        // 3. role: PK simples com AUTO_INCREMENT
        DB::statement('ALTER TABLE `role` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)');

        // 4. users: trocar PK composta (id, role_id) por PK simples (id) com AUTO_INCREMENT
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)');

        // 5. Recriar FK de users → role
        DB::statement('ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)');

        // 6. Recriar FK de service_order → users (agora referencia apenas users.id)
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_users1`');
        DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_role`');

        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL, ADD PRIMARY KEY (`id`, `role_id`)');
        DB::statement('ALTER TABLE `role` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL, ADD PRIMARY KEY (`id`)');

        DB::statement('ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)');
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_users1` FOREIGN KEY (`users_id`, `users_role_id`) REFERENCES `users` (`id`, `role_id`)');
    }
}
