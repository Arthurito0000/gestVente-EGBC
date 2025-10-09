<?php

namespace App\Http\Middleware;

use App\Models\Stock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class StockAlertNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Récupérer les alertes de stock pour tous les utilisateurs connectés
            $stockAlerts = [
                'ruptures' => Stock::where('quantite', 0)->with('product')->get(),
                'alertes' => Stock::whereRaw('quantite <= seuil AND quantite > 0')->with('product')->get()
            ];
            
            // Partager avec toutes les vues
            View::share('globalStockAlerts', $stockAlerts);
        }

        return $next($request);
    }
}
