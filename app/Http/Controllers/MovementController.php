<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movement;
use App\Models\Stock;
use App\Models\Product;

class MovementController extends Controller
{
    public function index()
    {
        $movements = Movement::with('product')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('movements.index', ['movements' => $movements, 'page' => 'Liste des mouvements']);
    }

    public function create()
    {
        $products = Product::all();
        return view('movements.create', ['products' => $products, 'page' => 'Ajout d\'un mouvement']);
    }

    public function store(Request $request)
    {
        // 🔴 Validation simplifiée : quantite arrive déjà en décimal du JavaScript
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:ENTREE,SORTIE,AJUSTEMENT',
            'ajustement_type' => 'required_if:type,AJUSTEMENT|in:AUGMENTATIF,DIMINUTIF',
            'quantite' => 'required|numeric|min:0.001',
            'prix_achat' => 'nullable|numeric|min:0',
            'motif' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        try {
            // 🔴 Plus besoin de convertir, la quantité arrive déjà en décimal
            $quantiteDecimal = floatval($request->quantite);
            
            // Préparer les données du mouvement
            $movementData = [
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantite' => $quantiteDecimal,
                'motif' => $request->motif,
                'date' => $request->date,
            ];
            
            // Ajouter ajustement_type seulement si c'est un ajustement
            if ($request->type === 'AJUSTEMENT' && $request->ajustement_type) {
                $movementData['ajustement_type'] = $request->ajustement_type;
                
                // Enrichir le motif
                $ajustementType = $request->ajustement_type === 'AUGMENTATIF' 
                    ? 'Ajustement augmentatif' 
                    : 'Ajustement diminutif';
                $movementData['motif'] = $ajustementType . ' - ' . $request->motif;
            }
            
            // Ajouter prix_achat seulement s'il est fourni
            if ($request->filled('prix_achat')) {
                $movementData['prix_achat'] = $request->prix_achat;
            }
            
            // Créer le mouvement
            Movement::create($movementData);

            // Mettre à jour le stock
            $stock = Stock::where('product_id', $request->product_id)->first();
            
            if ($stock) {
                // Stock existant : mettre à jour
                if ($request->type === 'ENTREE') {
                    $stock->quantite += $quantiteDecimal;
                } elseif ($request->type === 'SORTIE') {
                    $stock->quantite -= $quantiteDecimal;
                    $stock->quantite = max(0, $stock->quantite);
                } elseif ($request->type === 'AJUSTEMENT') {
                    if ($request->ajustement_type === 'AUGMENTATIF') {
                        $stock->quantite += $quantiteDecimal;
                    } else {
                        $stock->quantite -= $quantiteDecimal;
                        $stock->quantite = max(0, $stock->quantite);
                    }
                }
                $stock->save();
            } else {
                // Créer le stock s'il n'existe pas
                $initialQuantite = 0;
                
                if ($request->type === 'ENTREE') {
                    $initialQuantite = $quantiteDecimal;
                } elseif ($request->type === 'AJUSTEMENT' && $request->ajustement_type === 'AUGMENTATIF') {
                    $initialQuantite = $quantiteDecimal;
                }
                
                Stock::create([
                    'product_id' => $request->product_id,
                    'quantite' => $initialQuantite
                ]);
            }

            // Mise à jour du prix d'achat si fourni (sauf pour les ajustements)
            if ($request->filled('prix_achat') && $request->type !== 'AJUSTEMENT') {
                $product = Product::find($request->product_id);
                
                if ($product) {
                    $ancienPrix = $product->prix_achat;
                    $product->prix_achat = $request->prix_achat;
                    $product->save();
                    
                    $message = 'Mouvement créé avec succès.';
                    
                    if ($ancienPrix != $request->prix_achat) {
                        $message .= " Prix d'achat mis à jour : " 
                            . number_format($ancienPrix, 0, ',', ' ') . " Fcfa → " 
                            . number_format($request->prix_achat, 0, ',', ' ') . " Fcfa";
                    }
                    
                    return redirect()->route('movements.index')->with('success', $message);
                }
            }

            return redirect()->route('movements.index')->with('success', 'Mouvement créé avec succès.');
            
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création du mouvement : ' . $e->getMessage()])
                ->withInput();
        }
    }
}