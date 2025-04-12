<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRoleFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    // Vérifier si la colonne 'role' existe avant de tenter de la supprimer
    if (Schema::hasColumn('users', 'role')) {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['client', 'chauffeur', 'admin'])->default('client'); // ✅ Rétablissement du champ si besoin
        });
    }
}
