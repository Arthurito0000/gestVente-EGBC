<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku|max:255',
            'nom' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'categorie' => 'nullable|string|exists:categories,name',
            'quantite' => 'required|integer|min:0',
            'seuil_stock' => 'required|integer|min:0'
        ]);

        // Créer le produit
        $product = Product::create($request->except('quantite'));

        // Créer le stock avec la quantité initiale
        Stock::create([
            'product_id' => $product->id,
            'quantite' => $request->quantite,
            'seuil' => $request->seuil_stock
        ]);

        // Créer le mouvement de stock initial
        Movement::create([
            'product_id' => $product->id,
            'type' => 'ENTREE',
            'quantite' => $request->quantite,
            'motif' => 'Stock initial',
            'date' => now()
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id . '|max:255',
            'nom' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'categorie' => 'nullable|string|exists:categories,name',
            'quantite' => 'required|integer|min:0',
            'seuil_stock' => 'required|integer|min:0'
        ]);

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
