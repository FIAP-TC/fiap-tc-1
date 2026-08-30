<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultToCreatedAtInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE customer MODIFY create_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**clear
     * Run the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE customer MODIFY create_date DATETIME NOT NULL');
    }
}
