<?php

namespace App\Http\Controllers\VicePrincipal\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'vice-principal']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('vice-principal');

        $request->session()->regenerate();

        // Token generation removed - Sanctum no longer available

        return redirect()->intended(route('user.home', absolute: false));
    }
}
