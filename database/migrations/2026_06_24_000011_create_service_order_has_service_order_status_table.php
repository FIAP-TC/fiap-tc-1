<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderHasServiceOrderStatusTable extends Migration
{
    public function up()
    {
        Schema::create('service_order_has_service_order_status', function (Blueprint $table) {
            $table->integer('service_order_id');
            $table->integer('service_order_customer_id');
            $table->integer('service_order_users_id');
            $table->integer('service_order_users_role_id');
            $table->integer('service_order_status_id');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();

            $table->primary(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id', 'service_order_status_id'],
                'so_has_status_pk'
            );

            $table->index('service_order_status_id', 'fk_service_order_has_service_order_status_service_order_sta_idx');
            $table->index(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id'],
                'fk_service_order_has_service_order_status_service_order1_idx'
            );

            $table->foreign(['service_order_id', 'service_order_users_id', 'service_order_users_role_id'], 'fk_service_order_has_service_order_status_service_order1')
                ->references(['id', 'users_id', 'users_role_id'])
                ->on('service_order');

            $table->foreign('service_order_status_id', 'fk_service_order_has_service_order_status_service_order_status1')
                ->references('id')
                ->on('service_order_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_has_service_order_status');
    }
}
