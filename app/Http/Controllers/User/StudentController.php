<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\HomeroomTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Models\Teacher;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::with(['class', 'homeroomTeacher.teacher'])->filter($request->all());

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
                ->editColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->addColumn('Nama', function ($row) {
                    $html = '
                    <div class="product-names">
                    <p>' . $row->name . '</p>
                    </div>
                    ';
                    return $html;
                })
                ->addColumn('NIS', function ($row) {
                    $html = '
                    <p class="f-light">' . $row->nis . '</p>
                    ';
                    return $html;
                })
                ->addColumn('NISN', function ($row) {
                    $html = '
                    <p class="f-light">' . $row->nisn . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Kelas', function ($row) {
                    $html = '
                        <span class="badge badge-light-primary">' . ($row->class ? $row->class->name : '-') . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Wali Kelas', function ($row) {
                    $html = '
                        <span class="badge badge-light-info">' . ($row->homeroomTeacher && $row->homeroomTeacher->teacher ? $row->homeroomTeacher->teacher->name : '-') . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Status', function ($row) {
                    $statusColors = [
                        'active' => 'badge-light-success',
                        'transferred' => 'badge-light-warning',
                        'graduated' => 'badge-light-info',
                        'dropout' => 'badge-light-danger'
                    ];
                    $statusLabels = [
                        'active' => 'Aktif',
                        'transferred' => 'Pindah',
                        'graduated' => 'Lulus',
                        'dropout' => 'Keluar'
                    ];
                    $color = $statusColors[$row->status] ?? 'badge-light-secondary';
                    $label = $statusLabels[$row->status] ?? $row->status;

                    $html = '
                        <span class="badge ' . $color . '">' . $label . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '
                    <div class="common-align gap-2 justify-content-start">
                        <a class="square-white edit" href="#!" data-id="' . $row->id . '">
                            <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                        </a>
                        <a class="square-white trash" href="#!" data-id="' . $row->id . '">
                            <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                        </a>
                    </div>';
                    return $html;
                })
                ->rawColumns(['id', 'Nama', 'NIS', 'NISN', 'Kelas', 'Wali Kelas', 'Status', 'Aksi'])
                ->make(true);
        } else {
            $students = Student::with(['class' => fn($query) => $query->select('id', 'name'), 'homeroomTeacher.teacher' => fn($query) => $query->select('id', 'name')])->filter(request()->all())->paginate(10);
            $classes = SchoolClass::select('id', 'name', 'level', 'major')->get();
            $teachers = Teacher::select('id', 'name')->get();
            $statuses = ['active', 'transferred', 'graduated', 'dropout'];
            $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
            return view('user.student.index', [
                'students' => $students,
                'classes' => $classes,
                'teachers' => $teachers,
                'statuses' => $statuses,
                'religions' => $religions
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
    public function store(StoreStudentRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'password' => bcrypt($validated['name']),
            ]);
            $user->assignRole('student');

            $validated['user_id'] = $user->id;
            $student = Student::create($validated);

            return $this->sendResponse(
                'Siswa berhasil ditambahkan',
                [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'class_name' => $student->class ? $student->class->name : '-',
                    'homeroom_teacher_name' => $student->homeroomTeacher && $student->homeroomTeacher->teacher ? $student->homeroomTeacher->teacher->name : '-',
                    'status' => $student->status,
                    'created_at' => $student->created_at->translatedFormat('d/m/Y H:i')
                ],
                201
            );
        } catch (\Exception $e) {
            Log::error('Error creating student: ' . $e->getMessage());
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
            $student = Student::with(['class', 'homeroomTeacher.teacher'])->find($id);

            if (!$student) {
                return $this->sendError(
                    'Data siswa tidak ditemukan.',
                    [],
                    404
                );
            }

            return $this->sendResponse('Data siswa ditemukan', $student);
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
    public function update(StoreStudentRequest $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->update($request->validated());

            return $this->sendResponse('Siswa berhasil diedit', $student);
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
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
            $student = Student::findOrFail($id);
            $student->delete();

            return $this->sendResponse(
                'Siswa berhasil dihapus.',
            );
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
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

            Student::whereIn('id', $ids)->delete();

            return $this->sendResponse(
                'Data yang dipilih berhasil dihapus.'
            );
        } catch (\Exception $e) {
            Log::error('Error bulk deleting students: ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
