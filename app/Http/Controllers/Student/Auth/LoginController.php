<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'student']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('student');

        $request->session()->regenerate();

        return redirect()->intended(route('user.home', absolute: false));
    }
}
