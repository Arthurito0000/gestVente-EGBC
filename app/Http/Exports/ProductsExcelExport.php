<?php

namespace App\Http\Exports;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsExcelExport
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
     * Définir les en-têtes des colonnes
     */
    public function headings(): array
    {
        return [
            'Code SKU',
            'Nom du Produit', 
            'Prix Achat',
            'Devise',
            'Catégorie',
            'Seuil Stock',
            'Date Création'
        ];
    }

    /**
     * Définir les données à exporter
     */
    public function collection()
    {
        return $this->products->map(function($product) {
            return [
                'sku' => $product->sku,
                'nom' => $product->nom,
                'prix_achat' => number_format($product->prix_achat, 2, '.', ''),
                'devise' => 'Fcfa',
                'categorie' => $product->categorie ?: 'Non définie',
                'seuil_stock' => $product->seuil_stock,
                'created_at' => $product->created_at->format('d/m/Y')
            ];
        });
    }

    /**
     * Générer le fichier CSV
     */
    public function download()
    {
        $filename = 'produits_' . date('Y-m-d_H-i-s') . '.csv';
        
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
        return [
            'total_products' => $this->products->count(),
            'search_term' => $this->search,
            'export_date' => now()->format('d/m/Y H:i:s'),
            'filename' => 'produits_' . date('Y-m-d_H-i-s') . '.csv'
        ];
    }
}
