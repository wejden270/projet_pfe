<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            // Client qui fait la demande
            $table->foreignId('client_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Chauffeur assigné (nullable au départ)
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('drivers')
                  ->onDelete('set null');
            // Position du client
            $table->decimal('client_latitude', 10, 7);
            $table->decimal('client_longitude', 10, 7);
            // Statut de la demande
            $table->enum('status', ['en attente', 'accepté', 'refusé'])
                  ->default('en attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
}
