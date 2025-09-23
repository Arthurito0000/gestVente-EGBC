<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Http\Exports\ProductsExcelExport;
use App\Http\Exports\ProductsPdfExport;
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
     * Méthode générique pour exporter d'autres entités (stocks, mouvements, etc.)
     */
    public function stocksExcel(Request $request)
    {
        // TODO: Implémenter l'exportation des stocks
        return response()->json(['message' => 'Export stocks Excel - À implémenter']);
    }

    public function stocksPdf(Request $request)
    {
        // TODO: Implémenter l'exportation des stocks
        return response()->json(['message' => 'Export stocks PDF - À implémenter']);
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
}
