<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Login- en logoutfunctionaliteit.
 */
class LoginController extends Controller
{
    /**
     * Toont het loginformulier.
     */
    public function show(): View
    {
        return view('auth.login', [
            'pageTitle' => 'Inloggen - Kniploket Tiko',
        ]);
    }

    /**
     * Verwerkt loginpoging met validatie en logging.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            Log::warning('Mislukte loginpoging.', ['email' => $credentials['email']]);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Onjuist e-mailadres of wachtwoord.');
        }

        $user = Auth::user();

        if (! $user->isActief()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::warning('Login geblokkeerd: account niet actief.', ['email' => $credentials['email']]);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Dit account is niet actief.');
        }

        $request->session()->regenerate();

        Log::info('Gebruiker ingelogd.', ['userId' => $user->id, 'role' => $user->role]);

        return redirect()->intended(route('home'));
    }

    /**
     * Logt de gebruiker uit.
     */
    public function logout(Request $request): RedirectResponse
    {
        Log::info('Gebruiker uitgelogd.', ['userId' => Auth::id()]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'U bent succesvol uitgelogd.');
    }
}
