<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index()
    {
        $rooms = Room::filter(request()->all())->paginate(10)->onEachSide(1);
        return view('user.rooms.index', [
            'rooms' => $rooms
        ]);
    }

    public function show($id)
    {
        try {
            $room = Room::findOrFail($id);
            return $this->sendResponse('Data ruangan berhasil diambil.', $room);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
        ]);
        try {

            $room = Room::create($validated);

            return $this->sendResponse('Ruangan berhasil ditambahkan.', $room);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $id,
        ]);
        try {

            $room = Room::findOrFail($id);
            $room->update($validated);

            return $this->sendResponse('Ruangan berhasil diupdate.', $room->refresh());
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Cek apakah ruangan memiliki jadwal terkait
            if ($room->schedules()->count() > 0) {
                return $this->sendError(
                    'Ruangan ini masih digunakan dalam jadwal pembelajaran.',
                    [],
                    422
                );
            }

            $room->delete();

            return $this->sendResponse('Ruangan berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
