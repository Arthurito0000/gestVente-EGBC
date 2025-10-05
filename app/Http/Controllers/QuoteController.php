<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with(['user', 'items'])->latest();

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_devis', 'like', "%{$search}%")
                  ->orWhere('client_nom', 'like', "%{$search}%")
                  ->orWhere('objet', 'like', "%{$search}%");
            });
        }

        // Si vendeur, voir uniquement ses devis
        if (auth()->user()->hasRole('Vendeur')) {
            $query->where('user_id', auth()->id());
        }

        $quotes = $query->paginate(10);

        if ($request->ajax()) {
            return view('quotes.partials.table', compact('quotes'))->render();
        }

        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $products = Product::with('stock')->orderBy('nom')->get();
        return view('quotes.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'objet' => 'nullable|string',
            'date_devis' => 'required|date',
            'main_oeuvre' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.designation' => 'required|string',
            'items.*.quantite' => 'required|integer|min:1',
            'items.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Créer le devis
            $quote = Quote::create([
                'numero_devis' => Quote::generateNumeroDevis(),
                'user_id' => auth()->id(),
                'client_nom' => $validated['client_nom'],
                'objet' => $validated['objet'],
                'date_devis' => $validated['date_devis'],
            ]);

            // Ajouter les items
            $totalMateriel = 0;

            foreach ($validated['items'] as $item) {
                $prixTotal = $item['quantite'] * $item['prix_unitaire'];

                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'designation' => $item['designation'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'prix_total' => $prixTotal,
                ]);

                $totalMateriel += $prixTotal;
            }

            $mainOeuvre = $validated['main_oeuvre'] ?? 0;

            // Mettre à jour les totaux
            $quote->update([
                'total_materiel' => $totalMateriel,
                'main_oeuvre' => $mainOeuvre,
                'total_general' => $totalMateriel + $mainOeuvre,
            ]);

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Devis créé avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du devis : ' . $e->getMessage());
        }
    }

    public function show(Quote $quote)
    {
        $quote->load(['user', 'items.product']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $products = Product::with('stock')->orderBy('nom')->get();
        $quote->load('items');
        
        return view('quotes.edit', compact('quote', 'products'));
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'client_nom' => 'required|string|max:255',
            'objet' => 'nullable|string',
            'date_devis' => 'required|date',
            'main_oeuvre' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.designation' => 'required|string',
            'items.*.quantite' => 'required|integer|min:1',
            'items.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Supprimer les anciens items
            $quote->items()->delete();

            // Ajouter les nouveaux items
            $totalMateriel = 0;

            foreach ($validated['items'] as $item) {
                $prixTotal = $item['quantite'] * $item['prix_unitaire'];

                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'designation' => $item['designation'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'prix_total' => $prixTotal,
                ]);

                $totalMateriel += $prixTotal;
            }

            $mainOeuvre = $validated['main_oeuvre'] ?? 0;

            // Mettre à jour le devis et les totaux
            $quote->update([
                'client_nom' => $validated['client_nom'],
                'objet' => $validated['objet'],
                'date_devis' => $validated['date_devis'],
                'total_materiel' => $totalMateriel,
                'main_oeuvre' => $mainOeuvre,
                'total_general' => $totalMateriel + $mainOeuvre,
            ]);

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Devis modifié avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la modification du devis : ' . $e->getMessage());
        }
    }

    public function destroy(Quote $quote)
    {
        try {
            $quote->delete();
            return redirect()->route('quotes.index')
                ->with('success', 'Devis supprimé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du devis : ' . $e->getMessage());
        }
    }

    public function print(Quote $quote)
    {
        $quote->load(['user', 'items.product']);
        return view('quotes.print', compact('quote'));
    }

    public function downloadPdf(Quote $quote)
    {
        $quote->load(['user', 'items.product']);
        
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("devis-{$quote->numero_devis}.pdf");
    }

    public function convertToSale(Quote $quote)
    {
        // Préparer les données du devis pour pré-remplir le formulaire de facture
        $quoteData = [
            'quote_id' => $quote->id,
            'quote_numero' => $quote->numero_devis,
            'client_nom' => $quote->client_nom,
            'date_devis' => $quote->date_devis->format('Y-m-d'),
            'items' => $quote->items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'designation' => $item->designation,
                    'quantite' => $item->quantite,
                    'prix_unitaire' => $item->prix_unitaire,
                    'prix_total' => $item->prix_total,
                ];
            })->toArray(),
            'total_amount' => $quote->total_general
        ];

        // Stocker dans la session
        session(['quote_to_convert' => $quoteData]);

        return redirect()->route('invoices.create')
            ->with('info', "Formulaire de facture pré-rempli depuis le devis {$quote->numero_devis}. Ajoutez le téléphone du client et validez.");
    }

    public function duplicate(Quote $quote)
    {
        DB::beginTransaction();
        try {
            $newQuote = $quote->replicate();
            $newQuote->numero_devis = Quote::generateNumeroDevis();
            $newQuote->date_devis = now();
            $newQuote->save();

            foreach ($quote->items as $item) {
                $newItem = $item->replicate();
                $newItem->quote_id = $newQuote->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('quotes.edit', $newQuote)
                ->with('success', 'Devis dupliqué avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
        }
    }
}
