<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index($username)
    {
        $user = User::where('username', $username)->first();
        return view('user.profile.index', compact('user'));
    }

    public function updateImage(ProfileRequest $request, $username)
    {

        $user = User::where('username', $username)->first();
        $image = $request->image;
        Log::info($request->all());

        if ($request->hasFile('image')) {
            // Hanya hapus gambar lama jika BUKAN dari Google Avatar
            if ($user->image) {
                Storage::delete($user->image);
            }

            // Simpan gambar yang diunggah
            $path = $request->file('image')->store('gambar/gambar-profil', 'public');
            $image = $path;
        }

        $user->update([
            'image' => $image
        ]);

        return $this->sendResponse(
            'Profil gambar berhasil diubah',
            $user
        );
    }
}
