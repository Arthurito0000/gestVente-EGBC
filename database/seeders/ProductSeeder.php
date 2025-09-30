<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'sku' => 'SKU-001',
                'nom' => 'Souris Optique',
                'prix_achat' => 5.50,
                'prix_vente' => 8.50,
                'categorie' => 'Électronique',
                'quantite' => 120,
                'seuil_stock' => 20
            ],
            [
                'sku' => 'SKU-114',
                'nom' => 'Clavier Mécanique',
                'prix_achat' => 32.00,
                'prix_vente' => 55.00,
                'categorie' => 'Électronique',
                'quantite' => 15,
                'seuil_stock' => 10
            ],
            [
                'sku' => 'SKU-221',
                'nom' => 'Écran 24"',
                'prix_achat' => 110.00,
                'prix_vente' => 180.00,
                'categorie' => 'Électronique',
                'quantite' => 30,
                'seuil_stock' => 5
            ],
            [
                'sku' => 'SKU-778',
                'nom' => 'Câble HDMI',
                'prix_achat' => 8.50,
                'prix_vente' => 15.00,
                'categorie' => 'Électronique',
                'quantite' => 3,
                'seuil_stock' => 5
            ],
            [
                'sku' => 'SKU-311',
                'nom' => 'Batterie 18650',
                'prix_achat' => 12.00,
                'prix_vente' => 20.00,
                'categorie' => 'Électronique',
                'quantite' => 5,
                'seuil_stock' => 10
            ],
            [
                'sku' => 'SKU-992',
                'nom' => 'Adaptateur USB-C',
                'prix_achat' => 6.00,
                'prix_vente' => 12.00,
                'categorie' => 'Électronique',
                'quantite' => 7,
                'seuil_stock' => 8
            ],
            [
                'sku' => 'SKU-ALI-001',
                'nom' => 'Café en grains 1kg',
                'prix_achat' => 12.50,
                'prix_vente' => 22.00,
                'categorie' => 'Alimentaire',
                'quantite' => 25,
                'seuil_stock' => 5
            ],
            [
                'sku' => 'SKU-VET-001',
                'nom' => 'T-shirt coton bio',
                'prix_achat' => 8.00,
                'prix_vente' => 15.00,
                'categorie' => 'Vêtements',
                'quantite' => 12,
                'seuil_stock' => 3
            ],
            [
                'sku' => 'SKU-AUT-001',
                'nom' => 'Carnet de notes A5',
                'prix_achat' => 3.50,
                'prix_vente' => 7.00,
                'categorie' => 'Autre',
                'quantite' => 2,
                'seuil_stock' => 5
            ],
            [
                'sku' => 'SKU-SANS-CAT',
                'nom' => 'Produit sans catégorie',
                'prix_achat' => 10.00,
                'prix_vente' => 18.00,
                'categorie' => null,
                'quantite' => 15,
                'seuil_stock' => 8
            ]
        ];

        foreach ($products as $productData) {
            // Extraire les données de stock
            $quantite = $productData['quantite'];
            $seuil = $productData['seuil_stock'];
            
            // Supprimer les champs stock des données produit
            unset($productData['quantite']);
            
            // Créer ou mettre à jour le produit
            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
            
            // Créer ou mettre à jour le stock
            $stock = Stock::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantite' => $quantite,
                    'seuil' => $seuil
                ]
            );
            
            // Créer un mouvement initial si le stock vient d'être créé
            if ($stock->wasRecentlyCreated && $quantite > 0) {
                Movement::create([
                    'product_id' => $product->id,
                    'type' => 'ENTREE',
                    'quantite' => $quantite,
                    'motif' => 'Stock initial',
                    'date' => now()
                ]);
            }
        }
        
        $this->command->info('✅ ' . count($products) . ' produits créés avec leurs stocks !');
        $this->command->info('📦 Stocks et mouvements initiaux générés automatiquement.');
    }
}
