<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->decimal('value', 10, 2);
            $table->tinyInteger('status');
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
