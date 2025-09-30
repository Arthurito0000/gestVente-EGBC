<?php

namespace App\Http\Exports;

use App\Models\Stock;
use App\Http\Exports\BaseExport;
use Mpdf\Mpdf;

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
        try {
            $data = $this->getViewData();
            
            // Générer le HTML à partir de la vue
            $html = view('exports.stocks.pdf', $data)->render();
            
            // Configuration mPDF
            $config = $this->getPdfConfig();
            $mpdf = new Mpdf([
                'format' => $config['paper'],
                'orientation' => $config['orientation'] === 'landscape' ? 'L' : 'P',
                'margin_left' => $config['margin']['left'],
                'margin_right' => $config['margin']['right'],
                'margin_top' => $config['margin']['top'],
                'margin_bottom' => $config['margin']['bottom'],
                'default_font' => 'Arial'
            ]);
            
            // Écrire le HTML dans le PDF
            $mpdf->WriteHTML($html);
            
            $filename = $this->generateFilename('stocks', 'pdf');
            
            // Télécharger le PDF
            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la génération du PDF des stocks avec mPDF: ' . $e->getMessage(), 0, $e);
        }
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
     * En-têtes pour l'export (requis par BaseExport)
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Nom du produit',
            'Catégorie',
            'Quantité en stock',
            'Seuil d\'alerte',
            'Statut',
            'Valeur stock',
            'Dernière MAJ'
        ];
    }

    /**
     * Collection de données (requis par BaseExport)
     */
    public function collection()
    {
        return $this->stocks->map(function($stock) {
            $valeurStock = $stock->quantite * $stock->product->prix_achat;
            $statut = $stock->quantite == 0 ? 'Rupture' : 
                     ($stock->quantite <= $stock->seuil ? 'Stock faible' : 'Normal');
            
            return [
                'sku' => $stock->product->sku,
                'nom' => $stock->product->nom,
                'categorie' => $stock->product->categorie ?? 'Non définie',
                'quantite' => $stock->quantite,
                'seuil' => $stock->seuil,
                'statut' => $statut,
                'valeur_stock' => number_format($valeurStock, 2, ',', ' ') . ' FCFA',
                'updated_at' => $stock->updated_at->format('d/m/Y H:i')
            ];
        });
    }

    /**
     * Prévisualiser le PDF (pour debug)
     */
    public function preview()
    {
        $data = $this->getViewData();
        return view('exports.stocks.pdf', $data);
    }

    /**
     * Afficher le PDF dans le navigateur (avec mPDF)
     */
    public function viewPdf()
    {
        try {
            $data = $this->getViewData();
            
            // Générer le HTML à partir de la vue
            $html = view('exports.stocks.pdf', $data)->render();
            
            // Configuration mPDF
            $config = $this->getPdfConfig();
            $mpdf = new Mpdf([
                'format' => $config['paper'],
                'orientation' => $config['orientation'] === 'landscape' ? 'L' : 'P',
                'margin_left' => $config['margin']['left'],
                'margin_right' => $config['margin']['right'],
                'margin_top' => $config['margin']['top'],
                'margin_bottom' => $config['margin']['bottom'],
                'default_font' => 'Arial'
            ]);
            
            // Écrire le HTML dans le PDF
            $mpdf->WriteHTML($html);
            
            $filename = $this->generateFilename('stocks', 'pdf');
            
            // Afficher le PDF dans le navigateur
            return response($mpdf->Output($filename, 'I'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'affichage du PDF des stocks avec mPDF: ' . $e->getMessage(), 0, $e);
        }
    }
}
