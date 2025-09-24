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
        // Logique : si stock actuel <= (seuil / 2 + stock actuel), alors notification
        // Simplifié : si stock actuel <= seuil / 2, alors notification
        $lowStockProducts = Stock::with('product')
            ->whereRaw('quantite <= (seuil / 2)')
            ->where('quantite', '>', 0) // Exclure les ruptures de stock (déjà visibles ailleurs)
            ->orderBy('quantite', 'asc')
            ->get();

        $notifications = [];
        
        foreach ($lowStockProducts as $stock) {
            $pourcentage = ($stock->quantite / $stock->seuil) * 100;
            
            $notifications[] = [
                'id' => $stock->id,
                'type' => 'stock_faible',
                'title' => 'Stock faible détecté',
                'message' => "Le produit \"{$stock->product->nom}\" (SKU: {$stock->product->sku}) approche du seuil critique.",
                'details' => [
                    'product_name' => $stock->product->nom,
                    'sku' => $stock->product->sku,
                    'stock_actuel' => $stock->quantite,
                    'seuil' => $stock->seuil,
                    'pourcentage' => round($pourcentage, 1),
                    'seuil_alerte' => round($stock->seuil / 2, 1)
                ],
                'priority' => $pourcentage <= 25 ? 'high' : 'medium',
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
        $count = Stock::whereRaw('quantite <= (seuil / 2)')
            ->where('quantite', '>', 0)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Obtenir les statistiques des notifications
     */
    public function getNotificationStats()
    {
        $stocksFaibles = Stock::with('product')
            ->whereRaw('quantite <= (seuil / 2)')
            ->where('quantite', '>', 0)
            ->get();

        $stocksRupture = Stock::with('product')
            ->where('quantite', 0)
            ->get();

        $stocksCritiques = Stock::with('product')
            ->whereRaw('quantite <= (seuil / 4)')
            ->where('quantite', '>', 0)
            ->get();

        return response()->json([
            'stocks_faibles' => $stocksFaibles->count(),
            'stocks_rupture' => $stocksRupture->count(),
            'stocks_critiques' => $stocksCritiques->count(),
            'total_notifications' => $stocksFaibles->count() + $stocksRupture->count()
        ]);
    }
}
