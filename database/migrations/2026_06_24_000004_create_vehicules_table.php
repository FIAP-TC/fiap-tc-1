<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('plate', 25);
            $table->string('model', 255);
            $table->string('brand', 255);
            $table->integer('years');
            $table->boolean('status')->default(true);
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
            $table->integer('customer_id')->unsigned();

            $table->index('customer_id', 'fk_vehicules_customer1_idx');

            $table->foreign('customer_id', 'fk_vehicules_customer1')
                ->references('id')
                ->on('customer');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicules');
    }
}
