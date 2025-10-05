<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $invoices = Invoice::when($search, function ($query, $search) {
                return $query->where('client_name', 'like', "%{$search}%")
                           ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        
        // Conserver les paramètres de recherche dans la pagination
        $invoices->appends($request->query());

        if ($request->ajax()) {
            return view('invoices.partials.table', compact('invoices'))->render();
        }

        return view('invoices.index', compact('invoices', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        
        // Vérifier s'il y a des données de devis à convertir
        $quoteData = session('quote_to_convert');
        
        return view('invoices.create', compact('products', 'quoteData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        DB::beginTransaction();
    
        try {

            $invoice_number =  Invoice::count() + 1;
            // Save invoice
            $invoice = Invoice::create([
                'code'            => 'INV-' . (Invoice::count() + 1),
                'invoice_number'  => $invoice_number,
                'invoice_date'    => $request->invoice_date,
                'vendor_name'     => $request->vendor_name,
                'client_name'     => $request->client_name,
                'client_phone'    => $request->client_phone,
                'client_location' => $request->client_location,
                'currency'        => $request->currency ?? 'FCFA',
                'total_amount'    => $request->total_amount,
            ]);
    
            // Save products and update stock safely
            foreach ($request->lines as $line) {
                // Vérifier le stock avant de créer la ligne de facture
                $product = Product::with('stock')->lockForUpdate()->findOrFail($line['product_id']);
                
                if (!$product->stock) {
                    throw new \Exception("Aucun stock trouvé pour le produit {$product->nom}");
                }
                
                $stockDisponible = $product->stock->quantite;
                $quantiteDemandee = $line['quantity'];
                
                if ($quantiteDemandee > $stockDisponible) {
                    throw new \Exception(
                        "❌ FACTURE REFUSÉE - Stock insuffisant pour {$product->nom} (SKU: {$product->sku}). " .
                        "Quantité demandée: {$quantiteDemandee}, Stock disponible: {$stockDisponible}"
                    );
                }
                
                $nouveauStock = $stockDisponible - $quantiteDemandee;
                
                if ($nouveauStock < 0) {
                    throw new \Exception("❌ ERREUR SYSTÈME - Le stock ne peut pas devenir négatif !");
                }
                
                // Créer la ligne de facture
                $invoice->products()->attach($line['product_id'], [
                    'quantity'    => $line['quantity'],
                    'unit_price'  => $line['unit_price'],
                    'total_price' => $line['total_price'],
                ]);
    
                // Mettre à jour le stock de manière sécurisée
                $product->stock->update(['quantite' => $nouveauStock]);
                
                // Vérification post-mise à jour
                $product->stock->refresh();
                if ($product->stock->quantite < 0) {
                    throw new \Exception("❌ ERREUR CRITIQUE - Le stock est devenu négatif après la mise à jour !");
                }
            }
    
            DB::commit();

            // Créer les mouvements de stock et les ventes pour le dashboard
            foreach ($request->lines as $line) {
                // Créer le mouvement de stock
                Movement::create([
                    'product_id' => $line['product_id'],
                    'type' => 'SORTIE',
                    'quantite' => $line['quantity'],
                    'motif' => 'Vente - Facture ' . $invoice->code,
                    'date' => now()
                ]);

                // Créer l'entrée de vente pour les statistiques du dashboard
                Sale::create([
                    'product_id' => $line['product_id'],
                    'user_id' => auth()->id(),
                    'quantite' => $line['quantity'],
                    'prix_unitaire' => $line['unit_price'],
                    'total' => $line['total_price'],
                    'date_vente' => $request->invoice_date,
                    'numero_facture' => $invoice->code,
                    'notes' => 'Vente via facture ' . $invoice->code
                ]);
            }
            Log::info('Transaction committed successfully');
    
            // 🔴 BUG FIX A : Nettoyer la session après création de la facture
            session()->forget('quote_to_convert');
            
            // Load relations
            $invoice->load('products');
    
            // Rediriger vers la page d'impression au lieu de générer un PDF
            return redirect()->route('invoices.print', $invoice->id)->with('success', 'Facture créée avec succès ! Vous pouvez maintenant l\'imprimer.');
    
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PDF generation error', ['error' => $e->getMessage()]);
    
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
        }
    }
    
    

    /**
     * Afficher la page d'impression de la facture
     */
    public function print(string $id)
    {
        $invoice = Invoice::with('products')->findOrFail($id);
        return view('invoices.partials.invoice', compact('invoice'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('invoices.detail', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $filename = 'Facture_' . $invoice->invoice_number ?? $invoice->id . '.pdf';
        $path = storage_path('app/public/invoices/' . $filename);
        if (file_exists($path)) {
            unlink($path);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Facture supprimée avec succès');
    }

    /**
     * Download an existing invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        try {
            $invoice->load('products');

            $html = view('invoices.partials.invoice', compact('invoice'))->render();
            
            // Ensure temp dir exists for mPDF on Windows
            $tempPath = storage_path('app/mpdf-temp');
            if (!is_dir($tempPath)) { @mkdir($tempPath, 0775, true); }

            $mpdf = new Mpdf([
                'default_font' => 'dejavusans',
                'tempDir' => $tempPath,
            ]);
            $mpdf->WriteHTML($html);

            $filename = 'Facture_' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';
            $pdfContent = $mpdf->Output('', 'S');
            $length = function_exists('mb_strlen') ? mb_strlen($pdfContent, '8bit') : strlen($pdfContent);
            if (ob_get_length()) { @ob_end_clean(); }

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => (string) $length,
                'Cache-Control' => 'private, must-revalidate, max-age=0',
                'Pragma' => 'public',
            ]);
        } catch (\Throwable $e) {
            Log::error('Invoice download error', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Impossible de générer le PDF: ' . $e->getMessage()]);
        }
    }
}
