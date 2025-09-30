<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    private $saleData;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['product', 'user'])
            ->orderBy('date_vente', 'desc')
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('stock')
            ->whereHas('stock', function($query) {
                $query->where('quantite', '>', 0);
            })
            ->orderBy('nom')
            ->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // VÉRIFICATION CRITIQUE : Récupérer le stock avec verrou pour éviter les race conditions
                $product = Product::with('stock')->lockForUpdate()->findOrFail($request->product_id);
                
                if (!$product->stock) {
                    throw new \Exception("Aucun stock trouvé pour le produit {$product->nom}");
                }
                
                $stockDisponible = $product->stock->quantite;
                
                // VALIDATION STRICTE : Impossible de vendre plus que le stock
                if ($request->quantite > $stockDisponible) {
                    throw new \Exception(
                        "❌ VENTE REFUSÉE - Stock insuffisant pour {$product->nom} (SKU: {$product->sku}). " .
                        "Quantité demandée: {$request->quantite}, Stock disponible: {$stockDisponible}"
                    );
                }
                
                // Calculer le nouveau stock
                $nouveauStock = $stockDisponible - $request->quantite;
                
                // SÉCURITÉ ABSOLUE : Le stock ne peut JAMAIS être négatif
                if ($nouveauStock < 0) {
                    throw new \Exception(
                        "❌ ERREUR SYSTÈME - Le stock ne peut pas devenir négatif ! " .
                        "Stock actuel: {$stockDisponible}, Quantité demandée: {$request->quantite}"
                    );
                }

                // Créer la vente
                $sale = Sale::create([
                    'product_id' => $request->product_id,
                    'user_id' => auth()->id(),
                    'quantite' => $request->quantite,
                    'prix_unitaire' => $request->prix_unitaire,
                    'total' => $request->quantite * $request->prix_unitaire,
                    'date_vente' => now()->format('Y-m-d'),
                    'numero_facture' => $this->generateInvoiceNumber(),
                    'notes' => $request->notes,
                ]);

                // Mettre à jour le stock de manière sécurisée
                $product->stock->update([
                    'quantite' => $nouveauStock
                ]);

                // Vérifier que la mise à jour a bien fonctionné
                $product->stock->refresh();
                if ($product->stock->quantite < 0) {
                    throw new \Exception("❌ ERREUR CRITIQUE - Le stock est devenu négatif après la mise à jour !");
                }

                // Créer un mouvement de sortie
                Movement::create([
                    'product_id' => $request->product_id,
                    'type' => 'SORTIE',
                    'quantite' => $request->quantite,
                    'motif' => "Vente - Facture {$sale->numero_facture}",
                    'date' => now(),
                ]);
                
                // Stocker les infos pour les messages
                $this->saleData = [
                    'sale' => $sale,
                    'product' => $product,
                    'nouveauStock' => $nouveauStock,
                    'seuil' => $product->stock->seuil ?? 0
                ];
            });
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        // Messages de succès avec alertes stock si nécessaire
        $message = "✅ Vente enregistrée avec succès !";
        
        if (isset($this->saleData)) {
            $nouveauStock = $this->saleData['nouveauStock'];
            $product = $this->saleData['product'];
            $seuil = $this->saleData['seuil'];
            
            if ($nouveauStock == 0) {
                $message .= " ⚠️ ATTENTION: Le produit {$product->nom} est maintenant en rupture de stock.";
            } elseif ($nouveauStock <= $seuil) {
                $message .= " ⚠️ ATTENTION: Le stock du produit {$product->nom} est maintenant faible ({$nouveauStock} restants, seuil: {$seuil}).";
            }
        }

        return redirect()->route('sales.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['product', 'user']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $products = Product::with('stock')->orderBy('nom')->get();
        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                // Récupérer l'ancien produit avec verrou
                $ancienProduit = Product::with('stock')->lockForUpdate()->findOrFail($sale->product_id);
                $ancienneQuantite = $sale->quantite;

                // Récupérer le nouveau produit avec verrou
                $nouveauProduit = Product::with('stock')->lockForUpdate()->findOrFail($request->product_id);

                if (!$ancienProduit->stock || !$nouveauProduit->stock) {
                    throw new \Exception("Stock non trouvé pour un des produits");
                }

                // Calculer le stock disponible du nouveau produit (en tenant compte de la libération)
                $stockDisponible = $nouveauProduit->stock->quantite;
                if ($ancienProduit->id == $nouveauProduit->id) {
                    // Même produit : on libère l'ancienne quantité
                    $stockDisponible += $ancienneQuantite;
                }

                // Vérifier que la nouvelle quantité est disponible
                if ($request->quantite > $stockDisponible) {
                    throw new \Exception(
                        "❌ MODIFICATION REFUSÉE - Stock insuffisant pour {$nouveauProduit->nom} (SKU: {$nouveauProduit->sku}). " .
                        "Quantité demandée: {$request->quantite}, Stock disponible: {$stockDisponible}"
                    );
                }

                // Calculer les nouveaux stocks
                $nouveauStockAncien = $ancienProduit->stock->quantite + $ancienneQuantite;
                $nouveauStockNouveau = $stockDisponible - $request->quantite;

                // Vérifications de sécurité
                if ($nouveauStockAncien < 0 || $nouveauStockNouveau < 0) {
                    throw new \Exception("❌ ERREUR SYSTÈME - Un stock deviendrait négatif !");
                }

                // Mettre à jour la vente
                $sale->update([
                    'product_id' => $request->product_id,
                    'quantite' => $request->quantite,
                    'prix_unitaire' => $request->prix_unitaire,
                    'total' => $request->quantite * $request->prix_unitaire,
                    'notes' => $request->notes,
                ]);

                // Mettre à jour les stocks de manière sécurisée
                $ancienProduit->stock->update(['quantite' => $nouveauStockAncien]);
                
                if ($ancienProduit->id != $nouveauProduit->id) {
                    $nouveauProduit->stock->update(['quantite' => $nouveauStockNouveau]);
                } else {
                    // Même produit : le stock final est déjà calculé
                    $ancienProduit->stock->update(['quantite' => $nouveauStockNouveau]);
                }

                // Vérifications post-mise à jour
                $ancienProduit->stock->refresh();
                $nouveauProduit->stock->refresh();
                
                if ($ancienProduit->stock->quantite < 0 || $nouveauProduit->stock->quantite < 0) {
                    throw new \Exception("❌ ERREUR CRITIQUE - Un stock est devenu négatif après la mise à jour !");
                }
            });

            return redirect()->route('sales.index')->with('success', '✅ Vente modifiée avec succès !');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale) {
                // Récupérer le produit avec verrou
                $product = Product::with('stock')->lockForUpdate()->findOrFail($sale->product_id);
                
                if (!$product->stock) {
                    throw new \Exception("Stock non trouvé pour le produit {$product->nom}");
                }
                
                // Calculer le nouveau stock après restauration
                $stockActuel = $product->stock->quantite;
                $nouveauStock = $stockActuel + $sale->quantite;
                
                // Mettre à jour le stock de manière sécurisée
                $product->stock->update(['quantite' => $nouveauStock]);
                
                // Vérification post-mise à jour
                $product->stock->refresh();
                if ($product->stock->quantite < 0) {
                    throw new \Exception("❌ ERREUR CRITIQUE - Le stock est devenu négatif après la restauration !");
                }

                // Supprimer la vente
                $sale->delete();
            });

            return redirect()->route('sales.index')->with('success', '✅ Vente supprimée et stock restauré !');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * API pour vérifier le stock d'un produit
     */
    public function checkStock(Product $product)
    {
        $stock = $product->stock;
        
        return response()->json([
            'product_id' => $product->id,
            'nom' => $product->nom,
            'sku' => $product->sku,
            'prix_vente' => $product->prix_vente,
            'stock_disponible' => $stock ? $stock->quantite : 0,
            'seuil' => $stock ? $stock->seuil : 0,
            'status' => $stock && $stock->quantite > 0 ? 'disponible' : 'rupture'
        ]);
    }

    /**
     * Générer un numéro de facture unique
     */
    private function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $lastSale = Sale::whereDate('created_at', now())->latest()->first();
        $sequence = $lastSale ? (int)substr($lastSale->numero_facture, -3) + 1 : 1;
        
        return 'FAC-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
