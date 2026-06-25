<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->dateTime('create_date')->nullable();
            $table->dateTime('modified_date')->nullable();
            $table->string('status', 45);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role');
    }
}
