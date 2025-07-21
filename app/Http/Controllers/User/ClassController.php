<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Class\StoreClassRequest;
use App\Models\Major;
use App\Models\Teacher;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('class.*')) {
            return abort(403);
        }

        if ($request->ajax()) {
            $data = SchoolClass::query()
                ->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
                ->select([
                    'classes.id',
                    'classes.name',
                    'classes.level',
                    'majors.name as major_name',
                ])
                ->filter($request->all());

            return Datatables::of($data)
                ->addColumn('id', function ($row) {
                    $html = '
                        <div class="checkbox-checked">
                            <div class="form-check d-flex justify-content-center align-items-center">
                            <input class="form-check-input select-row" type="checkbox"
                                    style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '">
                            </div>
                        </div>
                        ';
                    return $html;
                })
                ->addColumn('Nama', function ($row) {
                    $html = '
                    <div class="product-names">
                    <p>' . $row->name . '</p>
                    </div>
                    ';
                    return $html;
                })
                ->addColumn('Tingkat', function ($row) {
                    $html = '
                    <p class="f-light">' . $row->level . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Jurusan', function ($row) {
                    $html = '
                        <span class="badge badge-light-primary">' . ($row->major_name ? $row->major_name : " - ") . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '
                    <div class="common-align gap-2 justify-content-start">
                        <a class="square-white edit" style="cursor: pointer" data-id="' . $row->id . '">
                            <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                        </a>
                        <a class="square-white trash" style="cursor: pointer" data-id="' . $row->id . '">
                            <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                        </a>
                    </div>';
                    return $html;
                })
                ->rawColumns(['id', 'Nama', 'Tingkat', 'Jurusan', 'Aksi'])
                ->make(true);
        } else {
            $levels = SchoolClass::select('level')->distinct()->get();
            $majors = Major::select('id', 'name')->get();
            return view('user.class.index', [
                'levels' => $levels,
                'majors' => $majors,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRequest $request)
    {
        try {
            if (!$request->user()->can('class.*')) {
                return abort(403);
            }

            $validated = $request->validated();

            $class = SchoolClass::create($validated);

            return $this->sendResponse(
                'Kelas berhasil ditambahkan',
                [
                    'id' => $class->id,
                    'name' => $class->name,
                    'level' => $class->level,
                    'major' => $class->major->name ?? '-',
                    'created_at' => $class->created_at->translatedFormat('d/m/Y H:i')
                ],
                201
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        try {
            if (!$request->user()->can('class.*')) {
                return abort(403);
            }

            $class = SchoolClass::find($id);

            if (!$class) {
                return $this->sendError(
                    'Data kelas tidak ditemukan.',
                    [],
                    404
                );
            }

            return $this->sendResponse('Data kelas ditemukan', $class);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClassRequest $request, $id)
    {
        try {
            if (!$request->user()->can('class.*')) {
                return abort(403);
            }

            $class = SchoolClass::findOrFail($id);

            $validated = $request->validated();

            $class->update($validated);

            return $this->sendResponse('Kelas berhasil diedit', $class);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (!$request->user()->can('class.*')) {
                return abort(403);
            }

            $class = SchoolClass::findOrFail($id);
            if ($class->students->count() > 0) {
                return $this->sendError(
                    'Kelas tidak dapat dihapus karena masih memiliki siswa.',
                    [],
                    400
                );
            }
            if ($class->schedules->count() > 0) {
                return $this->sendError(
                    'Kelas tidak dapat dihapus karena masih memiliki jadwal.',
                    [],
                    400
                );
            }
            $class->delete();

            return $this->sendResponse(
                'Kelas berhasil dihapus.',
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            if (!$request->user()->can('class.*')) {
                return abort(403);
            }

            $ids = $request->input('ids');

            if (empty($ids)) {
                return $this->sendError(
                    'Tidak ada data yang dipilih untuk dihapus.',
                    [],
                    400
                );
            }

            $classes = SchoolClass::whereIn('id', $ids)->get();
            foreach ($classes as $class) {
                if ($class->students->count() > 0) {
                    return $this->sendError(
                        'Kelas tidak dapat dihapus karena masih memiliki siswa.',
                        [],
                        400
                    );
                }
                if ($class->schedules->count() > 0) {
                    return $this->sendError(
                        'Kelas tidak dapat dihapus karena masih memiliki jadwal.',
                        [],
                        400
                    );
                }
                $class->delete();
            }

            return $this->sendResponse(
                'Data yang dipilih berhasil dihapus.'
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
