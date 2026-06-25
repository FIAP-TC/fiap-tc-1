<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderHasServicesTable extends Migration
{
    public function up()
    {
        Schema::create('service_order_has_services', function (Blueprint $table) {
            $table->integer('service_order_id');
            $table->integer('service_order_customer_id');
            $table->integer('service_order_users_id');
            $table->integer('service_order_users_role_id');
            $table->integer('services_id');
            $table->decimal('charged_value', 10, 2);

            $table->primary(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id', 'services_id'],
                'so_has_services_pk'
            );

            $table->index('services_id', 'fk_service_order_has_services_services1_idx');
            $table->index(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id'],
                'fk_service_order_has_services_service_order1_idx'
            );

            $table->foreign(['service_order_id', 'service_order_users_id', 'service_order_users_role_id'], 'fk_service_order_has_services_service_order1')
                ->references(['id', 'users_id', 'users_role_id'])
                ->on('service_order');

            $table->foreign('services_id', 'fk_service_order_has_services_services1')
                ->references('id')
                ->on('services');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_has_services');
    }
}
