<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Movement;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Générer des données de test pour la pagination
     */
    public function run(): void
    {
        // Créer 25 produits supplémentaires pour tester la pagination
        $categories = Category::pluck('name')->toArray();
        if (empty($categories)) {
            $categories = ['Électronique', 'Alimentaire', 'Vêtements', 'Autre'];
        }

        for ($i = 1; $i <= 25; $i++) {
            $product = Product::create([
                'sku' => 'TEST-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nom' => 'Produit Test ' . $i,
                'prix_achat' => rand(1000, 50000),
                'prix_vente' => rand(1500, 75000),
                'categorie' => $categories[array_rand($categories)],
            ]);

            // Créer le stock correspondant
            $quantite = rand(0, 100);
            $seuil = rand(5, 20);
            
            Stock::create([
                'product_id' => $product->id,
                'quantite' => $quantite,
                'seuil' => $seuil
            ]);

            // Créer un mouvement initial si quantité > 0
            if ($quantite > 0) {
                Movement::create([
                    'product_id' => $product->id,
                    'type' => 'ENTREE',
                    'quantite' => $quantite,
                    'motif' => 'Stock initial test',
                    'date' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        // Créer 15 utilisateurs supplémentaires pour tester la pagination
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'name' => 'Utilisateur Test ' . $i,
                'email' => 'test' . $i . '@gesteventes.com',
                'password' => Hash::make('test123'),
                'statut' => rand(0, 1) ? 'actif' : 'inactif',
            ])->assignRole('vendeur');
        }

        $this->command->info('✅ Données de test créées : 25 produits et 15 utilisateurs');
    }
}
