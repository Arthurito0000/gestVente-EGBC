<?php

namespace App\Http\Exports;

use App\Models\Stock;
use App\Http\Exports\BaseExport;
use Barryvdh\DomPDF\Facade\Pdf;

class StocksPdfExport extends BaseExport
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
     * Configuration du PDF
     */
    public function getPdfConfig()
    {
        return [
            'paper' => 'A4',
            'orientation' => 'landscape',
            'margin' => [
                'top' => 15,
                'right' => 15,
                'bottom' => 15,
                'left' => 15
            ]
        ];
    }

    /**
     * Données pour la vue PDF
     */
    public function getViewData()
    {
        $stocksByStatus = $this->getStocksByStatus();
        
        return [
            'stocks' => $this->stocks,
            'stats' => $this->getStats(),
            'config' => $this->getPdfConfig(),
            'stocks_by_status' => $stocksByStatus
        ];
    }

    /**
     * Générer et télécharger le PDF
     */
    public function download()
    {
        $data = $this->getViewData();
        
        // Créer le PDF avec DomPDF
        $pdf = Pdf::loadView('exports.stocks.pdf', $data);
        
        // Configuration du PDF
        $config = $this->getPdfConfig();
        $pdf->setPaper($config['paper'], $config['orientation']);
        
        $filename = $this->generateFilename('stocks', 'pdf');
        
        return $pdf->download($filename);
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
            'seuil_moyen' => $this->stocks->avg('seuil'),
            'categories' => $this->stocks->groupBy('product.categorie')->map->count(),
            'valeur_range' => [
                'min' => $this->stocks->min(function($stock) {
                    return $stock->quantite * $stock->product->prix_achat;
                }),
                'max' => $this->stocks->max(function($stock) {
                    return $stock->quantite * $stock->product->prix_achat;
                }),
                'avg' => $this->stocks->avg(function($stock) {
                    return $stock->quantite * $stock->product->prix_achat;
                })
            ]
        ];

        return array_merge($baseStats, $stocksStats, [
            'filename' => $this->generateFilename('stocks', 'pdf')
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

    /**
     * Prévisualiser le PDF (pour debug)
     */
    public function preview()
    {
        $data = $this->getViewData();
        return view('exports.stocks.pdf', $data);
    }
}
