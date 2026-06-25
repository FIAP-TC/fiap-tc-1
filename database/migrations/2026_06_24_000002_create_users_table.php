<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id');
            $table->string('username', 255);
            $table->string('password', 255);
            $table->dateTime('create_date');
            $table->dateTime('modified_date')->nullable();
            $table->tinyInteger('status');
            $table->integer('role_id');

            $table->primary(['id', 'role_id']);

            $table->index('role_id', 'fk_users_role_idx');

            $table->foreign('role_id', 'fk_users_role')
                ->references('id')
                ->on('role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
