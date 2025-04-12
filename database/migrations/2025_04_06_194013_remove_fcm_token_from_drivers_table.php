<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFcmTokenFromDriversTable extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('fcm_token')->nullable();
        });
    }
}

