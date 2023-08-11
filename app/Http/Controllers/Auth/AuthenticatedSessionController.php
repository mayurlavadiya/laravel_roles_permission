<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Attempt to authenticate the user using the provided credentials.
        $request->authenticate();

        // Regenerate the session ID and CSRF token to enhance security.
        $request->session()->regenerate();

        // Redirect the user to the intended page after successful login.
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout the user by invalidating the current session.
        Auth::guard('web')->logout();

        // Invalidate the entire session, removing user data.
        $request->session()->invalidate();

        // Regenerate the CSRF token to enhance security.
        $request->session()->regenerateToken();

        // Redirect the user to the home page after logout.
        return redirect('/');
    }
}
