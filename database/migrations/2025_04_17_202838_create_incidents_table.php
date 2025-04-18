<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            // rendre driver_id nullable pour respecter la contrainte ON DELETE SET NULL
            $table->foreignId('driver_id')
                  ->nullable() // Ajouter nullable ici
                  ->constrained('drivers')
                  ->onDelete('set null');
            $table->text('description');
            $table->dateTime('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
}
