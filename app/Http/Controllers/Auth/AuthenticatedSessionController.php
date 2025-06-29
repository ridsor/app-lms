<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        return view('auth.login', [
            'title' => 'Login'
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     $token = $request->user()->createToken('token')->plainTextToken;
    //     $request->session()->put('token', $token);

    //     if ($request->user()->hasRole('admin')) {
    //         return redirect()->intended(route('admin.dashboard', absolute: false));
    //     } else {
    //         return redirect()->intended(route('home', absolute: false));
    //     }
    // }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->tokens()->delete();
        Auth::guard('web')->logout();
        $request->session()->forget('token');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
