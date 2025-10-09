<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearTablesExceptUsers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:clear-except-users {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Vider toutes les tables sauf celle des users et tables système';

    /**
     * Tables à préserver (ne pas vider)
     */
    protected $preservedTables = [
        'users',
        'migrations',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'personal_access_tokens',
        // Tables Spatie Permission
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Êtes-vous sûr de vouloir vider toutes les tables sauf users et système ?')) {
                $this->info('Opération annulée.');
                return 0;
            }
        }

        $this->info('🔍 Récupération de la liste des tables...');
        
        // Désactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Récupérer toutes les tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableColumn = "Tables_in_{$databaseName}";
            
            $clearedTables = [];
            $skippedTables = [];
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                // Vérifier si la table doit être préservée
                if (in_array($tableName, $this->preservedTables)) {
                    $skippedTables[] = $tableName;
                    $this->line("⏭️  Ignoré: <fg=yellow>{$tableName}</>");
                    continue;
                }
                
                // Vider la table
                try {
                    DB::table($tableName)->truncate();
                    $clearedTables[] = $tableName;
                    $this->line("✅ Vidé: <fg=green>{$tableName}</>");
                } catch (\Exception $e) {
                    $this->error("❌ Erreur sur {$tableName}: " . $e->getMessage());
                }
            }
            
        } finally {
            // Réactiver les contraintes de clés étrangères
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        // Résumé
        $this->newLine();
        $this->info('📊 Résumé de l\'opération:');
        $this->table(
            ['Type', 'Nombre', 'Tables'],
            [
                ['Vidées', count($clearedTables), implode(', ', $clearedTables)],
                ['Préservées', count($skippedTables), implode(', ', $skippedTables)]
            ]
        );
        
        $this->info('✅ Opération terminée avec succès !');
        
        return 0;
    }
}
