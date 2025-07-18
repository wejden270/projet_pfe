<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFcmTokenToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
}
