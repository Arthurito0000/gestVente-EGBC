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
        $this->command->info('🚀 Création de 6 utilisateurs de test avec rôles et statuts...');

        // Utilisateur 1 : Admin principal
        $admin = User::updateOrCreate(
            ['email' => 'admin@gesteventes.com'],
            [
                'name' => 'Administrateur Principal',
                'password' => Hash::make('password123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['administrateur']);

        // Utilisateur 2 : Arthur (développeur/admin)
        $arthur = User::updateOrCreate(
            ['email' => 'arthur@gesteventes.com'],
            [
                'name' => 'Arthur EGBC',
                'password' => Hash::make('arthur123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $arthur->syncRoles(['administrateur']);

        // Utilisateur 3 : Gérant de stock
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

        // Utilisateur 4 : Vendeur principal
        $seller1 = User::updateOrCreate(
            ['email' => 'vendeur@gesteventes.com'],
            [
                'name' => 'Jean Martin',
                'password' => Hash::make('vendeur123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $seller1->syncRoles(['vendeur']);

        // Utilisateur 5 : Vendeur secondaire
        $seller2 = User::updateOrCreate(
            ['email' => 'vendeur2@gesteventes.com'],
            [
                'name' => 'Sophie Dubois',
                'password' => Hash::make('vendeur123'),
                'statut' => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $seller2->syncRoles(['vendeur']);

        // Utilisateur 6 : Utilisateur inactif (pour démonstration)
        $inactiveUser = User::updateOrCreate(
            ['email' => 'inactif@gesteventes.com'],
            [
                'name' => 'Utilisateur Inactif',
                'password' => Hash::make('inactif123'),
                'statut' => 'inactif',
                'email_verified_at' => now(),
            ]
        );
        $inactiveUser->syncRoles(['vendeur']);

        $this->command->info('✅ 6 utilisateurs créés avec leurs rôles et statuts !');
        $this->command->info('');
        $this->command->info('🔐 COMPTES DE TEST DISPONIBLES :');
        $this->command->info('══════════════════════════════════════════════════════════');
        $this->command->info('👑 ADMINISTRATEURS :');
        $this->command->info('   📧 admin@gesteventes.com      🔑 password123');
        $this->command->info('   📧 arthur@gesteventes.com     🔑 arthur123');
        $this->command->info('');
        $this->command->info('📦 GÉRANT DE STOCK :');
        $this->command->info('   📧 stock@gesteventes.com      🔑 stock123');
        $this->command->info('');
        $this->command->info('💰 VENDEURS :');
        $this->command->info('   📧 vendeur@gesteventes.com    🔑 vendeur123');
        $this->command->info('   📧 vendeur2@gesteventes.com   🔑 vendeur123');
        $this->command->info('');
        $this->command->info('❌ UTILISATEUR INACTIF (pour test) :');
        $this->command->info('   📧 inactif@gesteventes.com    🔑 inactif123 (BLOQUÉ)');
        $this->command->info('');
        $this->command->info('🎯 Rôles et statuts assignés automatiquement !');
        $this->command->info('ℹ️  L\'utilisateur inactif ne peut pas se connecter.');
    }
}