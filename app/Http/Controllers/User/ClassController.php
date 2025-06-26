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

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SchoolClass::filter($request->all());

            return Datatables::of($data)
                ->addColumn('id', function ($row) {
                    // Memperbaiki kode: id checkbox harus unik, gunakan id dinamis dan name array agar bisa multiple select
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
                        <span class="badge badge-light-primary">' . ($row->major?->name ? $row->major->name : " - ") . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Kapasitas', function ($row) {
                    $html = '
                        <p class="f-light">' . $row->capacity . '</p>
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
                ->addColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
                })
                ->rawColumns(['id', 'Nama', 'Tingkat', 'Jurusan', 'Kapasitas', 'Waktu', 'Aksi',])
                ->make(true);
        } else {
            $classes = SchoolClass::filter(request()->all())->paginate(10);
            $levels = SchoolClass::select('level')->distinct()->get();
            $majors = Major::select('id', 'name')->get();
            return view('user.class.index', [
                'classes' => $classes,
                'levels' => $levels,
                'majors' => $majors
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRequest $request)
    {
        try {
            // Buat kelas baru
            $schoolClass = SchoolClass::create([
                'name' => $request->validated('name'),
                'level' => $request->validated('level'),
                'major_id' => $request->validated('major_id'),
                'capacity' => $request->validated('capacity')
            ]);

            // Response sukses
            return $this->sendResponse(
                'Kelas berhasil ditambahkan',
                [
                    'id' => $schoolClass->id,
                    'name' => $schoolClass->name,
                    'level' => $schoolClass->level,
                    'major' => $schoolClass->major->name ?? '-',
                    'capacity' => $schoolClass->capacity,
                    'created_at' => $schoolClass->created_at->translatedFormat('d/m/Y H:i')
                ],
                201
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClassRequest $request, $id)
    {
        try {
            $class = SchoolClass::findOrFail($id);
            $class->update($request->validated());

            return $this->sendResponse('Kelas berhasil diedit', $class);
        } catch (\Exception $e) {
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
            $class = SchoolClass::findOrFail($id);
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

            $ids = $request->input('ids');

            if (empty($ids)) {
                return $this->sendError(
                    'Tidak ada data yang dipilih untuk dihapus.',
                    [],
                    400
                );
            }

            SchoolClass::whereIn('id', $ids)->delete();

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

    public function getByMajor($major_id)
    {
        $classes = SchoolClass::where('major_id', $major_id)
            ->select('id', 'name', 'level')
            ->orderBy('level', 'asc')
            ->get();

        return $this->sendResponse('Data kelas ditemukan', $classes);
    }
}
