<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->enum('identification', ['CPF', 'CNPJ']);
            $table->bigInteger('identification_number');
            $table->string('email', 255);
            $table->tinyInteger('status');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer');
    }
}
