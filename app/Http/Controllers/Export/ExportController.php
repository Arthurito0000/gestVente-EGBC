<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Http\Exports\ProductsExcelExport;
use App\Http\Exports\ProductsPdfExport;
use App\Http\Exports\StocksExcelExport;
use App\Http\Exports\StocksPdfExport;
use App\Http\Exports\InventaireExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Export products to Excel (CSV format)
     */
    public function productsExcel(Request $request)
    {
        $search = $request->get('search');
        
        $export = new ProductsExcelExport($search);
        
        return $export->download();
    }

    /**
     * Export products to PDF
     */
    public function productsPdf(Request $request)
    {
        $search = $request->get('search');
        
        $export = new ProductsPdfExport($search);
        
        return $export->download();
    }

    /**
     * Prévisualiser le PDF des produits (pour debug)
     */
    public function productsPreview(Request $request)
    {
        $search = $request->get('search');
        
        $export = new ProductsPdfExport($search);
        
        return $export->preview();
    }

    /**
     * Obtenir les statistiques d'exportation des produits
     */
    public function productsStats(Request $request)
    {
        $search = $request->get('search');
        
        $excelExport = new ProductsExcelExport($search);
        $pdfExport = new ProductsPdfExport($search);
        
        return response()->json([
            'excel' => $excelExport->getStats(),
            'pdf' => $pdfExport->getStats()
        ]);
    }

    /**
     * Export stocks to Excel (CSV format)
     */
    public function stocksExcel(Request $request)
    {
        $search = $request->get('search');
        $filters = [
            'stock_faible' => $request->get('stock_faible'),
            'stock_zero' => $request->get('stock_zero'),
            'seuil_min' => $request->get('seuil_min')
        ];
        
        $export = new StocksExcelExport($search, $filters);
        
        return $export->download();
    }

    /**
     * Export stocks to PDF
     */
    public function stocksPdf(Request $request)
    {
        try {
            $search = $request->get('search');
            $filters = [
                'stock_faible' => $request->get('stock_faible'),
                'stock_zero' => $request->get('stock_zero'),
                'seuil_min' => $request->get('seuil_min')
            ];
            
            $export = new StocksPdfExport($search, $filters);
            
            return $export->download();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'exportation PDF des stocks',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Prévisualiser le PDF des stocks (pour debug)
     */
    public function stocksPreview(Request $request)
    {
        $search = $request->get('search');
        $filters = [
            'stock_faible' => $request->get('stock_faible'),
            'stock_zero' => $request->get('stock_zero'),
            'seuil_min' => $request->get('seuil_min')
        ];
        
        $export = new StocksPdfExport($search, $filters);
        
        return $export->preview();
    }

    /**
     * Obtenir les statistiques d'exportation des stocks
     */
    public function stocksStats(Request $request)
    {
        $search = $request->get('search');
        $filters = [
            'stock_faible' => $request->get('stock_faible'),
            'stock_zero' => $request->get('stock_zero'),
            'seuil_min' => $request->get('seuil_min')
        ];
        
        $excelExport = new StocksExcelExport($search, $filters);
        $pdfExport = new StocksPdfExport($search, $filters);
        
        return response()->json([
            'excel' => $excelExport->getStats(),
            'pdf' => $pdfExport->getStats(),
            'stocks_by_status' => $pdfExport->getStocksByStatus()
        ]);
    }

    /**
     * Afficher le PDF des stocks dans le navigateur (avec mPDF)
     */
    public function stocksView(Request $request)
    {
        try {
            $search = $request->get('search');
            $filters = [
                'stock_faible' => $request->get('stock_faible'),
                'stock_zero' => $request->get('stock_zero'),
                'seuil_min' => $request->get('seuil_min')
            ];
            
            $export = new StocksPdfExport($search, $filters);
            
            return $export->viewPdf();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'affichage du PDF des stocks',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Test simple pour diagnostiquer le problème PDF stocks
     */
    public function stocksTest(Request $request)
    {
        try {
            // Test simple sans filtres
            $export = new StocksPdfExport();
            
            // Test de la génération PDF avec mPDF
            return $export->viewPdf();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Test échoué',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function movementsExcel(Request $request)
    {
        // TODO: Implémenter l'exportation des mouvements
        return response()->json(['message' => 'Export mouvements Excel - À implémenter']);
    }

    public function movementsPdf(Request $request)
    {
        // TODO: Implémenter l'exportation des mouvements
        return response()->json(['message' => 'Export mouvements PDF - À implémenter']);
    }

    /**
     * Export inventaire (fiche d'inventaire PDF)
     */
    public function stocksInventaire(Request $request)
    {
        try {
            $search = $request->get('search');
            $filters = [
                'stock_faible' => $request->get('stock_faible'),
                'stock_zero' => $request->get('stock_zero'),
                'seuil_min' => $request->get('seuil_min')
            ];
            
            $export = new InventaireExport($search, $filters);
            
            return $export->download();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la génération de l\'inventaire',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Prévisualiser la fiche d'inventaire (pour debug)
     */
    public function stocksInventairePreview(Request $request)
    {
        try {
            $search = $request->get('search');
            $filters = [
                'stock_faible' => $request->get('stock_faible'),
                'stock_zero' => $request->get('stock_zero'),
                'seuil_min' => $request->get('seuil_min')
            ];
            
            $export = new InventaireExport($search, $filters);
            
            return $export->preview();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la prévisualisation de l\'inventaire',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
}
