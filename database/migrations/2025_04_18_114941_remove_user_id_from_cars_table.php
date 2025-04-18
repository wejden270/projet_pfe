<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdFromCarsTable extends Migration
{
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère d'abord
            $table->dropForeign(['user_id']);

            // Ensuite supprimer la colonne
            $table->dropColumn('user_id');
        });
    }

    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            // Recréer la colonne (en cas de rollback)
            $table->unsignedBigInteger('user_id')->nullable();

            // Restaurer la clé étrangère (si besoin)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}

