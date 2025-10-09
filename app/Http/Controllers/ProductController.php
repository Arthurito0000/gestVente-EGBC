<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('categorie', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10);
        
        // Conserver les paramètres de recherche dans la pagination
        $products->appends($request->query());

        // Si c'est une requête AJAX, retourner seulement le contenu de la table
        if ($request->ajax()) {
            return view('products.partials.table', compact('products'))->render();
        }

        return view('products.index', [
            'products' => $products,
            'page' => 'Liste des produits',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', ['categories'=>$categories,'page'=>'Ajout d\'un produit']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(Product::rules());

        try {
            DB::transaction(function () use ($request) {
                // Créer le produit (sans quantite et seuil_stock)
                $product = Product::create($request->except(['quantite', 'seuil_stock']));

                // Créer le stock avec la quantité initiale
                Stock::create([
                    'product_id' => $product->id,
                    'quantite' => $request->quantite,
                    'seuil' => $request->seuil_stock
                ]);

                // Créer le mouvement de stock initial si quantité > 0
                if ($request->quantite > 0) {
                    Movement::create([
                        'product_id' => $product->id,
                        'type' => 'ENTREE',
                        'quantite' => $request->quantite,
                        'motif' => 'Stock initial',
                        'date' => now()
                    ]);
                }
            });

            return redirect()->route('products.index')
                ->with('success', '✅ Produit créé avec succès !');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Erreur lors de la création du produit : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show',['product'=>$product,'page'=>'Détails du produit']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', ['product'=>$product,'categories'=>$categories,'page'=>'Modification du produit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate(Product::rules($product->id));

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produit modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

}
