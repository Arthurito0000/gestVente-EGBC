<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Création des rôles et permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des permissions
        $permissions = [
            // Permissions Dashboard
            'view-dashboard',
            'view-analytics',
            
            // Permissions Produits
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'export-products',
            
            // Permissions Stock
            'view-stock',
            'manage-stock',
            'view-movements',
            'create-movements',
            'export-stock',
            'receive-stock-alerts',
            
            // Permissions Ventes
            'view-sales',
            'create-sales',
            'edit-sales',
            'delete-sales',
            'export-sales',
            'manage-invoices',
            
            // Permissions Administration
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'view-system-logs',
            'manage-categories',
            'manage-settings',
            
            // Permissions Rapports
            'view-reports',
            'export-reports',
            'view-financial-reports',
        ];

        $this->command->info('📝 Création de ' . count($permissions) . ' permissions...');
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Création des rôles avec leurs permissions
        $this->createAdministratorRole();
        $this->createStockManagerRole();
        $this->createSellerRole();

        $this->command->info('✅ Rôles et permissions créés avec succès !');
        $this->command->info('');
        $this->command->info('🎭 RÔLES CRÉÉS :');
        $this->command->info('══════════════════════════════════════');
        $this->command->info('👑 Administrateur - Accès complet au système');
        $this->command->info('📦 Gérant de Stock - Gestion des stocks, produits et catégories UNIQUEMENT');
        $this->command->info('💰 Vendeur - Gestion des ventes, factures et consultation stocks');
    }

    /**
     * Créer le rôle Administrateur avec toutes les permissions
     */
    private function createAdministratorRole(): void
    {
        $admin = Role::firstOrCreate(['name' => 'administrateur']);
        
        // L'administrateur a toutes les permissions
        $admin->givePermissionTo(Permission::all());
        
        $this->command->info('👑 Rôle Administrateur créé avec ' . $admin->permissions->count() . ' permissions');
    }

    /**
     * Créer le rôle Gérant de Stock
     */
    private function createStockManagerRole(): void
    {
        $stockManager = Role::firstOrCreate(['name' => 'gerant_stock']);
        
        $stockManagerPermissions = [
            // Dashboard et analytics
            'view-dashboard',
            'view-analytics',
            
            // Gestion complète des produits
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'export-products',
            
            // Gestion complète du stock
            'view-stock',
            'manage-stock',
            'view-movements',
            'create-movements',
            'export-stock',
            'receive-stock-alerts',
            
            // Gestion des catégories
            'manage-categories',
            
            // Rapports liés au stock uniquement
            'view-reports',
            'export-reports',
        ];
        
        $stockManager->givePermissionTo($stockManagerPermissions);
        
        $this->command->info('📦 Rôle Gérant de Stock créé avec ' . count($stockManagerPermissions) . ' permissions');
    }

    /**
     * Créer le rôle Vendeur
     */
    private function createSellerRole(): void
    {
        $seller = Role::firstOrCreate(['name' => 'vendeur']);
        
        $sellerPermissions = [
            // Dashboard basique
            'view-dashboard',
            
            // Consultation des produits UNIQUEMENT (pas d'export)
            'view-products',
            
            // Consultation du stock UNIQUEMENT (pas d'export)
            'view-stock',
            'view-movements',
            
            // Gestion complète des ventes
            'view-sales',
            'create-sales',
            'edit-sales',
            'export-sales',
            'manage-invoices',
            
            // Rapports de ventes uniquement (pas de stock)
            'view-financial-reports',
        ];
        
        $seller->givePermissionTo($sellerPermissions);
        
        $this->command->info('💰 Rôle Vendeur créé avec ' . count($sellerPermissions) . ' permissions');
    }
}
