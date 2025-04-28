<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAnnuleeToStatusEnum extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE demandes MODIFY COLUMN status ENUM('en_attente', 'acceptee', 'refusee', 'annulee')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE demandes MODIFY COLUMN status ENUM('en_attente', 'acceptee', 'refusee')");
    }
}
