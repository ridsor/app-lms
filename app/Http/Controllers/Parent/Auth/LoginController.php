<?php

namespace App\Http\Controllers\Parent\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'parent']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('parent');

        $request->session()->regenerate();

        $token = $request->user()->createToken('token')->plainTextToken;
        $request->session()->put('token', $token);

        return redirect()->intended(route('user.home', absolute: false));
    }
}
