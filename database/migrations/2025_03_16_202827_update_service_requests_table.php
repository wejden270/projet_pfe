<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('service_requests', function (Blueprint $table) {
        if (!Schema::hasColumn('service_requests', 'driver_id')) {
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
        }
        if (!Schema::hasColumn('service_requests', 'client_latitude')) {
            $table->decimal('client_latitude', 10, 7)->after('driver_id');
        }
        if (!Schema::hasColumn('service_requests', 'client_longitude')) {
            $table->decimal('client_longitude', 10, 7)->after('client_latitude');
        }
        if (!Schema::hasColumn('service_requests', 'status')) {
            $table->enum('status', ['en attente', 'accepté', 'refusé'])->default('en attente')->after('client_longitude');
        }
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Supprime la contrainte de clé étrangère avant de supprimer la colonne
            $table->dropForeign(['driver_id']); // Supprime la clé étrangère
            $table->dropColumn(['driver_id', 'client_latitude', 'client_longitude', 'status']);
        });
    }
}
