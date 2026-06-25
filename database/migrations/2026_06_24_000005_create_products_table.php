<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->enum('type', ['PECAS', 'INSUMOS']);
            $table->decimal('value', 10, 2);
            $table->integer('quantity');
            $table->tinyInteger('status');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
