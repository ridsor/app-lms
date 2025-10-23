<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Major\StoreMajorRequest;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('major.*')) {
            return abort(403);
        }

        if ($request->ajax()) {
            $data = Major::filter(request()->all());
            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    $html = '<div class="checkbox-checked"><div class="form-check d-flex justify-content-center align-items-center"><input class="form-check-input select-row" type="checkbox" style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '"></div></div>';
                    return $html;
                })
                ->addColumn('Nama', function ($row) {
                    return '<div class="product-names"><p>' . $row->name . '</p></div>';
                })
                ->addColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '<div class="common-align gap-2 justify-content-start"><a class="square-white edit"  data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg></a><a class="square-white trash"  data-id="' . $row->id . '"><svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg></a></div>';
                    return $html;
                })
                ->rawColumns(['id', 'Nama', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            return view('user.major.index');
        }
    }

    public function store(StoreMajorRequest $request)
    {
        try {
            if (!$request->user()->can('major.*')) {
                return abort(403);
            }

            $major = Major::create($request->validated());
            return $this->sendResponse('Jurusan berhasil ditambahkan', $major, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if (!$request->user()->can('major.*')) {
                return abort(403);
            }

            $major = Major::find($id);
            if (!$major) {
                return $this->sendError('Data jurusan tidak ditemukan.', [], 404);
            }
            return $this->sendResponse('Data jurusan ditemukan', $major);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(StoreMajorRequest $request, $id)
    {
        try {
            if (!$request->user()->can('major.*')) {
                return abort(403);
            }

            $major = Major::findOrFail($id);
            $major->update($request->validated());
            return $this->sendResponse('Jurusan berhasil diedit', $major);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (!$request->user()->can('major.*')) {
                return abort(403);
            }

            $major = Major::findOrFail($id);
            if ($major->classes()->count() > 0) {
                return $this->sendError(
                    'Jurusan tidak dapat dihapus karena masih memiliki kelas.',
                    [],
                    400
                );
            }
            $major->delete();

            return $this->sendResponse('Jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            if (!$request->user()->can('major.*')) {
                return abort(403);
            }

            $ids = $request->input('ids');
            if (empty($ids)) {
                return $this->sendError('Tidak ada data yang dipilih untuk dihapus.', [], 400);
            }
            $majors = Major::whereIn('id', $ids)->get();
            foreach ($majors as $major) {
                if ($major->classes()->count() > 0) {
                    return $this->sendError(
                        'Jurusan tidak dapat dihapus karena masih memiliki kelas.',
                        [],
                        400
                    );
                }
                $major->delete();
            }
            return $this->sendResponse('Data yang dipilih berhasil dihapus.');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }
}
