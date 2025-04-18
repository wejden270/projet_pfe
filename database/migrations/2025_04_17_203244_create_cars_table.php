<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // lien avec le chauffeur
            $table->string('make'); // marque de la voiture (ex: Toyota)
            $table->string('model'); // modèle (ex: Corolla)
            $table->year('year'); // année de fabrication
            $table->string('license_plate')->unique(); // plaque d'immatriculation
            $table->string('current_location')->nullable(); // position actuelle (peut être géolocalisée plus tard)
            $table->enum('status', ['disponible', 'en_mission', 'hors_service'])->default('disponible'); // état de la voiture
            $table->timestamps();

            // Clé étrangère vers la table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
}
