<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Notifications\PasswordChangedNotification;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
       $credentials = $request->validate([
           'email' => 'required|email',
           'password' => 'required'
       ]);

       // Vérifier d'abord si l'utilisateur existe
       $user = User::where('email', $credentials['email'])->first();
       
       // Vérifier si le compte est désactivé
       if ($user && $user->statut === 'inactif') {
           return back()->withInput($request->only('email'))
                        ->with('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
       }

       if (Auth::attempt($credentials)) {
           $request->session()->regenerate();

           // 🔥 FIX ULTIME : Redirection FORCÉE vers le dashboard avec route() helper
           // Évite tout conflit avec les requêtes AJAX des notifications
           return redirect()->route('dashboard');
       }

       return back()->withInput($request->only('email'))
                     ->with('error', 'Identifiants invalides. Veuillez réessayer.');
    }

    public function logout(Request $request)
{
    // Vérifier si l'utilisateur est déjà déconnecté
    if (!Auth::check()) {
        return redirect()->route('login')->with('info', 'Vous êtes déjà déconnecté.');
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect()->route('login')->with('success', 'Vous avez été déconnecté.');
}

    // Show the email form to request a password reset link
    public function showEmailVerificationForm()
    {
        return view('auth.email_verif');
    }

    // Send password reset link to the provided email
    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Vérifier si l'IP est suspecte
        if (PasswordResetLog::isSuspiciousIP($request->ip())) {
            PasswordResetLog::logFailed(
                $request->email,
                $request->ip(),
                $request->userAgent(),
                'suspicious_ip'
            );
            
            return back()->withErrors([
                'email' => 'Trop de tentatives depuis cette adresse IP. Veuillez réessayer plus tard.'
            ]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Logger la demande réussie
            PasswordResetLog::logRequest(
                $request->email,
                $request->ip(),
                $request->userAgent(),
                'success'
            );
            
            return back()->with('success', __($status));
        }

        // Logger l'échec
        PasswordResetLog::logFailed(
            $request->email,
            $request->ip(),
            $request->userAgent(),
            'send_failed'
        );

        return back()->withErrors(['email' => __($status)]);
    }

    // Show the reset form coming from the email link (token + email)
    public function showPasswordResetForm(Request $request, $token)
    {
        return view('auth.password_reset', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // Handle the actual password reset using the token
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Logger le reset réussi
                PasswordResetLog::logReset(
                    $user->email,
                    $request->ip(),
                    $request->userAgent(),
                    'success'
                );

                // Envoyer la notification de changement de mot de passe
                $user->notify(new PasswordChangedNotification(
                    $request->ip(),
                    $request->userAgent()
                ));

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
        }

        // Logger l'échec du reset
        PasswordResetLog::logFailed(
            $request->email,
            $request->ip(),
            $request->userAgent(),
            'reset_failed'
        );

        return back()->withErrors(['email' => __($status)]);
    }
}