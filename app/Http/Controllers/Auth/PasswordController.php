<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
                'password' => [
                    'required',
                    Password::min(8) // Minimum 8 karakter
                        ->letters() // Harus mengandung huruf
                        ->mixedCase() // Harus ada huruf besar dan kecil
                        ->numbers() // Harus mengandung angka
                        ->symbols() // Harus ada karakter spesial (!@#$%^&)
                        ->uncompromised(), // Memeriksa apakah password termasuk dalam daf
                    'confirmed',
                ],
            ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah');
    }
}