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

        // Calculer les dates selon la période
        $dates = $this->calculatePeriodDates($period, $startDate, $endDate);

        // KPIs selon le rôle
        if ($user->isAdmin()) {
            $data = $this->getAdminDashboard($dates, $period);
        } elseif ($user->isStockManager()) {
            $data = $this->getStockManagerDashboard($dates, $period);
        } elseif ($user->isSeller()) {
            $data = $this->getSellerDashboard($dates, $period, $user);
        } else {
            $data = $this->getBasicDashboard($dates, $period);
        }

        $data['period'] = $period;
        $data['start_date'] = $dates['start']->format('Y-m-d');
        $data['end_date'] = $dates['end']->format('Y-m-d');
        $data['user_role'] = $user->getFormattedRoleName();

        return view('dashboard', $data);
    }

    /**
     * Dashboard pour Administrateur - Vue complète
     */
    private function getAdminDashboard($dates, $period)
    {
        $previousDates = $this->getPreviousPeriodDates($dates, $period);

        return [
            // KPIs Financiers
            'chiffre_affaires' => $this->getChiffreAffaires($dates),
            'chiffre_affaires_precedent' => $this->getChiffreAffaires($previousDates),
            'benefice_net' => $this->getBeneficeNet($dates),
            'benefice_precedent' => $this->getBeneficeNet($previousDates),
            
            // KPIs Produits
            'total_produits' => Product::count(),
            'produits_vendus' => $this->getProduitsVendus($dates),
            'top_produits' => $this->getTopProduits($dates, 5),
            'produits_invendus' => $this->getProduitsInvendus($dates),
            
            // KPIs Stock
            'valeur_stock' => $this->getValeurStock(),
            'produits_rupture' => $this->getProduitsRupture(),
            'produits_alerte' => $this->getProduitsAlerte(),
            'mouvements_recents' => $this->getMovementsRecents(10),
            
            // KPIs Vendeurs
            'top_vendeurs' => $this->getTopVendeurs($dates, 5),
            'ventes_par_categorie' => $this->getVentesParCategorie($dates),
            
            // Graphiques
            'evolution_ca' => $this->getEvolutionCA($dates, $period),
            'repartition_categories' => $this->getRepartitionCategories($dates),
        ];
    }

    /**
     * Dashboard pour Gérant de Stock - Focus stock et produits
     */
    private function getStockManagerDashboard($dates, $period)
    {
        return [
            // KPIs Stock
            'total_produits' => Product::count(),
            'valeur_stock' => $this->getValeurStock(),
            'produits_rupture' => $this->getProduitsRupture(),
            'produits_alerte' => $this->getProduitsAlerte(),
            
            // Mouvements
            'entrees_periode' => $this->getEntreesPeriode($dates),
            'sorties_periode' => $this->getSortiesPeriode($dates),
            'mouvements_recents' => $this->getMovementsRecents(15),
            
            // Analyses produits
            'top_produits_stock' => $this->getTopProduitsStock(10),
            'produits_invendus' => $this->getProduitsInvendus($dates),
            'categories_stock' => $this->getCategoriesStock(),
            
            // Graphiques
            'evolution_stock' => $this->getEvolutionStock($dates, $period),
        ];
    }

    /**
     * Dashboard pour Vendeur - Focus ventes personnelles
     */
    private function getSellerDashboard($dates, $period, $user)
    {
        return [
            // KPIs Ventes personnelles
            'mes_ventes' => $this->getMesVentes($dates, $user),
            'mon_ca' => $this->getMonCA($dates, $user),
            'mes_ventes_precedent' => $this->getMesVentes($this->getPreviousPeriodDates($dates, $period), $user),
            
            // Produits
            'mes_top_produits' => $this->getMesTopProduits($dates, $user, 5),
            'produits_disponibles' => $this->getProduitsDisponibles(),
            'produits_alerte' => $this->getProduitsAlerte(), // Pour savoir quoi ne pas vendre
            
            // Comparaisons
            'classement_vendeurs' => $this->getClassementVendeurs($dates),
            'ma_position' => $this->getMaPosition($dates, $user),
            
            // Graphiques
            'evolution_mes_ventes' => $this->getEvolutionMesVentes($dates, $period, $user),
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

    private function getChiffreAffaires($dates)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])->sum('total') ?? 0;
    }

    private function getBeneficeNet($dates)
    {
        $ventes = Sale::betweenDates($dates['start'], $dates['end'])
            ->with('product')
            ->get();

        $benefice = 0;
        foreach ($ventes as $vente) {
            $coutAchat = $vente->product->prix_achat * $vente->quantite;
            $benefice += $vente->total - $coutAchat;
        }

        return $benefice;
    }

    private function getProduitsVendus($dates)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])->sum('quantite') ?? 0;
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

    private function getValeurStock()
    {
        return Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(stocks.quantite * products.prix_achat) as valeur_totale')
            ->value('valeur_totale') ?? 0;
    }

    private function getProduitsRupture()
    {
        return Stock::where('quantite', 0)
            ->with('product')
            ->get();
    }

    private function getProduitsAlerte()
    {
        return Stock::whereRaw('quantite <= seuil AND quantite > 0')
            ->with('product')
            ->get();
    }

    private function getMovementsRecents($limit = 10)
    {
        return Movement::with(['product'])
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

    private function getVentesParCategorie($dates)
    {
        return Sale::betweenDates($dates['start'], $dates['end'])
            ->join('products', 'sales.product_id', '=', 'products.id')
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

    private function getEntreesPeriode($dates)
    {
        return Movement::betweenDates($dates['start'], $dates['end'])
            ->where('type', 'ENTREE')
            ->sum('quantite') ?? 0;
    }

    private function getSortiesPeriode($dates)
    {
        return Movement::betweenDates($dates['start'], $dates['end'])
            ->where('type', 'SORTIE')
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
