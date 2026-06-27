<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderTable extends Migration
{
    public function up()
    {
        Schema::create('service_order', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('users_id');
            $table->integer('users_role_id');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
            $table->decimal('order_value', 10, 2);
            $table->decimal('time_average', 10, 2)->nullable();
            $table->enum('status', ['APROVADO', 'PENDENTE', 'NEGADO']);
            $table->integer('vehicules_id')->unsigned();

            $table->primary(['id', 'users_id', 'users_role_id', 'vehicules_id'], 'service_order_pk');

            // unique index required so child tables can FK-reference this subset of columns
            $table->unique(['id', 'users_id', 'users_role_id'], 'so_users_unique');

            $table->index(['users_id', 'users_role_id'], 'fk_service_order_users1_idx');
            $table->index('vehicules_id', 'fk_service_order_vehicules1_idx');

            $table->foreign(['users_id', 'users_role_id'], 'fk_service_order_users1')
                ->references(['id', 'role_id'])
                ->on('users');

            $table->foreign('vehicules_id', 'fk_service_order_vehicules1')
                ->references('id')
                ->on('vehicules');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order');
    }
}
