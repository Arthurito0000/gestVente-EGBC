<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Création de données de ventes de test...');

        // Récupérer les produits et utilisateurs
        $products = Product::all();
        $vendeurs = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['vendeur', 'administrateur']);
        })->get();

        if ($products->isEmpty() || $vendeurs->isEmpty()) {
            $this->command->warn('⚠️ Pas de produits ou vendeurs disponibles. Exécutez d\'abord ProductSeeder et UserSeeder.');
            return;
        }

        $sales = [];
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        // Générer 50 ventes sur les 3 derniers mois
        for ($i = 0; $i < 50; $i++) {
            $product = $products->random();
            $vendeur = $vendeurs->random();
            $quantite = rand(1, 5);
            $prixUnitaire = $product->prix_achat * (1 + (rand(20, 80) / 100)); // Marge de 20% à 80%
            $total = $quantite * $prixUnitaire;
            $dateVente = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            $sales[] = [
                'product_id' => $product->id,
                'user_id' => $vendeur->id,
                'quantite' => $quantite,
                'prix_unitaire' => round($prixUnitaire, 2),
                'total' => round($total, 2),
                'date_vente' => $dateVente->format('Y-m-d'),
                'numero_facture' => 'FAC-' . $dateVente->format('Ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'notes' => $this->getRandomNote(),
                'created_at' => $dateVente,
                'updated_at' => $dateVente,
            ];
        }

        // Ajouter quelques ventes récentes (cette semaine)
        for ($i = 0; $i < 10; $i++) {
            $product = $products->random();
            $vendeur = $vendeurs->random();
            $quantite = rand(1, 3);
            $prixUnitaire = $product->prix_achat * (1 + (rand(25, 60) / 100));
            $total = $quantite * $prixUnitaire;
            $dateVente = Carbon::now()->subDays(rand(0, 6));

            $sales[] = [
                'product_id' => $product->id,
                'user_id' => $vendeur->id,
                'quantite' => $quantite,
                'prix_unitaire' => round($prixUnitaire, 2),
                'total' => round($total, 2),
                'date_vente' => $dateVente->format('Y-m-d'),
                'numero_facture' => 'FAC-' . $dateVente->format('Ymd') . '-' . str_pad($i + 51, 3, '0', STR_PAD_LEFT),
                'notes' => $this->getRandomNote(),
                'created_at' => $dateVente,
                'updated_at' => $dateVente,
            ];
        }

        Sale::insert($sales);

        $this->command->info('✅ ' . count($sales) . ' ventes créées avec succès !');
        $this->command->info('📊 Répartition :');
        $this->command->info('   • 50 ventes sur les 3 derniers mois');
        $this->command->info('   • 10 ventes récentes (cette semaine)');
        $this->command->info('   • Réparties entre ' . $vendeurs->count() . ' vendeurs');
        $this->command->info('   • Sur ' . $products->count() . ' produits différents');
    }

    private function getRandomNote(): ?string
    {
        $notes = [
            'Vente en magasin',
            'Commande téléphonique',
            'Vente en ligne',
            'Client fidèle',
            'Promotion spéciale',
            'Vente groupée',
            'Commande urgente',
            null, // Parfois pas de note
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }
}
