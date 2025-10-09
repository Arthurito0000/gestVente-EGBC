<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Création de 4 utilisateurs de test...');

        // Utilisateur 1 : Admin principal
        $admin = User::updateOrCreate(
            ['email' => 'lontchijoel12@gmail.com'],
            [
                'name' => 'lontchi joel',
                'password' => Hash::make('password123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['administrateur']);

        // Utilisateur 2 : Gérant de stock
        $stockManager = User::updateOrCreate(
            ['email' => 'stock@gesteventes.com'],
            [
                'name' => 'Marie Dupont',
                'password' => Hash::make('stock123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $stockManager->syncRoles(['gerant_stock']);

        // Utilisateur 3 : Vendeur
        $seller = User::updateOrCreate(
            ['email' => 'vendeur@gesteventes.com'],
            [
                'name' => 'Jean Martin',
                'password' => Hash::make('vendeur123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $seller->syncRoles(['vendeur']);

        // Utilisateur 4 : Gestionnaire de ventes
        $salesManager = User::updateOrCreate(
            ['email' => 'ventes@gesteventes.com'],
            [
                'name' => 'Sophie Lambert',
                'password' => Hash::make('ventes123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $salesManager->syncRoles(['gestionnaire_ventes']);

        $this->command->info('✅ 4 utilisateurs créés avec succès !');
        $this->command->info('');
        $this->command->info('🔐 COMPTES DE TEST :');
        $this->command->info('══════════════════════════════════════════════════════════');
        $this->command->info('👑 Administrateur         : lontchijoel12@gmail.com   / password123');
        $this->command->info('📦 Gérant Stock           : stock@gesteventes.com   / stock123');
        $this->command->info('💰 Vendeur                : vendeur@gesteventes.com / vendeur123');
        $this->command->info('🏪 Gestionnaire de ventes : ventes@gesteventes.com  / ventes123');
    }
}