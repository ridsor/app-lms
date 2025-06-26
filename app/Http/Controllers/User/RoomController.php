<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Room::filter(request()->all());
            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    $html = '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
                    return $html;
                })
                ->addColumn('Nama', function ($row) {
                    return '<div class="product-names"><p>' . $row->name . '</p></div>';
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '<div class="common-align gap-2 justify-content-start"><a class="square-white edit"  data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg></a><a class="square-white trash"  data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg></a></div>';
                    return $html;
                })
                ->addColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
                })
                ->rawColumns(['id', 'Nama', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            $rooms = Room::paginate(10);
            return view('user.rooms.index', [
                'rooms' => $rooms
            ]);
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
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function show($id)
    {
        try {
            $room = Room::find($id);
            if (!$room) {
                return $this->sendError('Data ruangan tidak ditemukan.', [], 404);
            }
            return $this->sendResponse('Data ruangan berhasil diambil.', $room);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
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
            return $this->sendResponse('Ruangan berhasil diedit.', $room->refresh());
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            if ($room->schedules()->count() > 0) {
                return $this->sendError('Ruangan ini masih digunakan dalam jadwal pembelajaran.', [], 422);
            }
            $room->delete();
            return $this->sendResponse('Ruangan berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (empty($ids)) {
                return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
            }
            $rooms = Room::whereIn('id', $ids)->get();
            foreach ($rooms as $room) {
                if ($room->schedules()->count() > 0) {
                    return $this->sendError('Beberapa ruangan masih digunakan dalam jadwal pembelajaran.', [], 422);
                }
            }
            Room::whereIn('id', $ids)->delete();
            return $this->sendResponse('Data yang dipilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }
}
