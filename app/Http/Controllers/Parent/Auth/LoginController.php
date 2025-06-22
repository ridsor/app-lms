<?php

namespace App\Http\Controllers\Parent\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', ['role' => 'parent']);
    }

    public function login(Request $request)
    {
        // Dummy login logic
        return redirect()->route('home');
    }
}
