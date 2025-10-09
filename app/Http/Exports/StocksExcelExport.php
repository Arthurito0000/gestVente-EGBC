<?php

namespace App\Http\Exports;

use App\Models\Stock;
use App\Http\Exports\BaseExport;

class StocksExcelExport extends BaseExport
{
    protected $stocks;

    public function __construct($search = null, $filters = [])
    {
        parent::__construct($search, $filters);
    }

    /**
     * Charger les stocks avec filtres
     */
    protected function loadData()
    {
        $query = Stock::with('product');

        // Appliquer la logique de recherche
        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('sku', 'LIKE', "%{$this->search}%")
                  ->orWhere('nom', 'LIKE', "%{$this->search}%")
                  ->orWhere('categorie', 'LIKE', "%{$this->search}%");
            });
        }

        // Filtres spécifiques aux stocks
        if (isset($this->filters['stock_faible']) && $this->filters['stock_faible']) {
            $query->whereRaw('quantite <= seuil');
        }

        if (isset($this->filters['stock_zero']) && $this->filters['stock_zero']) {
            $query->where('quantite', 0);
        }

        if (isset($this->filters['seuil_min']) && $this->filters['seuil_min']) {
            $query->where('seuil', '>=', $this->filters['seuil_min']);
        }

        $this->stocks = $query->latest()->get();
        $this->data = $this->stocks;
    }

    /**
     * Définir les en-têtes des colonnes
     */
    public function headings(): array
    {
        return [
            'Code SKU',
            'Nom du Produit',
            'Catégorie',
            'Quantité en Stock',
            'Seuil d\'Alerte',
            'Statut Stock',
            'Valeur Stock (Fcfa)',
            'Dernière MAJ'
        ];
    }

    /**
     * Définir les données à exporter
     */
    public function collection()
    {
        return $this->stocks->map(function($stock) {
            $product = $stock->product;
            $valeurStock = $stock->quantite * $product->prix_achat;
            
            // Déterminer le statut du stock
            $statut = 'Normal';
            if ($stock->quantite == 0) {
                $statut = 'Rupture';
            } elseif ($stock->quantite <= $stock->seuil) {
                $statut = 'Stock faible';
            }

            return [
                'sku' => $product->sku,
                'nom' => $product->nom,
                'categorie' => $product->categorie ?: 'Non définie',
                'quantite' => $stock->quantite,
                'seuil' => $stock->seuil,
                'statut' => $statut,
                'valeur_stock' => $this->formatPrice($valeurStock),
                'updated_at' => $this->formatDate($stock->updated_at)
            ];
        });
    }

    /**
     * Générer le fichier CSV
     */
    public function download()
    {
        $filename = $this->generateFilename('stocks', 'csv');
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM pour UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, $this->headings(), ';');
            
            // Données
            foreach ($this->collection() as $row) {
                fputcsv($file, array_values($row), ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtenir les statistiques d'exportation
     */
    public function getStats()
    {
        $baseStats = $this->getBaseStats();
        
        $stocksStats = [
            'total_stocks' => $this->stocks->count(),
            'stocks_normaux' => $this->stocks->filter(function($stock) {
                return $stock->quantite > $stock->seuil;
            })->count(),
            'stocks_faibles' => $this->stocks->filter(function($stock) {
                return $stock->quantite <= $stock->seuil && $stock->quantite > 0;
            })->count(),
            'ruptures_stock' => $this->stocks->filter(function($stock) {
                return $stock->quantite == 0;
            })->count(),
            'valeur_totale_stock' => $this->stocks->sum(function($stock) {
                return $stock->quantite * $stock->product->prix_achat;
            }),
            'quantite_totale' => $this->stocks->sum('quantite'),
            'seuil_moyen' => $this->stocks->avg('seuil')
        ];

        return array_merge($baseStats, $stocksStats, [
            'filename' => $this->generateFilename('stocks', 'csv')
        ]);
    }

    /**
     * Obtenir les stocks par statut
     */
    public function getStocksByStatus()
    {
        return [
            'normal' => $this->stocks->filter(function($stock) {
                return $stock->quantite > $stock->seuil;
            }),
            'faible' => $this->stocks->filter(function($stock) {
                return $stock->quantite <= $stock->seuil && $stock->quantite > 0;
            }),
            'rupture' => $this->stocks->filter(function($stock) {
                return $stock->quantite == 0;
            })
        ];
    }
}
