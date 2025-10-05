<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs vendeurs et admin
        $vendeur = User::where('email', 'vendeur@gesteventes.com')->first();
        $admin = User::where('email', 'admin@gesteventes.com')->first();
        
        if (!$vendeur || !$admin) {
            $this->command->warn('Les utilisateurs vendeur ou admin n\'existent pas. Veuillez exécuter UserSeeder d\'abord.');
            return;
        }

        // Récupérer quelques produits
        $products = Product::with('stock')->take(10)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('Aucun produit trouvé. Veuillez exécuter ProductSeeder d\'abord.');
            return;
        }

        // Devis 1 : Brouillon (vendeur)
        $quote1 = Quote::create([
            'numero_devis' => 'DEV-20250104-001',
            'user_id' => $vendeur->id,
            'client_nom' => 'Mme CHRISTINE (Lendi)',
            'objet' => 'Fabrication et pose de 14 fenêtres en aluminium noir sans moustiquaire et vitre antelio bleu',
            'date_devis' => now()->subDays(5),
            'date_validite' => now()->addDays(25),
            'statut' => 'brouillon',
            'total_materiel' => 0,
            'total_main_oeuvre' => 0,
            'total_general' => 0,
        ]);

        // Items pour devis 1
        $items1 = [
            ['designation' => 'Dormant', 'quantite' => 5, 'prix_unitaire' => 13000, 'type' => 'materiel'],
            ['designation' => 'montant serrure', 'quantite' => 5, 'prix_unitaire' => 12000, 'type' => 'materiel'],
            ['designation' => 'montant chicane', 'quantite' => 5, 'prix_unitaire' => 12000, 'type' => 'materiel'],
            ['designation' => 'traverse', 'quantite' => 6, 'prix_unitaire' => 12000, 'type' => 'materiel'],
            ['designation' => 'rail', 'quantite' => 6, 'prix_unitaire' => 19000, 'type' => 'materiel'],
            ['designation' => 'ouvre joint', 'quantite' => 13, 'prix_unitaire' => 4500, 'type' => 'materiel'],
            ['designation' => 'serrure', 'quantite' => 14, 'prix_unitaire' => 1500, 'type' => 'materiel'],
            ['designation' => 'galet', 'quantite' => 14, 'prix_unitaire' => 10000, 'type' => 'materiel'],
            ['designation' => 'vitre', 'quantite' => 18, 'prix_unitaire' => 10000, 'type' => 'materiel'],
            ['designation' => 'joint vitrage', 'quantite' => 2, 'prix_unitaire' => 14000, 'type' => 'materiel'],
            ['designation' => 'vise d\'assemblage', 'quantite' => 1, 'prix_unitaire' => 3500, 'type' => 'materiel'],
            ['designation' => 'vise pose', 'quantite' => 1, 'prix_unitaire' => 3500, 'type' => 'materiel'],
            ['designation' => 'silicone', 'quantite' => 10, 'prix_unitaire' => 1500, 'type' => 'materiel'],
            ['designation' => 'joint broche', 'quantite' => 1, 'prix_unitaire' => 10000, 'type' => 'materiel'],
            ['designation' => 'cheville', 'quantite' => 1, 'prix_unitaire' => 1000, 'type' => 'materiel'],
            ['designation' => 'Main d\'œuvre', 'quantite' => 1, 'prix_unitaire' => 150000, 'type' => 'main_oeuvre'],
        ];

        $totalMateriel1 = 0;
        $totalMainOeuvre1 = 0;

        foreach ($items1 as $itemData) {
            $product = $products->random();
            $prixTotal = $itemData['quantite'] * $itemData['prix_unitaire'];
            
            QuoteItem::create([
                'quote_id' => $quote1->id,
                'product_id' => $product->id,
                'designation' => $itemData['designation'],
                'quantite' => $itemData['quantite'],
                'prix_unitaire' => $itemData['prix_unitaire'],
                'prix_total' => $prixTotal,
                'type' => $itemData['type'],
            ]);

            if ($itemData['type'] === 'materiel') {
                $totalMateriel1 += $prixTotal;
            } else {
                $totalMainOeuvre1 += $prixTotal;
            }
        }

        $quote1->update([
            'total_materiel' => $totalMateriel1,
            'total_main_oeuvre' => $totalMainOeuvre1,
            'total_general' => $totalMateriel1 + $totalMainOeuvre1,
        ]);

        // Devis 2 : Envoyé (admin)
        $quote2 = Quote::create([
            'numero_devis' => 'DEV-20250103-001',
            'user_id' => $admin->id,
            'client_nom' => 'Mr JOEL',
            'objet' => 'Installation de porte en aluminium avec vitre',
            'date_devis' => now()->subDays(3),
            'date_validite' => now()->addDays(27),
            'statut' => 'envoye',
            'total_materiel' => 0,
            'total_main_oeuvre' => 0,
            'total_general' => 0,
        ]);

        $items2 = [
            ['designation' => 'Porte aluminium', 'quantite' => 1, 'prix_unitaire' => 85000, 'type' => 'materiel'],
            ['designation' => 'Vitre sécurit', 'quantite' => 1, 'prix_unitaire' => 45000, 'type' => 'materiel'],
            ['designation' => 'Serrure 3 points', 'quantite' => 1, 'prix_unitaire' => 25000, 'type' => 'materiel'],
            ['designation' => 'Poignée', 'quantite' => 2, 'prix_unitaire' => 8000, 'type' => 'materiel'],
            ['designation' => 'Installation', 'quantite' => 1, 'prix_unitaire' => 50000, 'type' => 'main_oeuvre'],
        ];

        $totalMateriel2 = 0;
        $totalMainOeuvre2 = 0;

        foreach ($items2 as $itemData) {
            $product = $products->random();
            $prixTotal = $itemData['quantite'] * $itemData['prix_unitaire'];
            
            QuoteItem::create([
                'quote_id' => $quote2->id,
                'product_id' => $product->id,
                'designation' => $itemData['designation'],
                'quantite' => $itemData['quantite'],
                'prix_unitaire' => $itemData['prix_unitaire'],
                'prix_total' => $prixTotal,
                'type' => $itemData['type'],
            ]);

            if ($itemData['type'] === 'materiel') {
                $totalMateriel2 += $prixTotal;
            } else {
                $totalMainOeuvre2 += $prixTotal;
            }
        }

        $quote2->update([
            'total_materiel' => $totalMateriel2,
            'total_main_oeuvre' => $totalMainOeuvre2,
            'total_general' => $totalMateriel2 + $totalMainOeuvre2,
        ]);

        // Devis 3 : Accepté (vendeur)
        $quote3 = Quote::create([
            'numero_devis' => 'DEV-20250102-001',
            'user_id' => $vendeur->id,
            'client_nom' => 'Société BATITECH',
            'objet' => 'Fourniture et pose de grille de protection roulante',
            'date_devis' => now()->subDays(7),
            'date_validite' => now()->addDays(23),
            'statut' => 'accepte',
            'total_materiel' => 0,
            'total_main_oeuvre' => 0,
            'total_general' => 0,
        ]);

        $items3 = [
            ['designation' => 'Grille roulante 2m x 2m', 'quantite' => 2, 'prix_unitaire' => 120000, 'type' => 'materiel'],
            ['designation' => 'Moteur électrique', 'quantite' => 2, 'prix_unitaire' => 75000, 'type' => 'materiel'],
            ['designation' => 'Télécommande', 'quantite' => 4, 'prix_unitaire' => 15000, 'type' => 'materiel'],
            ['designation' => 'Installation et câblage', 'quantite' => 1, 'prix_unitaire' => 100000, 'type' => 'main_oeuvre'],
        ];

        $totalMateriel3 = 0;
        $totalMainOeuvre3 = 0;

        foreach ($items3 as $itemData) {
            $product = $products->random();
            $prixTotal = $itemData['quantite'] * $itemData['prix_unitaire'];
            
            QuoteItem::create([
                'quote_id' => $quote3->id,
                'product_id' => $product->id,
                'designation' => $itemData['designation'],
                'quantite' => $itemData['quantite'],
                'prix_unitaire' => $itemData['prix_unitaire'],
                'prix_total' => $prixTotal,
                'type' => $itemData['type'],
            ]);

            if ($itemData['type'] === 'materiel') {
                $totalMateriel3 += $prixTotal;
            } else {
                $totalMainOeuvre3 += $prixTotal;
            }
        }

        $quote3->update([
            'total_materiel' => $totalMateriel3,
            'total_main_oeuvre' => $totalMainOeuvre3,
            'total_general' => $totalMateriel3 + $totalMainOeuvre3,
        ]);

        // Devis 4 : Refusé (admin)
        $quote4 = Quote::create([
            'numero_devis' => 'DEV-20250101-001',
            'user_id' => $admin->id,
            'client_nom' => 'M. DUPONT',
            'objet' => 'Rénovation fenêtres appartement',
            'date_devis' => now()->subDays(10),
            'date_validite' => now()->addDays(20),
            'statut' => 'refuse',
            'total_materiel' => 0,
            'total_main_oeuvre' => 0,
            'total_general' => 0,
        ]);

        $items4 = [
            ['designation' => 'Fenêtre aluminium 1m x 1.5m', 'quantite' => 3, 'prix_unitaire' => 65000, 'type' => 'materiel'],
            ['designation' => 'Vitre double vitrage', 'quantite' => 3, 'prix_unitaire' => 35000, 'type' => 'materiel'],
            ['designation' => 'Pose et finitions', 'quantite' => 1, 'prix_unitaire' => 80000, 'type' => 'main_oeuvre'],
        ];

        $totalMateriel4 = 0;
        $totalMainOeuvre4 = 0;

        foreach ($items4 as $itemData) {
            $product = $products->random();
            $prixTotal = $itemData['quantite'] * $itemData['prix_unitaire'];
            
            QuoteItem::create([
                'quote_id' => $quote4->id,
                'product_id' => $product->id,
                'designation' => $itemData['designation'],
                'quantite' => $itemData['quantite'],
                'prix_unitaire' => $itemData['prix_unitaire'],
                'prix_total' => $prixTotal,
                'type' => $itemData['type'],
            ]);

            if ($itemData['type'] === 'materiel') {
                $totalMateriel4 += $prixTotal;
            } else {
                $totalMainOeuvre4 += $prixTotal;
            }
        }

        $quote4->update([
            'total_materiel' => $totalMateriel4,
            'total_main_oeuvre' => $totalMainOeuvre4,
            'total_general' => $totalMateriel4 + $totalMainOeuvre4,
        ]);

        $this->command->info('✅ 4 devis de test créés avec succès !');
        $this->command->info('   - 1 Brouillon (vendeur)');
        $this->command->info('   - 1 Envoyé (admin)');
        $this->command->info('   - 1 Accepté (vendeur)');
        $this->command->info('   - 1 Refusé (admin)');
    }
}
