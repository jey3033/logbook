<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToDivision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            //
            $table->string('uuid')->unique()->nullable();
            $table->string('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            //
            if (Schema::hasColumns('divisions',['uuid', 'active'])) {
                $table->dropColumn('uuid');
                $table->dropColumn('active');
            } 
            if (Schema::hasColumn('divisions', 'uuid')) {
                $table->dropColumn('uuid');
            } 
            if (Schema::hasColumn('divisions', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
}
