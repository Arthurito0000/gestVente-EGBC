<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Création de 5 utilisateurs de test avec rôles...');

        // Utilisateur 1 : Admin principal
        $admin = User::factory()
            ->withName('Administrateur Principal')
            ->withEmail('admin@gesteventes.com')
            ->withPassword('password123')
            ->create();
        $admin->assignRole('administrateur');

        // Utilisateur 2 : Arthur (développeur/admin)
        $arthur = User::factory()
            ->withName('Arthur EGBC')
            ->withEmail('arthur@gesteventes.com')
            ->withPassword('arthur123')
            ->create();
        $arthur->assignRole('administrateur');

        // Utilisateur 3 : Gérant de stock
        $stockManager = User::factory()
            ->withName('Marie Dupont')
            ->withEmail('stock@gesteventes.com')
            ->withPassword('stock123')
            ->create();
        $stockManager->assignRole('gerant_stock');

        // Utilisateur 4 : Vendeur principal
        $seller1 = User::factory()
            ->withName('Jean Martin')
            ->withEmail('vendeur@gesteventes.com')
            ->withPassword('vendeur123')
            ->create();
        $seller1->assignRole('vendeur');

        // Utilisateur 5 : Vendeur secondaire
        $seller2 = User::factory()
            ->withName('Sophie Dubois')
            ->withEmail('vendeur2@gesteventes.com')
            ->withPassword('vendeur123')
            ->create();
        $seller2->assignRole('vendeur');

        $this->command->info('✅ 5 utilisateurs créés avec leurs rôles !');
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
        $this->command->info('🎯 Rôles assignés automatiquement !');
    }
}