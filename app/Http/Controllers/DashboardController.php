<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Movement;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Afficher le dashboard principal avec KPIs personnalisés par rôle
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 'month'); // jour, semaine, mois, trimestre, année, custom
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $productId = $request->get('product_id');

        // Calculer les dates selon la période
        $dates = $this->calculatePeriodDates($period, $startDate, $endDate);

        // KPIs selon le rôle
        if ($user->isAdmin()) {
            $data = $this->getAdminDashboard($dates, $period, $productId);
        } elseif ($user->isStockManager()) {
            $data = $this->getStockManagerDashboard($dates, $period, $productId);
        } elseif ($user->isSeller()) {
            // Vendeur: statistiques globales, pas personnelles
            $data = $this->getSellerDashboard($dates, $period, $productId);
        } else {
            $data = $this->getBasicDashboard($dates, $period);
        }

        $data['period'] = $period;
        $data['start_date'] = $dates['start']->format('Y-m-d');
        $data['end_date'] = $dates['end']->format('Y-m-d');
        $data['user_role'] = $user->getFormattedRoleName();
        $data['product_id'] = $productId;
        $data['products_list'] = Product::select('id', 'sku', 'nom')->orderBy('nom')->get();


        return view('dashboard', $data);
    }

    /**
     * Dashboard pour Administrateur - Vue complète
     */
    private function getAdminDashboard($dates, $period, $productId = null)
    {
        $previousDates = $this->getPreviousPeriodDates($dates, $period);

        return [
            // KPIs Financiers
            'chiffre_affaires' => $this->getChiffreAffaires($dates, $productId),
            'chiffre_affaires_precedent' => $this->getChiffreAffaires($previousDates, $productId),
            'benefice_net' => $this->getBeneficeNet($dates, $productId),
            'benefice_precedent' => $this->getBeneficeNet($previousDates, $productId),
            'nombre_ventes' => $this->getNombreVentes($dates, $productId),

            // KPIs Produits
            'total_produits' => Product::count(),
            'produits_vendus' => $this->getProduitsVendus($dates, $productId),
            'top_produits' => $this->getTopProduits($dates, 5),

            // KPIs Stock
            'valeur_stock' => $this->getValeurStock($productId),
            'produits_rupture' => $this->getProduitsRupture($productId),
            'produits_alerte' => $this->getProduitsAlerte($productId),

            // Répartition
            'ventes_par_categorie' => $this->getVentesParCategorie($dates, $productId),

            // Activité
            'top_vendeurs' => $this->getTopVendeurs($dates, 10),
            'mouvements_recents' => $this->getMovementsRecents(10, $productId),

            // Graphiques (no-op placeholders)
            'evolution_ca' => $this->getEvolutionCA($dates, $period),
            'repartition_categories' => $this->getRepartitionCategories($dates),
        ];
    }
 
    /**
     * Dashboard pour Gérant de Stock - Focus stock et produits
     */
    private function getStockManagerDashboard($dates, $period, $productId = null)
    {
        return [
            // KPIs Stock
            'total_produits' => Product::count(),
            'valeur_stock' => $this->getValeurStock($productId),
            'produits_rupture' => $this->getProduitsRupture($productId),
            'produits_alerte' => $this->getProduitsAlerte($productId),
            
            // Mouvements
            'entrees_periode' => $this->getEntreesPeriode($dates, $productId),
            'sorties_periode' => $this->getSortiesPeriode($dates, $productId),
            
            // Analyses produits
            'top_produits_stock' => $this->getTopProduitsStock(10),
            'categories_stock' => $this->getCategoriesStock(),
            
            // Graphiques
            'evolution_stock' => $this->getEvolutionStock($dates, $period),
        ];
    }

    /**
     * Dashboard pour Vendeur - Focus ventes personnelles
     */
    private function getSellerDashboard($dates, $period, $productId = null)
    {
        return [
            // KPIs Ventes globales (pas personnelles)
            'nombre_ventes' => $this->getNombreVentes($dates, $productId),
            'chiffre_affaires' => $this->getChiffreAffaires($dates, $productId),
            'produits_vendus' => $this->getProduitsVendus($dates, $productId),
            
            // Stock infos utiles
            'produits_alerte' => $this->getProduitsAlerte($productId),
            'produits_disponibles' => $this->getProduitsDisponibles(),
        ];
    }

    /**
     * Dashboard basique pour autres rôles
     */
    private function getBasicDashboard($dates, $period)
    {
        return [
            'total_produits' => Product::count(),
            'produits_alerte' => $this->getProduitsAlerte(),
            'mouvements_recents' => $this->getMovementsRecents(5),
        ];
    }

    /**
     * Calculer les dates selon la période
     */
    private function calculatePeriodDates($period, $startDate = null, $endDate = null)
    {
        if ($period === 'custom' && $startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay(),
            ];
        }

        switch ($period) {
            case 'day':
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::today()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->startOfWeek(),
                    'end' => Carbon::now()->endOfWeek(),
                ];
            case 'quarter':
                return [
                    'start' => Carbon::now()->startOfQuarter(),
                    'end' => Carbon::now()->endOfQuarter(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear(),
                ];
            default: // month
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                ];
        }
    }

    /**
     * Calculer la période précédente pour comparaison
     */
    private function getPreviousPeriodDates($dates, $period)
    {
        $duration = $dates['end']->diffInDays($dates['start']) + 1;
        
        return [
            'start' => $dates['start']->copy()->subDays($duration),
            'end' => $dates['start']->copy()->subDay(),
        ];
    }

    // ==================== MÉTHODES DE CALCUL KPIs ====================

    private function getChiffreAffaires($dates, $productId = null)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->sum('total') ?? 0;
    }

    private function getBeneficeNet($dates, $productId = null)
    {
        $ventes = Sale::betweenDates($dates['start'], $dates['end'])
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->with('product')
            ->get();

        $benefice = 0;
        foreach ($ventes as $vente) {
            // Check if product and prix_achat exist
            if ($vente->product && $vente->product->prix_achat !== null) {
                $coutAchat = $vente->product->prix_achat * $vente->quantite;
                $benefice += $vente->total - $coutAchat;
            } else {
                // If purchase price is missing, use a default calculation
                // This is a fallback - ideally should be fixed at data entry
                $benefice += $vente->total * 0.3; // Assume 30% margin
            }
        }

        return $benefice;
    }

    private function getProduitsVendus($dates, $productId = null)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->sum('quantite') ?? 0;
    }

    private function getNombreVentes($dates, $productId = null)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->count();
    }

    private function getTopProduits($dates, $limit = 5)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->select('product_id', DB::raw('SUM(quantite) as total_vendu'), DB::raw('SUM(total) as ca_produit'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_vendu', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getProduitsInvendus($dates)
    {
        $produitsVendus = Sale::betweenDates($dates['start'], $dates['end'])
            ->pluck('product_id')
            ->unique();

        return Product::whereNotIn('id', $produitsVendus)
            ->with('stock')
            ->get()
            ->take(10);
    }

    private function getValeurStock($productId = null)
    {
        $query = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(stocks.quantite * products.prix_achat) as valeur_totale');
        if ($productId) {
            $query->where('stocks.product_id', $productId);
        }
        return $query->value('valeur_totale') ?? 0;
    }

    private function getProduitsRupture($productId = null)
    {
        return Stock::where('quantite', 0)
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->with('product')
            ->get();
    }

    private function getProduitsAlerte($productId = null)
    {
        return Stock::whereRaw('quantite <= seuil AND quantite > 0')
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->with('product')
            ->get();
    }

    private function getMovementsRecents($limit = 10, $productId = null)
    {
        return Movement::with(['product'])
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTopVendeurs($dates, $limit = 5)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->select('user_id', DB::raw('SUM(total) as ca_vendeur'), DB::raw('COUNT(*) as nb_ventes'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('ca_vendeur', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getVentesParCategorie($dates, $productId = null)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->when($productId, function($q) use ($productId){ $q->where('sales.product_id', $productId); })
            ->select('products.categorie', DB::raw('SUM(sales.total) as ca_categorie'))
            ->groupBy('products.categorie')
            ->orderBy('ca_categorie', 'desc')
            ->get();
    }

    private function getMesVentes($dates, $user)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->where('user_id', $user->id)
            ->count();
    }

    private function getMonCA($dates, $user)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->where('user_id', $user->id)
            ->sum('total') ?? 0;
    }

    private function getMesTopProduits($dates, $user, $limit = 5)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->where('user_id', $user->id)
            ->select('product_id', DB::raw('SUM(quantite) as total_vendu'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_vendu', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getProduitsDisponibles()
    {
        return Stock::where('quantite', '>', 0)
            ->with('product')
            ->orderBy('quantite', 'desc')
            ->get()
            ->take(10);
    }

    private function getClassementVendeurs($dates)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->select('user_id', DB::raw('SUM(total) as ca_vendeur'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('ca_vendeur', 'desc')
            ->get();
    }

    private function getMaPosition($dates, $user)
    {
        $classement = $this->getClassementVendeurs($dates);
        
        foreach ($classement as $index => $vendeur) {
            if ($vendeur->user_id === $user->id) {
                return $index + 1;
            }
        }
        
        return null;
    }

    private function getEntreesPeriode($dates, $productId = null)
    {
        return Movement::betweenDates($dates['start'], $dates['end'])
            ->where('type', 'ENTREE')
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->sum('quantite') ?? 0;
    }

    private function getSortiesPeriode($dates, $productId = null)
    {
        return Movement::betweenDates($dates['start'], $dates['end'])
            ->where('type', 'SORTIE')
            ->when($productId, function($q) use ($productId){ $q->where('product_id', $productId); })
            ->sum('quantite') ?? 0;
    }

    private function getTopProduitsStock($limit = 10)
    {
        return Stock::with('product')
            ->orderBy('quantite', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getCategoriesStock()
    {
        return Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->select('products.categorie', DB::raw('SUM(stocks.quantite) as total_stock'))
            ->groupBy('products.categorie')
            ->orderBy('total_stock', 'desc')
            ->get();
    }

    // Méthodes pour les graphiques (à implémenter selon les besoins)
    private function getEvolutionCA($dates, $period) { return []; }
    private function getRepartitionCategories($dates) { return []; }
    private function getEvolutionStock($dates, $period) { return []; }
    private function getEvolutionMesVentes($dates, $period, $user) { return []; }
}
