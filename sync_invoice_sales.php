<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Invoice;
use App\Models\Sale;

// Récupérer la facture INV-1
$invoice = Invoice::where('code', 'INV-1')->with('products')->first();

if (!$invoice) {
    echo "Facture INV-1 non trouvée\n";
    exit(1);
}

echo "Synchronisation de la facture {$invoice->code}\n";
echo "Date: {$invoice->invoice_date}\n";
echo "Montant total: {$invoice->total_amount} FCFA\n";

// Créer les entrées de vente pour chaque produit de la facture
foreach ($invoice->products as $product) {
    echo "Création vente pour: {$product->nom} (SKU: {$product->sku})\n";
    echo "  Quantité: {$product->pivot->quantity}\n";
    echo "  Prix unitaire: {$product->pivot->unit_price} FCFA\n";
    echo "  Total: {$product->pivot->total_price} FCFA\n";
    
    // Vérifier si une vente existe déjà pour cette facture et ce produit
    $existingSale = Sale::where('numero_facture', $invoice->code)
                       ->where('product_id', $product->id)
                       ->first();
    
    if ($existingSale) {
        echo "  ⚠️ Vente déjà existante, ignorée\n";
        continue;
    }
    
    // Créer l'entrée de vente
    Sale::create([
        'product_id' => $product->id,
        'user_id' => 1, // Admin par défaut (à ajuster si nécessaire)
        'quantite' => $product->pivot->quantity,
        'prix_unitaire' => $product->pivot->unit_price,
        'total' => $product->pivot->total_price,
        'date_vente' => $invoice->invoice_date,
        'numero_facture' => $invoice->code,
        'notes' => 'Vente via facture ' . $invoice->code . ' (synchronisation)'
    ]);
    
    echo "  ✅ Vente créée avec succès\n";
}

echo "\n🎉 Synchronisation terminée ! Le dashboard devrait maintenant afficher les bonnes statistiques.\n";
