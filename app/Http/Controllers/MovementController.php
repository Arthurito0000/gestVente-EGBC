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

        return view('movements.index', ['movements'=>$movements,'page'=>'Liste des mouvements']);
    }

    public function create()
    {
        $products = Product::all();
        return view('movements.create', ['products'=>$products,'page'=>'Ajout d\'un mouvement']);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:ENTREE,SORTIE,AJUSTEMENT',
            'ajustement_type' => 'required_if:type,AJUSTEMENT|in:AUGMENTATIF,DIMINUTIF',
            'quantite' => 'required|integer|min:1',
            'prix_achat' => 'nullable|numeric|min:0',
            'motif' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // Créer le mouvement
        $movementData = $request->all();
        
        // Pour les ajustements, on enrichit le motif avec le type d'ajustement
        if ($request->type === 'AJUSTEMENT') {
            $ajustementType = $request->ajustement_type === 'AUGMENTATIF' ? 'Ajustement augmentatif' : 'Ajustement diminutif';
            $movementData['motif'] = $ajustementType . ' - ' . $request->motif;
        }
        
        $movement = Movement::create($movementData);

        // Mettre à jour le stock
        $stock = Stock::where('product_id', $request->product_id)->first();
        if ($stock) {
            if ($request->type === 'ENTREE') {
                $stock->quantite += $request->quantite;
            } elseif ($request->type === 'SORTIE') {
                $stock->quantite -= $request->quantite;
                // S'assurer que le stock ne devient pas négatif
                $stock->quantite = max(0, $stock->quantite);
            } elseif ($request->type === 'AJUSTEMENT') {
                // Pour les ajustements, on applique selon le type
                if ($request->ajustement_type === 'AUGMENTATIF') {
                    $stock->quantite += $request->quantite;
                } else { // DIMINUTIF
                    $stock->quantite -= $request->quantite;
                    $stock->quantite = max(0, $stock->quantite);
                }
            }
            $stock->save();
        }

        // Si un prix d'achat est fourni ET que ce n'est pas un ajustement, mettre à jour le produit
        if ($request->filled('prix_achat') && $request->type !== 'AJUSTEMENT') {
            $product = Product::find($request->product_id);
            if ($product) {
                $ancienPrix = $product->prix_achat;
                $product->prix_achat = $request->prix_achat;
                $product->save();
                
                // Ajouter un message informatif sur la mise à jour du prix
                $message = 'Mouvement créé avec succès.';
                if ($ancienPrix != $request->prix_achat) {
                    $message .= " Prix d'achat mis à jour : {$ancienPrix} Fcfa → {$request->prix_achat} Fcfa";
                }
                
                return redirect()->route('movements.index')->with('success', $message);
            }
        }

        return redirect()->route('movements.index')->with('success', 'Mouvement créé avec succès.');
    }
}
