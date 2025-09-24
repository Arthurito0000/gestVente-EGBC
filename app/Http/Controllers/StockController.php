<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the stocks with search functionality
     */
    public function index(Request $request)
    {
        $query = Stock::with('product');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('sku', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('categorie', 'LIKE', "%{$search}%");
            });
        }

        // Filtres spécifiques aux stocks
        if ($request->filled('stock_faible') && $request->stock_faible) {
            $query->whereRaw('quantite <= seuil');
        }

        if ($request->filled('stock_zero') && $request->stock_zero) {
            $query->where('quantite', 0);
        }

        $stocks = $query->latest()->paginate(10);
        
        // Conserver les paramètres de recherche dans la pagination
        $stocks->appends($request->query());

        // Si c'est une requête AJAX, retourner seulement la partie table
        if ($request->ajax()) {
            return view('stock.partials.table', compact('stocks'))->render();
        }

        return view('stock.index', [
            'stocks' => $stocks,
            'page' => 'Stocks des produits'
        ]);
    }

    /**
     * Show stock details for a specific product
     */
    public function show($product)
    {
        $stock = Stock::with('product')->where('product_id', $product)->firstOrFail();
        return view('stock.show', compact('stock'));
    }

    /**
     * Remove the specified stock from storage
     */
    public function destroy(Stock $stock)
    {
        $productName = $stock->product->nom;
        $stock->delete();

        return redirect()->route('stock.index')
            ->with('success', "Stock du produit \"{$productName}\" supprimé avec succès.");
    }
}
