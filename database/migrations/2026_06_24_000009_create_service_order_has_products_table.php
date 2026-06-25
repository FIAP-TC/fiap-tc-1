<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderHasProductsTable extends Migration
{
    public function up()
    {
        Schema::create('service_order_has_products', function (Blueprint $table) {
            $table->integer('service_order_id');
            $table->integer('service_order_customer_id');
            $table->integer('service_order_users_id');
            $table->integer('service_order_users_role_id');
            $table->integer('products_id');
            $table->decimal('charged_value', 10, 2);

            $table->primary(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id', 'products_id'],
                'so_has_products_pk'
            );

            $table->index('products_id', 'fk_service_order_has_products_products1_idx');
            $table->index(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id'],
                'fk_service_order_has_products_service_order1_idx'
            );

            $table->foreign(['service_order_id', 'service_order_users_id', 'service_order_users_role_id'], 'fk_service_order_has_products_service_order1')
                ->references(['id', 'users_id', 'users_role_id'])
                ->on('service_order');

            $table->foreign('products_id', 'fk_service_order_has_products_products1')
                ->references('id')
                ->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_has_products');
    }
}
