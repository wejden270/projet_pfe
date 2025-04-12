<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id(); // Colonne d'identifiant auto-incrémenté
            $table->foreignId('car_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table cars
            $table->decimal('latitude', 10, 7); // Latitude avec précision
            $table->decimal('longitude', 10, 7); // Longitude avec précision
            $table->timestamp('timestamp')->useCurrent(); // Timestamp automatiquement défini à la date et heure actuelles
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at pour la gestion des timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations'); // Supprime la table si elle existe
    }
}


