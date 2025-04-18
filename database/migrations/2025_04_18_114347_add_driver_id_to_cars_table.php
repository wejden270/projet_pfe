<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverIdToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('cars', function (Blueprint $table) {
        $table->unsignedBigInteger('driver_id')->after('id');

        // Si tu veux lier avec la table drivers :
        $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('cars', function (Blueprint $table) {
        $table->dropForeign(['driver_id']);
        $table->dropColumn('driver_id');
    });
}
}
