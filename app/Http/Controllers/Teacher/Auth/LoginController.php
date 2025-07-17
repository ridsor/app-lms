<?php

namespace App\Http\Controllers\Teacher\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'teacher']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('teacher');

        $request->session()->regenerate();

        // Token generation removed - Sanctum no longer available

        return redirect()->intended(route('user.home', absolute: false));
    }
}
