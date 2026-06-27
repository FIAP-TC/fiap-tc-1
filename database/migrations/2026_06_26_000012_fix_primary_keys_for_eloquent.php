<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix migrations para compatibilidade com Eloquent.
 *
 * O schema original (gerado pelo MySQL Workbench) usa PKs compostas em users/role
 * que são incompatíveis com o Eloquent, que espera PK simples e auto-incrementada.
 *
 * SET FOREIGN_KEY_CHECKS=0 não funciona via PDO do Laravel porque a variável de
 * sessão não é propagada entre statement() calls. Por isso dropamos a cadeia de
 * FKs manualmente antes de alterar as PKs.
 *
 * Cadeia de dependências:
 *   service_order_has_* → service_order → users → role
 */
class FixPrimaryKeysForEloquent extends Migration
{
    public function up(): void
    {
        // 1. Dropar FKs das tabelas filhas de service_order (mais externas primeiro)
        DB::statement('ALTER TABLE `service_order_has_products` DROP FOREIGN KEY `fk_service_order_has_products_service_order1`');
        DB::statement('ALTER TABLE `service_order_has_services` DROP FOREIGN KEY `fk_service_order_has_services_service_order1`');
        DB::statement('ALTER TABLE `service_order_has_service_order_status` DROP FOREIGN KEY `fk_service_order_has_service_order_status_service_order1`');

        // 2. Dropar FKs de service_order → users e → vehicules
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_users1`');
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_vehicules1`');

        // 3. Dropar FK de users → role
        DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_role`');

        // 4. Alterar role: PK simples com AUTO_INCREMENT
        DB::statement('ALTER TABLE `role` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)');

        // 5. Alterar users: trocar PK composta (id, role_id) por PK simples (id) com AUTO_INCREMENT
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)');

        // 6. Recriar FK de users → role
        DB::statement('ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)');

        // 7. Recriar FK de service_order → users (referencia apenas users.id — PK simples)
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)');

        // 8. Recriar FK de service_order → vehicules (referencia apenas vehicules.id — PK simples)
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_vehicules1` FOREIGN KEY (`vehicules_id`) REFERENCES `vehicules` (`id`)');

        // 9. Recriar FKs das tabelas filhas → service_order
        DB::statement('ALTER TABLE `service_order_has_products` ADD CONSTRAINT `fk_service_order_has_products_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
        DB::statement('ALTER TABLE `service_order_has_services` ADD CONSTRAINT `fk_service_order_has_services_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
        DB::statement('ALTER TABLE `service_order_has_service_order_status` ADD CONSTRAINT `fk_service_order_has_service_order_status_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `service_order_has_products` DROP FOREIGN KEY `fk_service_order_has_products_service_order1`');
        DB::statement('ALTER TABLE `service_order_has_services` DROP FOREIGN KEY `fk_service_order_has_services_service_order1`');
        DB::statement('ALTER TABLE `service_order_has_service_order_status` DROP FOREIGN KEY `fk_service_order_has_service_order_status_service_order1`');
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_users1`');
        DB::statement('ALTER TABLE `service_order` DROP FOREIGN KEY `fk_service_order_vehicules1`');
        DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_role`');

        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL, ADD PRIMARY KEY (`id`, `role_id`)');
        DB::statement('ALTER TABLE `role` DROP PRIMARY KEY, MODIFY `id` INT NOT NULL, ADD PRIMARY KEY (`id`)');

        DB::statement('ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)');
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_users1` FOREIGN KEY (`users_id`, `users_role_id`) REFERENCES `users` (`id`, `role_id`)');
        DB::statement('ALTER TABLE `service_order` ADD CONSTRAINT `fk_service_order_vehicules1` FOREIGN KEY (`vehicules_id`) REFERENCES `vehicules` (`id`)');
        DB::statement('ALTER TABLE `service_order_has_products` ADD CONSTRAINT `fk_service_order_has_products_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
        DB::statement('ALTER TABLE `service_order_has_services` ADD CONSTRAINT `fk_service_order_has_services_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
        DB::statement('ALTER TABLE `service_order_has_service_order_status` ADD CONSTRAINT `fk_service_order_has_service_order_status_service_order1` FOREIGN KEY (`service_order_id`, `service_order_users_id`, `service_order_users_role_id`) REFERENCES `service_order` (`id`, `users_id`, `users_role_id`)');
    }
}
