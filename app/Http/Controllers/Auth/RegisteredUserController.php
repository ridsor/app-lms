<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'title' => 'Registrasi'
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', (new Unique('users', 'email'))->whereNull('deleted_at')],
            'password' => [
                'required',
                'confirmed',
                Password::min(8) // Minimum 8 karakter
                    ->letters() // Harus mengandung huruf
                    ->mixedCase() // Harus ada huruf besar dan kecil
                    ->numbers() // Harus mengandung angka
                    ->symbols() // Harus ada karakter spesial (!@#$%^&)
                    ->uncompromised() // Memeriksa apakah password termasuk dalam daf
            ],
        ]);

        $trashedUser = User::onlyTrashed()->where('email', $validated['email'])->first();

        if ($trashedUser) {
            // Kirim email verifikasi untuk restorasi
            $trashedUser->restore();

            // Update data user
            $trashedUser->update([
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => null,
            ]);

            $trashedUser->sendEmailVerificationNotification();
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
        }


        return back()->with('success', '<strong>Success!</strong> Akun Berhasil Dibuat, Silahkan Verifikasi Email Anda!');
    }
}