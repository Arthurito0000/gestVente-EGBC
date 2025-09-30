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
        // Ajouter une contrainte CHECK pour empêcher les stocks négatifs
        DB::statement('ALTER TABLE stocks ADD CONSTRAINT check_quantite_positive CHECK (quantite >= 0)');
        DB::statement('ALTER TABLE stocks ADD CONSTRAINT check_seuil_positive CHECK (seuil >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les contraintes
        DB::statement('ALTER TABLE stocks DROP CONSTRAINT IF EXISTS check_quantite_positive');
        DB::statement('ALTER TABLE stocks DROP CONSTRAINT IF EXISTS check_seuil_positive');
    }
};
