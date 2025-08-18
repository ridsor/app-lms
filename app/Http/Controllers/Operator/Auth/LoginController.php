<?php

namespace App\Http\Controllers\Operator\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'operator']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('operator');

        $request->session()->regenerate();

        // Token generation removed - Sanctum no longer available

        return redirect()->intended(route('user.home', absolute: false));
    }
}
