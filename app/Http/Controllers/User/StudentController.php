<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Models\Major;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Student\BulkEditStudentRequest;
use Rap2hpoutre\FastExcel\FastExcel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('viewAny', Student::class)) {
            abort(403);
        }

        if ($request->ajax()) {
            $data = Student::query()
                ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
                ->select([
                    'students.id',
                    'students.name',
                    'students.nis',
                    'students.nisn',
                    'students.status',
                    'students.created_at',
                    'classes.name as class_name',
                    'classes.level as class_level',
                    'majors.name as major_name',
                ])
                ->filterByPermission($request->user())
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
                ->addColumn('Aksi', function ($row) use ($request) {
                    $html = '';
                    if ($request->user()->can('student.*')) {
                        $html .= '
                        <div class="common-align gap-2 justify-content-start">
                            <a class="square-white view" data-id="' . $row->id . '"><svg>
                                <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                                </use>
                            </svg>
                            </a>
                            <a class="square-white edit"  data-id="' . $row->id . '">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                            <a class="square-white trash"  data-id="' . $row->id . '">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                            </a>
                        </div>';
                    }
                    if ($request->user()->can('student.edit.homeroomteacher')) {
                        $html .= '
                        <div class="common-align gap-2 justify-content-start">
                            <a class="square-white edit"  data-id="' . $row->id . '">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                        </div>
                        ';
                    }
                    return $html;
                })
                ->rawColumns(['id', 'Nama', 'NIS', 'NISN', 'Jurusan', 'Kelas', 'Status', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            $classes = SchoolClass::select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc')->get();
            $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
            $classNames = SchoolClass::select('name')->distinct()->orderBy('name', 'asc')->get();
            $majors = Major::select('id', 'name')->orderBy('name', 'asc')->get();
            $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
            $genders = [['label' => 'Laki-laki', 'value' => 'M'], ['label' => 'Perempuan', 'value' => 'F']];
            $statuses = [['label' => 'Aktif', 'value' => 'active'], ['label' => 'Pindah', 'value' => 'transferred'], ['label' => 'Lulus', 'value' => 'graduated'], ['label' => 'Keluar', 'value' => 'dropout']];

            return view('user.student.index', [
                'classes' => $classes,
                'classLevels' => $classLevels,
                'classNames' => $classNames,
                'majors' => $majors,
                'statuses' => $statuses,
                'religions' => $religions,
                'genders' => $genders,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        if (!$request->user()->can('create', Student::class)) {
            return abort(403);
        }

        try {
            $validated = $request->validated();

            $validated['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');

            DB::beginTransaction();
            $student = User::create([
                'name' => $validated['name'],
            ]);
            $student->assignRole('student');
            $parent = User::create([
                'name' => 'Wali  ' . $validated['name'],
            ]);
            $parent->assignRole('parent');

            $validated['user_id'] = $student->id;
            $validated['parent_id'] = $parent->id;
            Student::create($validated);

            DB::commit();

            return $this->sendResponse(
                'Siswa berhasil ditambahkan',
                [],
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
    public function show(Request $request, $id)
    {
        try {
            $student = Student::with([
                'class' => function ($query) {
                    $query->select('id', 'name', 'level', 'major_id');
                },
                'class.major' => function ($query) {
                    $query->select('id', 'name');
                },
            ])->findOrFail($id);

            if (!$request->user()->can('view', $student)) {
                return abort(403);
            }

            if (!$student) {
                return $this->sendError(
                    'Data siswa tidak ditemukan.',
                    [],
                    404
                );
            }

            // Format data untuk kebutuhan frontend/modal
            $data = [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'class' => $student->class ? [
                    'id' => $student->class->id,
                    'name' => $student->class->name,
                    'level' => $student->class->level,
                    'major' => $student->class->major ? $student->class->major->name : null,
                ] : null,
                'date_of_birth' => $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : null,
                'birthplace' => $student->birthplace,
                'gender' => $student->gender,
                'religion' => $student->religion,
                'admission_year' => $student->admission_year,
                'status' => $student->status,
                'created_at' => $student->created_at ? $student->created_at->translatedFormat('d/m/Y H:i') : null,
            ];

            return $this->sendResponse('Data siswa ditemukan', $data);
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
    public function edit(Request $request, string $id)
    {
        try {
            $student = Student::with([
                'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
            ])->findOrFail($id);

            if (!$request->user()->can('update', $student)) {
                return abort(403);
            }

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
     * Update the specified resource in storage.
     */
    public function update(StoreStudentRequest $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            if (!$request->user()->can('update', $student)) {
                return abort(403);
            }

            $validated = $request->validated();

            $validated['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');

            DB::beginTransaction();
            $student->update($validated);
            $student->user->update([
                'name' => $validated['name'],
            ]);
            $student->parent->update([
                'name' => 'Wali ' . $validated['name'],
            ]);
            DB::commit();

            return $this->sendResponse('Siswa berhasil diedit', $student);
        } catch (\Exception $e) {
            DB::rollBack();
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

            if (!$request->user()->can('delete', Student::class)) {
                return abort(403);
            }

            DB::beginTransaction();

            if ($student->user) {
                $student->user->delete();
            }
            $student->delete();

            DB::commit();

            return $this->sendResponse(
                'Siswa berhasil dihapus.',
            );
        } catch (\Exception $e) {
            DB::rollBack();
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
            if (!$request->user()->can('delete', Student::class)) {
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

            DB::beginTransaction();

            Student::whereIn('id', $ids)->get()->each(function ($student) use ($request) {
                $student->user->delete();
                $student->parent->delete();
                $student->delete();
            });

            DB::commit();

            return $this->sendResponse(
                'Data yang dipilih berhasil dihapus.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk destroying students: ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function bulkEdit(BulkEditStudentRequest $request)
    {
        try {
            $validated = $request->validated();
            $ids = $validated['ids'];

            $data = [];
            if (!empty($validated['class_id'])) {
                $data['class_id'] = $validated['class_id'];
            }
            if (!empty($validated['homeroom_teacher_id'])) {
                $data['homeroom_teacher_id'] = $validated['homeroom_teacher_id'];
            }
            if (!empty($validated['status'])) {
                $data['status'] = $validated['status'];
            }

            if (!empty($data)) {
                Student::whereIn('id', $ids)->get()->each(function ($student) use ($data, $request) {
                    if (!$request->user()->can('update', $student)) {
                        return abort(403);
                    }

                    $student->update($data);
                });
                return $this->sendResponse(
                    'Data yang dipilih berhasil diedit.'
                );
            } else {
                return $this->sendError(
                    'Tidak ada data yang valid untuk diupdate.',
                    [],
                    400
                );
            }
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function exportStudentAccount(Request $request)
    {
        try {
            if (!$request->user()->can('viewAny', Student::class)) {
                return abort(403);
            }

            $query = Student::query()
                ->select('id', 'name', 'nis', 'nisn', 'class_id', 'user_id')
                ->with([
                    'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
                    'class.major' => fn($query) => $query->select('id', 'name'),
                    'user' => fn($query) => $query->select('id', 'username'),
                ])
                ->filterByPermission($request->user());

            if ($request->filled('major')) {
                $query->whereHas('class.major', function ($q) use ($request) {
                    $q->where('name', $request->major);
                });
            }

            if ($request->filled('class')) {
                $query->whereHas('class', function ($q) use ($request) {
                    $q->where('name', $request->class);
                });
            }

            if ($request->filled('level')) {
                $query->whereHas('class', function ($q) use ($request) {
                    $q->where('level', $request->level);
                });
            }

            $students = $query->get();


            $exportData = [];
            if (Major::count() > 0) {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NIS' => $student->nis,
                        'NISN' => $student->nisn,
                        'Jurusan' => $student->class && $student->class->major ? $student->class->major->name : '-',
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->user ? $student->user->username : '-',
                        'Password' => $student->user ? $student->user->username : '-',
                    ];
                });
            } else {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NIS' => $student->nis,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->user ? $student->user->username : '-',
                        'Password' => $student->user ? $student->user->username : '-',
                    ];
                });
            }

            $filename = 'akun-siswa';
            if ($request->filled('major')) {
                $filename .= '-' . strtolower(str_replace(' ', '-', $request->major));
            }
            if ($request->filled('class')) {
                $filename .= '-' . strtolower(str_replace(' ', '-', $request->class));
            }
            if ($request->filled('level')) {
                $filename .= '-' . $request->level;
            }

            $filename .= '.xlsx';

            // Export data
            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }

    public function exportParentAccount(Request $request)
    {
        try {
            if (!$request->user()->can('viewAny', Student::class)) {
                return abort(403);
            }

            $query = Student::query()
                ->select('id', 'name', 'nis', 'nisn', 'class_id', 'parent_id')
                ->with([
                    'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
                    'class.major' => fn($query) => $query->select('id', 'name'),
                    'parent' => fn($query) => $query->select('id', 'username'),
                ])
                ->filterByPermission($request->user());

            if ($request->filled('major')) {
                $query->whereHas('class.major', function ($q) use ($request) {
                    $q->where('name', $request->major);
                });
            }

            if ($request->filled('class')) {
                $query->whereHas('class', function ($q) use ($request) {
                    $q->where('name', $request->class);
                });
            }

            if ($request->filled('level')) {
                $query->whereHas('class', function ($q) use ($request) {
                    $q->where('level', $request->level);
                });
            }

            $students = $query->get();

            $exportData = [];
            if (Major::count() > 0) {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NIS' => $student->nis,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->parent ? $student->parent->username : '-',
                        'Password' => $student->parent ? $student->parent->username : '-',
                    ];
                });
            } else {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NIS' => $student->nis,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->parent ? $student->parent->username : '-',
                        'Password' => $student->parent ? $student->parent->username : '-',
                    ];
                });
            }

            $filename = 'akun-wali-siswa';
            if ($request->filled('major')) {
                $filename .= '-' . strtolower(str_replace(' ', '-', $request->major));
            }
            if ($request->filled('class')) {
                $filename .= '-' . strtolower(str_replace(' ', '-', $request->class));
            }
            if ($request->filled('level')) {
                $filename .= '-' . $request->level;
            }
            $filename .= '.xlsx';

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
}
