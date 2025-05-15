<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'status')) {
                $table->enum('status', ['disponible', 'en_mission', 'indisponible'])
                      ->default('disponible');
            }
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
