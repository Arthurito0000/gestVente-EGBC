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
            'manage-quotes',
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
        $this->createSalesManagerRole(); // Nouveau rôle : Gestionnaire de ventes

        $this->command->info('✅ Rôles et permissions créés avec succès !');
        $this->command->info('');
        $this->command->info('🎭 RÔLES CRÉÉS :');
        $this->command->info('══════════════════════════════════════');
        $this->command->info('👑 Administrateur - Accès complet au système');
        $this->command->info('📦 Gérant de Stock - Gestion des stocks, produits et catégories UNIQUEMENT');
        $this->command->info('💰 Vendeur - Gestion des ventes, factures, création produits et catégories');
        $this->command->info('🏪 Gestionnaire de ventes - Factures et consultation stock UNIQUEMENT');
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
            
            // Gestion des produits (consultation + création)
            // 'view-products',
            // 'create-products',
            // 'edit-products',
            
            // Consultation du stock UNIQUEMENT (pas de mouvements)
            'view-stock',
            
            // Alertes de stock (important pour éviter de vendre des produits en rupture)
            'receive-stock-alerts',
            
            // Gestion des catégories (pour créer des catégories de produits)
            // 'manage-categories',
            
            // Gestion complète des ventes et factures
            'view-sales',
            'create-sales',
            'edit-sales',
            'export-sales',
            'manage-invoices',
            
            // Rapports de ventes uniquement
            'view-financial-reports',
        ];
        
        $seller->givePermissionTo($sellerPermissions);
        
        $this->command->info('💰 Rôle Vendeur créé avec ' . count($sellerPermissions) . ' permissions');
    }

    /**
     * Créer le rôle Gestionnaire de ventes
     * Accès UNIQUEMENT aux factures et consultation du stock
     * PAS d'accès aux devis, catégories, fichiers produit
     */
    private function createSalesManagerRole(): void
    {
        $salesManager = Role::firstOrCreate(['name' => 'gestionnaire_ventes']);
        
        $salesManagerPermissions = [
            // Dashboard basique
            'view-dashboard',
            
            // Consultation du stock UNIQUEMENT (pour vérifier disponibilité)
            'view-stock',
            
            // Gestion complète des factures
            'view-sales',
            'create-sales',
            'edit-sales',
            'manage-invoices',
            'export-sales',
            
            // Rapports de ventes uniquement
            'view-financial-reports',
        ];
        
        $salesManager->givePermissionTo($salesManagerPermissions);
        
        $this->command->info('🏪 Rôle Gestionnaire de ventes créé avec ' . count($salesManagerPermissions) . ' permissions');
    }
}
