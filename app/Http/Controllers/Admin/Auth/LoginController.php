<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'admin']);
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate('admin');

        $request->session()->regenerate();

        // Token generation removed - Sanctum no longer available

        return redirect()->intended(route('admin.home', absolute: false));
    }
}
