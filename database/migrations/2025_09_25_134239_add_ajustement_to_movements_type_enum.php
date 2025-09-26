<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'ENUM pour ajouter AJUSTEMENT
        DB::statement("ALTER TABLE movements MODIFY COLUMN type ENUM('ENTREE', 'SORTIE', 'AJUSTEMENT')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ENUM original
        DB::statement("ALTER TABLE movements MODIFY COLUMN type ENUM('ENTREE', 'SORTIE')");
    }
};
