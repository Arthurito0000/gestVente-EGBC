<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearTablesSeeder extends Seeder
{
    /**
     * Vider toutes les tables sauf users et categories
     */
    public function run(): void
    {
        $this->command->info('🗑️ Suppression des données...');
        
        // Désactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider les tables
        DB::table('sales')->truncate();
        $this->command->info('✅ Table sales vidée');
        
        DB::table('movements')->truncate();
        $this->command->info('✅ Table movements vidée');
        
        DB::table('stocks')->truncate();
        $this->command->info('✅ Table stocks vidée');
        
        DB::table('products')->truncate();
        $this->command->info('✅ Table products vidée');
        
        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('');
        $this->command->info('✅ Toutes les tables ont été vidées sauf users et categories !');
    }
}
