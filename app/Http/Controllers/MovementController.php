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
            'product_id' => 'required',
            'type' => 'required',
            'quantite' => 'required',
            'motif' => 'required',
            'date' => 'required',
        ]);

        $movement = new Movement($request->all());
        $movement->save();

        $stock = Stock::where('product_id', $request->product_id)->first();
            $stock->quantite += $request->quantite;
        $stock->save();

        return redirect()->route('movements.index')->with('success', 'Mouvement créé avec succès.');

        
    }
}
