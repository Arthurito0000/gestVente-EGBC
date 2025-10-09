<?php

namespace App\Http\Exports;

use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf;

class InventaireExport
{
    protected $stocks;
    protected $search;
    protected $filters;
    protected $data;

    public function __construct($search = null, $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
        $this->loadData();
    }

    protected function loadData()
    {
        $query = Stock::with('product');
        
        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('sku', 'LIKE', "%{$this->search}%")
                  ->orWhere('nom', 'LIKE', "%{$this->search}%")
                  ->orWhere('categorie', 'LIKE', "%{$this->search}%");
            });
        }

        // Appliquer les filtres si nécessaire
        if (isset($this->filters['stock_faible']) && $this->filters['stock_faible']) {
            $query->whereRaw('quantite <= seuil');
        }

        if (isset($this->filters['stock_zero']) && $this->filters['stock_zero']) {
            $query->where('quantite', 0);
        }

        if (isset($this->filters['seuil_min']) && $this->filters['seuil_min']) {
            $query->where('seuil', '>=', $this->filters['seuil_min']);
        }

        $this->stocks = $query->orderBy('product_id')->get();
        $this->data = $this->stocks;
    }

    public function getPdfConfig()
    {
        return [
            'paper' => 'A4',
            'orientation' => 'portrait',
            'margin' => [
                'top' => 15,
                'right' => 10,
                'bottom' => 15,
                'left' => 10
            ]
        ];
    }

    public function getViewData()
    {
        $stats = $this->getStats();
        
        return [
            'stocks' => $this->stocks,
            'stats' => $stats,
            'config' => $this->getPdfConfig(),
            'search' => $this->search,
            'date_inventaire' => now()->format('d/m/Y')
        ];
    }

    public function download()
    {
        $this->loadData();
        
        $data = $this->getViewData();
        $config = $this->getPdfConfig();
        
        $pdf = Pdf::loadView('exports.inventaire.pdf', $data)
                  ->setPaper($config['paper'], $config['orientation'])
                  ->setOptions([
                      'margin_top' => $config['margin']['top'],
                      'margin_right' => $config['margin']['right'],
                      'margin_bottom' => $config['margin']['bottom'],
                      'margin_left' => $config['margin']['left'],
                      'enable_php' => true
                  ]);

        $filename = 'inventaire_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function preview()
    {
        $this->loadData();
        $data = $this->getViewData();
        return view('exports.inventaire.pdf', $data);
    }

    public function getStats()
    {
        if (!$this->data) {
            $this->loadData();
        }

        $totalProduits = $this->stocks->count();
        $totalQuantite = $this->stocks->sum('quantite');
        
        return [
            'total_produits' => $totalProduits,
            'total_quantite' => $totalQuantite,
            'date_generation' => now()->format('d/m/Y à H:i'),
            'recherche' => $this->search ?: 'Tous les produits'
        ];
    }
}
