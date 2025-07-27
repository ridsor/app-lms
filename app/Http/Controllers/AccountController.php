<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request, $username)
    {
        if ($username !== $request->user()->username) {
            abort(404);
        }
        $user = $request->user();
        return view('account.index', compact('user'));
    }
}
