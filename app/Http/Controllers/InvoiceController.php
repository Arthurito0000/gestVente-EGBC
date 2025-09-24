<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\Movement;
use App\Models\Product;
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
    public function index()
    {
        $invoices = Invoice::oldest('id')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('invoices.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        DB::beginTransaction();
    
        try {
            // Save invoice
            $invoice = Invoice::create([
                'code'            => 'INV-' . (Invoice::count() + 1),
                'invoice_number'  => $request->invoice_number,
                'invoice_date'    => $request->invoice_date,
                'vendor_name'     => $request->vendor_name,
                'client_name'     => $request->client_name,
                'client_location' => $request->client_location,
                'currency'        => $request->currency ?? 'FCFA',
                'total_amount'    => $request->total_amount,
            ]);
    
            // Save products and decrement stock
            foreach ($request->lines as $line) {
                $invoice->products()->attach($line['product_id'], [
                    'quantity'    => $line['quantity'],
                    'unit_price'  => $line['unit_price'],
                    'total_price' => $line['total_price'],
                ]);
    
                Stock::where('product_id', $line['product_id'])
                    ->decrement('quantite', $line['quantity']);
            }
    
            DB::commit();

             foreach ($request->lines as $line) {
                Movement::create([
                    'product_id' => $line['product_id'],
                    'type' => 'SORTIE',
                    'quantite' => $line['quantity'],
                    'motif' => 'Vente',
                    'date' => now()
                ]);
            }
            Log::info('Transaction committed successfully');
    
            // Load relations
            $invoice->load('products');
    
            // Render Blade view
            $html = view('invoices.partials.invoice', compact('invoice'))->render();
    
            // Ensure temporary directory exists for mPDF
            $tempPath = storage_path('app/mpdf-temp');
            if (!is_dir($tempPath)) {
                @mkdir($tempPath, 0775, true);
            }
    
            // Configure mPDF
            $mpdf = new Mpdf([
                'default_font' => 'dejavusans',
                'tempDir' => $tempPath,
            ]);
    
            $mpdf->WriteHTML($html);
    
            // Save to file
            $invoiceStoragePath = storage_path('app/public/invoices');
            if (!is_dir($invoiceStoragePath)) {
                mkdir($invoiceStoragePath, 0775, true);
            }
            
            // Save PDF to file
            $filename = 'Facture_' . $invoice->invoice_number . '.pdf';
            $path = $invoiceStoragePath . '/' . $filename;
            $mpdf->Output($path, \Mpdf\Output\Destination::FILE);
    
            // Redirect to index with download link in session
            return redirect()->route('invoices.index')->with('pdf_download', asset('storage/invoices/' . $filename));
    
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PDF generation error', ['error' => $e->getMessage()]);
    
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
        }
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
