<?php

namespace App\Http\Exports;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductsPdfExport
{
    protected $products;
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
        $this->loadProducts();
    }

    /**
     * Charger les produits avec filtres
     */
    protected function loadProducts()
    {
        $query = Product::query();

        // Appliquer la logique de recherche
        if ($this->search) {
            $query->where(function($q) {
                $q->where('sku', 'LIKE', "%{$this->search}%")
                  ->orWhere('nom', 'LIKE', "%{$this->search}%")
                  ->orWhere('categorie', 'LIKE', "%{$this->search}%");
            });
        }

        $this->products = $query->latest()->get();
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
        return [
            'products' => $this->products,
            'stats' => $this->getStats(),
            'config' => $this->getPdfConfig()
        ];
    }

    /**
     * Générer et télécharger le PDF
     */
    public function download()
    {
        $data = $this->getViewData();
        
        // Créer le PDF avec DomPDF
        $pdf = Pdf::loadView('exports.products.pdf', $data);
        
        // Configuration du PDF
        $config = $this->getPdfConfig();
        $pdf->setPaper($config['paper'], $config['orientation']);
        
        $filename = 'produits_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Obtenir les statistiques d'exportation
     */
    public function getStats()
    {
        return [
            'total_products' => $this->products->count(),
            'search_term' => $this->search,
            'export_date' => now()->format('d/m/Y H:i:s'),
            'filename' => 'produits_' . date('Y-m-d_H-i-s') . '.pdf',
            'categories' => $this->products->groupBy('categorie')->map->count(),
            'price_range' => [
                'min' => $this->products->min('prix_achat'),
                'max' => $this->products->max('prix_achat'),
                'avg' => $this->products->avg('prix_achat')
            ]
        ];
    }

    /**
     * Prévisualiser le PDF (pour debug)
     */
    public function preview()
    {
        $data = $this->getViewData();
        return view('exports.products.pdf', $data);
    }
}
