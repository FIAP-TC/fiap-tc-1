<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderTable extends Migration
{
    public function up(): void
    {
        Schema::create('service_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->integer('users_role_id');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
            $table->decimal('order_value', 10, 2);
            $table->decimal('time_average', 10, 2)->nullable();
            // status como boolean para soft-delete (padrão do projeto)
            // O histórico real de status fica em service_order_has_service_order_status
            $table->boolean('status')->default(true);
            $table->unsignedInteger('vehicules_id');

            $table->index(['users_id', 'users_role_id'], 'fk_service_order_users1_idx');
            $table->index('vehicules_id', 'fk_service_order_vehicules1_idx');

            // FK composta referencia a PK composta original de users (id, role_id).
            // A migration 000012 dropa e recria como FK simples após normalizar users.
            $table->foreign(['users_id', 'users_role_id'], 'fk_service_order_users1')
                ->references(['id', 'role_id'])
                ->on('users');

            $table->foreign('vehicules_id', 'fk_service_order_vehicules1')
                ->references('id')
                ->on('vehicules');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order');
    }
}
