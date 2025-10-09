<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'user.status' => \App\Http\Middleware\CheckUserStatus::class,
        ]);
        
        // Appliquer les middlewares à toutes les routes authentifiées
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserStatus::class,
            \App\Http\Middleware\StockAlertNotifications::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 🔥 Gestion de l'erreur CSRF (419) pour la déconnexion
        $exceptions->render(function (TokenMismatchException $e, $request) {
            // Si c'est une requête de déconnexion
            if ($request->is('logout')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->with('info', 'Session expirée, vous avez été déconnecté.');
            }
            
            // Pour toutes les autres requêtes avec token CSRF expiré
            return redirect()->route('login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        });
    })->create();