<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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

       if (Auth::attempt($credentials)) {
           $request->session()->regenerate();

           return redirect()->intended('dashboard');
       }

       return back()->withInput($request->only('email'))
                     ->with('error', 'Identifiants invalides. Veuillez réessayer.');
    }

    public function logout(Request $request)
    {
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

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }

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
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
