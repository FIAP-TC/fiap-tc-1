<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultToCreateDateInServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE services MODIFY create_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE services MODIFY create_date DATETIME NOT NULL');
    }
}
