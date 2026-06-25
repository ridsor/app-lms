<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Student\StudentRequest;
use App\Models\Major;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Student\BulkEditStudentRequest;
use App\Models\Period;
use App\Models\Teacher;
use Rap2hpoutre\FastExcel\FastExcel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        if ($request->ajax()) {
            $data = Student::query()
                ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
                ->leftJoin('teachers', 'students.homeroom_teacher_id', '=', 'teachers.id')
                ->select([
                    'students.id',
                    'students.name',
                    'students.nisn',
                    'students.status',
                    'students.created_at',
                    'classes.name as class_name',
                    'classes.level as class_level',
                    'majors.name as major_name',
                    'teachers.name as homeroom_teacher_name',
                ])
                ->filter($request->all())
                ->filterByPermission($request->user());

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
                        <span class="badge badge-light-primary">' . ($row->class_name ? $row->class_name . ' ' . $row->class_level : '-') . '</span>
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
                        <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                            <a class="square-white view" data-id="' . $row->id . '"><svg>
                                <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                                </use>
                            </svg>
                            </a>
                            <a class="square-white edit"  data-id="' . $row->id . '" style="cursor: pointer;">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                            <a class="square-white trash"  data-id="' . $row->id . '" style="cursor: pointer;">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                            </a>
                            <a class="square-white"  style="cursor: pointer;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" id="reset-password" data-id="' . $row->id . '">Reset Kata Sandi</a></li>
                            </ul>
                        </div>';
                    }
                    if ($request->user()->can('student.edit.homeroomteacher')) {
                        $html .= '
                        <div class="common-align gap-2 justify-content-start">
                        <a class="square-white view" data-id="' . $row->id . '"><svg>
                            <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                            </use>
                        </svg>
                        <a class="square-white edit"  data-id="' . $row->id . '">
                            <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                        </a>
                        </div>
                        ';
                    }
                    return $html;
                })
                ->rawColumns(['id', 'Nama', 'NISN', 'Jurusan', 'Kelas', 'Status', 'Waktu', 'Aksi'])
                ->make(true);
        } else {
            $classes = SchoolClass::select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
            $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
            $classNames = SchoolClass::select('name')->distinct()->orderBy('name', 'asc')->get();
            $majors = Major::with(['classes' => function ($query) {
                $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
            }])->select('id', 'name')->orderBy('name', 'asc')->get();
            $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
            $genders = [['label' => 'Laki-laki', 'value' => 'M'], ['label' => 'Perempuan', 'value' => 'F']];
            $statuses = [['label' => 'Aktif', 'value' => 'active'], ['label' => 'Pindah', 'value' => 'transferred'], ['label' => 'Lulus', 'value' => 'graduated'], ['label' => 'Keluar', 'value' => 'dropout']];
            $teachers = Teacher::select('id', 'name')->orderBy('name', 'asc')->get();
            $classes = $classes->orderBy('name', 'asc')->get();
            $hasMajors = Major::count() > 0;

            return view('user.student.index', [
                'classes' => $classes,
                'classLevels' => $classLevels,
                'classNames' => $classNames,
                'majors' => $majors,
                'statuses' => $statuses,
                'religions' => $religions,
                'genders' => $genders,
                'teachers' => $teachers,
                'hasMajors' => $hasMajors,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request)
    {
        try {
            if (!$request->user()->can('create', Student::class)) {
                return abort(403);
            }

            $validated = $request->validated();

            $validated['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $validated['date_of_birth'])->translatedFormat('Y-m-d');

            DB::beginTransaction();

            $student = User::create([
                'name' => $validated['name'],
            ]);
            $student->assignRole('student');
            $parent = User::create([
                'name' => 'Wali ' . $validated['name'],
            ]);
            $parent->assignRole('parent');

            $validated['user_id'] = $student->id;
            $validated['parent_id'] = $parent->id;
            $student = Student::create($validated);

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
                'homeroom_teacher' => function ($query) {
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
    public function edit(Request $request, string $id)
    {
        try {
            $student = Student::with([
                'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
                'homeroom_teacher' => fn($query) => $query->select('id', 'name'),
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
    public function update(StudentRequest $request, $id)
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
            $student->user->name = $validated['name'];
            $student->parent->name = 'Wali ' . $validated['name'];
            $student->user->save();
            $student->parent->save();

            DB::commit();

            return $this->sendResponse('Siswa berhasil diperbarui', $student);
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
            if ($student->parent) {
                $student->parent->delete();
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

    public function resetPassword(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $this->authorize('update', $student);

            if ($student->user) {
                $student->user->update([
                    'password' => bcrypt($student->user->username)
                ]);
            }
            if ($student->parent) {
                $student->parent->update([
                    'password' => bcrypt($student->parent->username)
                ]);
            }

            return $this->sendResponse(
                'Siswa berhasil direset kata sandi.',
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
                if ($student->user) {
                    $student->user->delete();
                }
                if ($student->parent) {
                    $student->parent->delete();
                }
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

            $data = collect([
                'class_id'            => $validated['class_id'] ?? null,
                'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? null,
                'status'              => $validated['status'] ?? null,
            ])->filter(function ($value, $key) use ($validated) {
                if (($validated[$key] ?? null) === 'nothing') {
                    return true;
                }
                return !empty($value);
            })->map(function ($value, $key) use ($validated) {
                return ($validated[$key] ?? null) === 'nothing' ? null : $value;
            })->toArray();

            if (empty($data)) {
                return $this->sendError(
                    'Tidak ada data yang valid untuk diupdate.',
                    [],
                    400
                );
            }

            DB::beginTransaction();

            Student::whereIn('id', $ids)->get()->each(function ($student) use ($data, $request) {
                if (!$request->user()->can('update', $student)) {
                    abort(403);
                }

                $student->update($data);
            });

            DB::commit();
            return $this->sendResponse('Data yang dipilih berhasil diedit.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function exportStudentAccount(Request $request)
    {
        try {
            if (!$request->user()->can('viewAny', Student::class)) {
                return abort(403);
            }

            $query = Student::query()
                ->select('id', 'name', 'nisn', 'class_id', 'user_id')
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

            $headerRows = [
                ['Akun Siswa' => ''],
                ['Jurusan', 'Jurusan' => $request->filled('major') ? $request->major : 'Semua Jurusan'],
                ['Kelas', 'Kelas' => $request->filled('class') ? $request->class : 'Semua Kelas'],
                ['Tingkat', 'Tingkat' => $request->filled('level') ? $request->level : 'Semua Tingkat'],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'NISN' => 'NISN', 'Jurusan' => 'Jurusan', 'Kelas' => 'Kelas', 'Tingkat' => 'Tingkat', 'Username' => 'Username', 'Password' => 'Password'],
            ];


            $dataRows = [];
            if (Major::count() > 0) {
                $dataRows = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NISN' => $student->nisn,
                        'Jurusan' => $student->class && $student->class->major ? $student->class->major->name : '-',
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->user ? $student->user->username : '-',
                        'Password' => $student->user ? $student->user->username : '-',
                    ];
                })->toArray();
            } else {
                $dataRows = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->user ? $student->user->username : '-',
                        'Password' => $student->user ? $student->user->username : '-',
                    ];
                })->toArray();
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

            $exportData = array_merge($headerRows, $dataRows);

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
                ->select('id', 'name', 'nisn', 'class_id', 'parent_id')
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

            $headerRows = [
                ['Akun Orang Tua' => ''],
                ['Jurusan', 'Jurusan' => $request->filled('major') ? $request->major : 'Semua Jurusan'],
                ['Kelas', 'Kelas' => $request->filled('class') ? $request->class : 'Semua Kelas'],
                ['Tingkat', 'Tingkat' => $request->filled('level') ? $request->level : 'Semua Tingkat'],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'NISN' => 'NISN', 'Jurusan' => 'Jurusan', 'Kelas' => 'Kelas', 'Username' => 'Username', 'Password' => 'Password'],
            ];

            $exportData = [];
            if (Major::count() > 0) {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name . $student->class->level . ($student->class->major ? ' ' . $student->class->major->name : '') : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->parent ? $student->parent->username : '-',
                        'Password' => $student->parent ? $student->parent->username : '-',
                    ];
                })->toArray();
            } else {
                $exportData = $students->map(function ($student, $index) {
                    return [
                        'No' => $index + 1,
                        'Nama' => $student->name,
                        'NISN' => $student->nisn,
                        'Kelas' => $student->class ? $student->class->name . $student->class->level . ($student->class->major ? ' ' . $student->class->major->name : '') : '-',
                        'Tingkat' => $student->class ? $student->class->level : '-',
                        'Username' => $student->parent ? $student->parent->username : '-',
                        'Password' => $student->parent ? $student->parent->username : '-',
                    ];
                })->toArray();
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

            $exportData = array_merge($headerRows, $exportData);

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
}
