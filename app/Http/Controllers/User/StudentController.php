<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\HomeroomTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Models\Major;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::query()
                ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
                ->leftJoin('homeroom_teachers', 'students.homeroom_teacher_id', '=', 'homeroom_teachers.id')
                ->leftJoin('teachers', 'homeroom_teachers.teacher_id', '=', 'teachers.id')
                ->select([
                    'students.*',
                    'classes.name as class_name',
                    'classes.level as class_level',
                    'majors.name as major_name',
                    'teachers.name as homeroom_teacher_name'
                ])->filter($request->all());

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
                ->addColumn('Jurusan', function ($row) {
                    $html = '
                    <span class="badge badge-light-primary">' . ($row->major_name ? $row->major_name : '-') . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Kelas', function ($row) {
                    $html = '
                        <span class="badge badge-light-primary">' . ($row->class_name . '-' . $row->class_level) . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Wali Kelas', function ($row) {
                    $html = '
                        <span class="badge badge-light-info">' . ($row->homeroom_teacher_name ? $row->homeroom_teacher_name : '-') . '</span>
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
                    $color = $statusColors[$row->status] ?? 'badge-light-secondary';

                    $html = '
                        <span class="badge ' . $color . '">' . Helper::getStudentStatusLabel($row->status) . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
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
                ->rawColumns(['id', 'Nama', 'NIS', 'NISN', 'Jurusan', 'Kelas', 'Wali Kelas', 'Status', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            $students = Student::with(['class' => fn($query) => $query->select('id', 'name'), 'homeroomTeacher.teacher' => fn($query) => $query->select('id', 'name')])->filter(request()->all())->paginate(10);
            $classes = SchoolClass::select('id', 'name', 'level', 'major_id')->orderBy('level', 'asc')->get();
            $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
            $majors = Major::select('id', 'name')->orderBy('name', 'asc')->get();
            $teachers = Teacher::select('id', 'name')->get();
            $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
            $genders = [['label' => 'Laki-laki', 'value' => 'M'], ['label' => 'Perempuan', 'value' => 'F']];
            $statuses = [['label' => 'Aktif', 'value' => 'active'], ['label' => 'Pindah', 'value' => 'transferred'], ['label' => 'Lulus', 'value' => 'graduated'], ['label' => 'Keluar', 'value' => 'dropout']];

            return view('user.student.index', [
                'students' => $students,
                'classes' => $classes,
                'classLevels' => $classLevels,
                'majors' => $majors,
                'teachers' => $teachers,
                'statuses' => $statuses,
                'religions' => $religions,
                'genders' => $genders,
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

            DB::beginTransaction();
            $user = User::create([
                'name' => $validated['name'],
                'password' => bcrypt($validated['name']),
            ]);
            $user->assignRole('student');
            $homeroomTeacher = HomeroomTeacher::create([
                'teacher_id' => $validated['homeroom_teacher_id'],
            ]);

            $validated['user_id'] = $user->id;
            $validated['homeroom_teacher_id'] = $homeroomTeacher->id;
            $validated['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');
            $student = Student::create($validated);

            DB::commit();

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
            DB::rollBack();
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
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $student = Student::with([
                'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
                'class.major' => fn($query) => $query->select('id', 'name'),
                'class.major.classes' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
                'homeroomTeacher.teacher' => fn($query) => $query->select('id', 'name')
            ])->findOrFail($id);

            if (!$student) {
                return $this->sendError(
                    'Data siswa tidak ditemukan.',
                    [],
                    404
                );
            }

            return $this->sendResponse('Data siswa ditemukan', $student);
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
     * Update the specified resource in storage.
     */
    public function update(StoreStudentRequest $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            $validated = $request->validated();
            $validated['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');
            $student->update($validated);

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
