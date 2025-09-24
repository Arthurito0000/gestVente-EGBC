<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;

class NotificationController extends Controller
{
    /**
     * Obtenir les notifications de stocks faibles
     */
    public function getStockNotifications()
    {
        // Logique : si stock actuel <= (seuil + seuil/2), alors notification
        // Exemple : si seuil = 10, notification quand stock <= 15
        $lowStockProducts = Stock::with('product')
            ->whereRaw('quantite <= (seuil + seuil / 2)')
            ->where('quantite', '>', 0) // Exclure les ruptures de stock (déjà visibles ailleurs)
            ->orderBy('quantite', 'asc')
            ->get();

        $notifications = [];
        
        foreach ($lowStockProducts as $stock) {
            $seuilAlerte = $stock->seuil + ($stock->seuil / 2);
            $pourcentage = ($stock->quantite / $seuilAlerte) * 100;
            
            // Déterminer le niveau d'urgence
            $priority = 'low';
            $statusMessage = 'Stock bientôt faible';
            
            if ($stock->quantite <= $stock->seuil) {
                $priority = 'high';
                $statusMessage = 'Stock critique - Réapprovisionnement urgent';
            } elseif ($stock->quantite <= ($stock->seuil * 1.25)) { // 125% du seuil
                $priority = 'medium';
                $statusMessage = 'Stock faible - Prévoir réapprovisionnement';
            }
            
            $notifications[] = [
                'id' => $stock->id,
                'type' => 'stock_faible',
                'title' => $statusMessage,
                'message' => "Le produit \"{$stock->product->nom}\" (SKU: {$stock->product->sku}) approche du seuil critique. Prévoir un réapprovisionnement.",
                'details' => [
                    'product_name' => $stock->product->nom,
                    'sku' => $stock->product->sku,
                    'stock_actuel' => $stock->quantite,
                    'seuil_critique' => $stock->seuil,
                    'seuil_alerte' => round($seuilAlerte, 1),
                    'pourcentage' => round($pourcentage, 1),
                    'quantite_recommandee' => $stock->seuil * 2 // Suggestion de réapprovisionnement
                ],
                'priority' => $priority,
                'created_at' => now()->format('d/m/Y H:i')
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    /**
     * Marquer une notification comme lue (pour usage futur)
     */
    public function markAsRead(Request $request)
    {
        // Pour l'instant, on retourne juste un succès
        // Plus tard, on pourra stocker les notifications lues en base
        return response()->json(['success' => true]);
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getNotificationCount()
    {
        // Compter les produits dont le stock <= (seuil + seuil/2)
        $count = Stock::whereRaw('quantite <= (seuil + seuil / 2)')
            ->where('quantite', '>', 0)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Obtenir les statistiques des notifications
     */
    public function getNotificationStats()
    {
        // Stocks en alerte précoce (entre seuil*1.25 et seuil*1.5)
        $stocksAlertePrecoce = Stock::with('product')
            ->whereRaw('quantite <= (seuil + seuil / 2)')
            ->whereRaw('quantite > (seuil * 1.25)')
            ->where('quantite', '>', 0)
            ->get();

        // Stocks faibles (entre seuil et seuil*1.25)
        $stocksFaibles = Stock::with('product')
            ->whereRaw('quantite <= (seuil * 1.25)')
            ->whereRaw('quantite > seuil')
            ->where('quantite', '>', 0)
            ->get();

        // Stocks critiques (en dessous du seuil mais pas en rupture)
        $stocksCritiques = Stock::with('product')
            ->whereRaw('quantite <= seuil')
            ->where('quantite', '>', 0)
            ->get();

        // Stocks en rupture
        $stocksRupture = Stock::with('product')
            ->where('quantite', 0)
            ->get();

        // Total des notifications (tous les stocks <= seuil + seuil/2)
        $totalNotifications = Stock::whereRaw('quantite <= (seuil + seuil / 2)')
            ->where('quantite', '>', 0)
            ->count();

        return response()->json([
            'alerte_precoce' => $stocksAlertePrecoce->count(),
            'stocks_faibles' => $stocksFaibles->count(),
            'stocks_critiques' => $stocksCritiques->count(),
            'stocks_rupture' => $stocksRupture->count(),
            'total_notifications' => $totalNotifications,
            'details' => [
                'alerte_precoce_products' => $stocksAlertePrecoce->map(function($stock) {
                    return [
                        'nom' => $stock->product->nom,
                        'sku' => $stock->product->sku,
                        'quantite' => $stock->quantite,
                        'seuil' => $stock->seuil,
                        'seuil_alerte' => $stock->seuil + ($stock->seuil / 2)
                    ];
                })
            ]
        ]);
    }

    /**
     * Obtenir les niveaux de stock pour un produit spécifique
     */
    public function getProductStockLevel($productId)
    {
        $stock = Stock::with('product')->where('product_id', $productId)->first();
        
        if (!$stock) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $seuilAlerte = $stock->seuil + ($stock->seuil / 2);
        
        $level = 'normal';
        $message = 'Stock normal';
        
        if ($stock->quantite == 0) {
            $level = 'rupture';
            $message = 'Rupture de stock';
        } elseif ($stock->quantite <= $stock->seuil) {
            $level = 'critique';
            $message = 'Stock critique';
        } elseif ($stock->quantite <= ($stock->seuil * 1.25)) {
            $level = 'faible';
            $message = 'Stock faible';
        } elseif ($stock->quantite <= $seuilAlerte) {
            $level = 'alerte_precoce';
            $message = 'Stock en alerte précoce';
        }

        return response()->json([
            'product' => $stock->product->nom,
            'sku' => $stock->product->sku,
            'quantite_actuelle' => $stock->quantite,
            'seuil_critique' => $stock->seuil,
            'seuil_alerte' => $seuilAlerte,
            'level' => $level,
            'message' => $message,
            'pourcentage_seuil_critique' => round(($stock->quantite / $stock->seuil) * 100, 1),
            'pourcentage_seuil_alerte' => round(($stock->quantite / $seuilAlerte) * 100, 1)
        ]);
    }
}