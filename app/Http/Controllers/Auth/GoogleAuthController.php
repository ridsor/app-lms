<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google’s OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request)
    {
        try {
            // Get the user information from Google
            $user = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect(route('home', absolute: false))->with('error', 'Otentikasi Google gagal.');
        }

        // Check if the user already exists in the database
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Log the user in if they already exist
            Auth::login($existingUser);
            $token = $request->user()->createToken('token')->plainTextToken;
            $request->session()->put('token', $token);
        } else {
            try {
                DB::beginTransaction();

                // Otherwise, create a new user and log them in
                $newUser = User::updateOrCreate([
                    'email' => $user->email
                ], [
                    'name' => $user->name,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now()
                ]);

                $newUser->profile()->updateOrCreate([
                    'user_id' => $user->id
                ], [
                    'image' => $user->avatar,
                ]);

                Auth::login($newUser);

                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                return redirect(route('home', absolute: false))->with('error', 'Google authentication failed.');
            }
        }

        // Redirect the user to the dashboard or any other secure page
        return redirect(route('home', absolute: false));
    }
}