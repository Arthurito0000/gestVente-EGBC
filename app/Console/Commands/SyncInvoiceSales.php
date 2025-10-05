<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Console\Command;

class SyncInvoiceSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:invoice-sales {--invoice-code= : Code de la facture spécifique à synchroniser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les factures existantes avec la table des ventes pour les statistiques du dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceCode = $this->option('invoice-code');
        
        if ($invoiceCode) {
            // Synchroniser une facture spécifique
            $invoice = Invoice::where('code', $invoiceCode)->with('products')->first();
            
            if (!$invoice) {
                $this->error("Facture {$invoiceCode} non trouvée");
                return 1;
            }
            
            $this->syncInvoice($invoice);
        } else {
            // Synchroniser toutes les factures
            $invoices = Invoice::with('products')->get();
            
            if ($invoices->isEmpty()) {
                $this->info('Aucune facture à synchroniser');
                return 0;
            }
            
            $this->info("Synchronisation de {$invoices->count()} facture(s)...");
            
            foreach ($invoices as $invoice) {
                $this->syncInvoice($invoice);
            }
        }
        
        $this->info('🎉 Synchronisation terminée !');
        return 0;
    }
    
    private function syncInvoice(Invoice $invoice)
    {
        $this->line("Synchronisation de la facture {$invoice->code}");
        $this->line("Date: {$invoice->invoice_date}");
        $this->line("Montant total: {$invoice->total_amount} FCFA");
        
        $salesCreated = 0;
        $salesSkipped = 0;
        
        foreach ($invoice->products as $product) {
            // Vérifier si une vente existe déjà pour cette facture et ce produit
            $existingSale = Sale::where('numero_facture', $invoice->code)
                               ->where('product_id', $product->id)
                               ->first();
            
            if ($existingSale) {
                $salesSkipped++;
                continue;
            }
            
            // Créer l'entrée de vente
            Sale::create([
                'product_id' => $product->id,
                'user_id' => 1, // Admin par défaut
                'quantite' => $product->pivot->quantity,
                'prix_unitaire' => $product->pivot->unit_price,
                'total' => $product->pivot->total_price,
                'date_vente' => $invoice->invoice_date,
                'numero_facture' => $invoice->code,
                'notes' => 'Vente via facture ' . $invoice->code . ' (synchronisation)'
            ]);
            
            $salesCreated++;
        }
        
        $this->line("  ✅ {$salesCreated} vente(s) créée(s), {$salesSkipped} ignorée(s)");
    }
}
