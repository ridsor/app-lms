<?php

namespace App\Http\Controllers;

use App\Models\User;

class AccountController extends Controller
{
    public function index($username)
    {
        $user = User::where('username', $username)->first();
        return view('account.index', compact('user'));
    }
}
