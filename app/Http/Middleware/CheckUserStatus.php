<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (Auth::check()) {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est inactif
            if ($user->statut === 'inactif') {
                // Déconnecter l'utilisateur
                Auth::logout();
                
                // Invalider la session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Rediriger vers la page de connexion avec un message d'erreur
                return redirect()->route('login')->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
                ]);
            }
        }
        
        return $next($request);
    }
}
