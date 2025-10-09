<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;

class NotificationController extends Controller
{
    /**
     * Obtenir les notifications de stocks faibles ET ruptures
     */
    public function getStockNotifications()
{
    // Vérification stricte
    if (!auth()->check()) {
        return response()->json([
            'notifications' => [],
            'count' => 0,
            'error' => 'Unauthorized'
        ], 401);
    }

    try {
        // Récupérer les stocks avec leurs produits
        $stockProducts = Stock::with('product')
            ->where(function($query) {
                $query->where('quantite', 0)
                      ->orWhereRaw('quantite <= (seuil + seuil / 2)');
            })
            ->orderByRaw('CASE WHEN quantite = 0 THEN 0 ELSE quantite END ASC')
            ->get();

        $notifications = [];
        
        foreach ($stockProducts as $stock) {
            // Calculer le seuil d'alerte (seuil + 50% du seuil)
            $seuilAlerte = $stock->seuil + ($stock->seuil / 2);
            
            // Déterminer la priorité et les détails
            if ($stock->quantite == 0) {
                // RUPTURE DE STOCK
                $notifications[] = [
                    'priority' => 'critical',
                    'title' => 'RUPTURE TOTALE - Vente impossible',
                    'details' => [
                        'product_name' => $stock->product->nom,
                        'sku' => $stock->product->sku,
                        'stock_actuel' => 0,
                        'seuil_critique' => $stock->seuil,
                        'seuil_alerte' => $seuilAlerte,
                        'quantite_recommandee' => $stock->seuil * 2, // Recommander le double du seuil
                        'pourcentage' => 0
                    ]
                ];
            } elseif ($stock->quantite <= $stock->seuil) {
                // STOCK CRITIQUE
                $pourcentage = round(($stock->quantite / $seuilAlerte) * 100, 1);
                $notifications[] = [
                    'priority' => 'high',
                    'title' => 'Stock critique - Réapprovisionnement urgent requis',
                    'details' => [
                        'product_name' => $stock->product->nom,
                        'sku' => $stock->product->sku,
                        'stock_actuel' => $stock->quantite,
                        'seuil_critique' => $stock->seuil,
                        'seuil_alerte' => $seuilAlerte,
                        'pourcentage' => $pourcentage
                    ]
                ];
            } elseif ($stock->quantite <= $seuilAlerte) {
                // STOCK FAIBLE
                $pourcentage = round(($stock->quantite / $seuilAlerte) * 100, 1);
                $notifications[] = [
                    'priority' => 'medium',
                    'title' => 'Stock faible - Planifier un réapprovisionnement',
                    'details' => [
                        'product_name' => $stock->product->nom,
                        'sku' => $stock->product->sku,
                        'stock_actuel' => $stock->quantite,
                        'seuil_critique' => $stock->seuil,
                        'seuil_alerte' => $seuilAlerte,
                        'pourcentage' => $pourcentage
                    ]
                ];
            }
        }
        
        return response()->json([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    } catch (\Exception $e) {
        \Log::error('Erreur notifications stock: ' . $e->getMessage());
        return response()->json(['notifications' => [], 'count' => 0], 500);
    }
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
     * Obtenir le nombre de notifications non lues (stocks faibles + ruptures)
     */
    public function getNotificationCount()
    {
        // Vérification stricte
        if (!auth()->check()) {
            return response()->json(['count' => 0, 'error' => 'Unauthorized'], 401);
        }
    
        try {
            $count = Stock::where(function($query) {
                    $query->where('quantite', 0)
                          ->orWhereRaw('quantite <= (seuil + seuil / 2)');
                })
                ->count();
    
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            \Log::error('Erreur notifications count: ' . $e->getMessage());
            return response()->json(['count' => 0], 500);
        }
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

        // Total des notifications (ruptures + stocks faibles)
        $totalNotifications = Stock::where(function($query) {
                $query->where('quantite', 0) // Ruptures de stock
                      ->orWhereRaw('quantite <= (seuil + seuil / 2)'); // Stocks faibles
            })
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