<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderHasServicesTable extends Migration
{
    public function up(): void
    {
        Schema::create('service_order_has_services', function (Blueprint $table) {
            $table->unsignedInteger('service_order_id');
            // Coluna vestigial do schema original — sem FK ativa, mantida para compatibilidade
            $table->integer('service_order_customer_id')->default(0);
            $table->integer('service_order_users_id');
            $table->integer('service_order_users_role_id');
            $table->unsignedInteger('services_id');
            $table->decimal('charged_value', 10, 2);

            $table->primary(
                ['service_order_id', 'service_order_customer_id', 'service_order_users_id', 'service_order_users_role_id', 'services_id'],
                'so_has_services_pk'
            );

            $table->index('services_id', 'fk_service_order_has_services_services1_idx');

            $table->foreign('service_order_id', 'fk_service_order_has_services_service_order1')
                ->references('id')
                ->on('service_order');

            $table->foreign('services_id', 'fk_service_order_has_services_services1')
                ->references('id')
                ->on('services');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_order_has_services');
    }
}
