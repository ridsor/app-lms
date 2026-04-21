<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Major;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;


class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with([
            'meeting:id,schedule_id',
            'meeting.schedule:id,subject_id,class_id',
            'meeting.schedule.meetings:id,schedule_id',
            'meeting.schedule.subject:id,name,code',
            'meeting.schedule.class:id,name,level,major_id',
            'meeting.schedule.class.major:id,name',
            'submissions' => function ($q) use ($request) {
                if ($request->user()->hasRole('student')) {
                    $q->select(["id", "task_id"])->where('student_id', $request->user()->student->id);
                } elseif ($request->user()->hasRole('parent')) {
                    $q->select(["id", "task_id"])->where('student_id', $request->user()->parent->id);
                }
            }
        ])
            ->withCount([
                'submissions as not_yet_rated' => function ($query) {
                    $query->whereNull('score');
                }
            ])
            ->filter($request->all())
            ->filterByPermission($request->user())
            ->orderBy('start_time', 'DESC')
            ->get() // First get all results
            ->map(function ($task) { // Modify the collection
                $index = $task->meeting->schedule->meetings->search(function ($item) use ($task) {
                    return $item->id == $task->meeting->id;
                });
                $task->meeting->meeting_at = $index + 1;
                return $task;
            });

        // Manually paginate the modified collection
        $page = request()->get('page', 1);
        $perPage = 10;
        $tasks = new \Illuminate\Pagination\LengthAwarePaginator(
            $tasks->forPage($page, $perPage),
            $tasks->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $this->authorize('viewAny', Task::class);

        $activePeriod = Period::where('status', true)->first();
        $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
        $classNames = SchoolClass::select('name')->distinct()->orderBy('name', 'asc')->get();
        $majors = Major::with(['classes' => function ($query) {
            $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
        }])->select('id', 'name')->orderBy('name', 'asc')->get();
        $classes = SchoolClass::select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc')->get();
        $hasMajors = Major::count() > 0;
        $subjects = Subject::select('id', 'name', 'curriculum_id')->with(['curriculum:id,name'])->get();
        $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();
        $taskTypes = [
            ['value' => 'individual', 'label' => 'Individu'],
            ['value' => 'group', 'label' => 'Kelompok'],
        ];

        return view('user.task.index', compact('tasks', 'classes', 'classLevels', 'classNames', 'majors', 'hasMajors', 'subjects', 'periods', 'taskTypes', 'activePeriod'));
    }

    public function show(Request $request, $task_id)
    {
        $task = Task::with([
            'meeting:id,schedule_id',
            'meeting.schedule:id,subject_id,teacher_id,class_id',
            'meeting.schedule.meetings:id,schedule_id',
            'meeting.schedule.subject:id,name,code',
            'meeting.schedule.class:id,name,level,major_id',
            'meeting.schedule.class.major:id,name',
        ])
            ->withCount([
                'submissions as not_yet_rated' => function ($query) {
                    $query->whereNull('score');
                }
            ])
            ->findOrFail($task_id);
        $this->authorize('update', $task);

        $index = $task->meeting->schedule->meetings->search(function ($item) use ($task) {
            return $item->id == $task->meeting->id;
        });
        $task->meeting->meeting_at = $index + 1;

        $taskType = [
            ['value' => 'individual', 'label' => 'Individu'],
            ['value' => 'group', 'label' => 'Kelompok'],
        ];

        return view('user.task.show', compact('task', 'taskType'));
    }

    public function collection(Request $request, $task_id)
    {
        $task = Task::with([
            'meeting:id,schedule_id',
            'meeting.schedule:id,subject_id,teacher_id,class_id',
            'meeting.schedule.subject:id,name,code',
            'meeting.schedule.class:id,name,level,major_id',
            'meeting.schedule.class.major:id,name',
            'submissions:id,task_id,submitted_at'
        ])
            ->withCount([
                'submissions as not_yet_rated' => function ($query) {
                    $query->whereNull('score');
                }
            ])
            ->findOrFail($task_id);

        $this->authorize('update', $task);

        if ($request->ajax()) {
            $data = TaskSubmission::select([
                'task_id',
                'task_submissions.id',
                'students.name',
                'students.nis',
                'task_submissions.submitted_at',
                'task_submissions.graded_at',
                'task_submissions.score',
                'task_submissions.graded_by',
            ])
                ->leftJoin('students', 'student_id', '=', 'students.id')
                ->with('grader:id,name')
                ->orderBy('submitted_at', 'asc')
                ->where('task_id', $task->id);

            if ($request->filled('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $data->where(function ($q) use ($search) {
                    $q->whereFullText('students.name', $search);
                });
            }

            return Datatables::of($data)
                ->addColumn('Nama', function ($row) {
                    $html = '
                    <div>
                    <p class="f-light mb-0">
                        ' . $row->name  . '</p>
                    <p class="f-light mb-0">' . $row->nis  . '</p> 
                    </div>
                    ';
                    return $html;
                })
                ->addColumn('Pengumpulan', function ($row) {
                    $html = '
                        <p class="f-light">' . $row->submitted_at->translatedFormat('j M Y H:i') . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Nilai', function ($row) {
                    $html = '
                    <span class="badge badge-light-primary">' . ($row->formatted_score ?? '-') . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Penilaian', function ($row) {
                    $html = '
                    <p class="f-light">' . (optional($row->graded_at)->translatedFormat('j M Y H:i') ?? '-') . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Penilai', function ($row) {
                    $html = '
                    <p class="f-light">' . ($row->grader->name  ?? '-') . '</p>
                    ';
                    return $html;
                })
                ->rawColumns(['Nama', 'Pengumpulan', 'Nilai', 'Penilaian', 'Penilai'])
                ->make(true);
        } else {
            return view('user.task.show-collection', compact('task'));
        }
    }

    public function exportResult(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
                return abort(403);
            }

            $task = Task::with([
                'meeting:id,schedule_id',
                'meeting.schedule:id,subject_id,teacher_id,class_id,period_id',
                'meeting.schedule.subject:id,name,code',
                'meeting.schedule.class:id,name,level,major_id',
                'meeting.schedule.class.major:id,name',
                'meeting.schedule.period:id,academic_year,semester',
                'submissions',
                'submissions.student:id,name,nis',
            ])->findOrFail($id);

            $this->authorize('update', $task);

            $headerRows = [
                ['HASIL TUGAS' => ''],
                ['Periode', 'Periode' => $task->meeting->schedule->period ? $task->meeting->schedule->period->academic_year . ' ' . Helper::getSemesterLabel($task->meeting->schedule->period->semester) : '-'],
                ['Mata Pelajaran', 'Mata Pelajaran' => $task->meeting->schedule->subject ? $task->meeting->schedule->subject->code . ' - ' . strtoupper($task->meeting->schedule->subject->name) : '-'],
                ['Kelas', 'Kelas' => $task->meeting->schedule->class ? $task->meeting->schedule->class->name . $task->meeting->schedule->class->level . ($task->meeting->schedule->class->major ? ' ' . $task->meeting->schedule->class->major->name : '') : '-'],
                ['Guru', 'Guru' => $task->meeting->schedule->teacher ? $task->meeting->schedule->teacher->name . ' (' . $task->meeting->schedule->teacher->nip . ')' : '-'],
                ['Jenis Tugas', 'Jenis Tugas' => Helper::getTaskTypeLabel($task->type) ?: '-'],
                ['Waktu Tugas', 'Waktu Tugas' => $task->start_time && $task->end_time ? $task->start_time->translatedFormat('j F Y H:i') . ' - ' . $task->end_time->translatedFormat('j F Y H:i') : '-'],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'NIS' => 'NIS', 'Nilai' => 'Nilai'],
            ];

            $exportData = $task->submissions->map(function ($result, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $result->student ? $result->student->name : '-',
                    'NIS' => $result->student ? $result->student->nis : '-',
                    'Nilai' => $result->formatted_score ? $result->formatted_score : '-',
                ];
            })->toArray();

            $filename = 'Nilai Tugas ' . now()->format('Y-m-d') . '.xlsx';

            $exportData = array_merge($headerRows, $exportData);

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }

    public function edit($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('view', $task);

            return $this->sendResponse(
                'Tugas berhasil ditemukan.',
                $task
            );
        } catch (\Exception $e) {
            return $this->sendError('Tugas tidak ditemukan.', [], 404);
        }
    }

    public function store(TaskRequest $request, $meeting_id)
    {
        try {
            $this->authorize('create', Task::class);

            $validated = $request->validated();

            if ($request->hasFile('file_path')) {
                $filePath = $request->file('file_path')->store('file/tugas');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            $validated['meeting_id'] = $meeting_id;

            $task = Task::create($validated);

            return $this->sendResponse('Tugas berhasil disimpan', $task, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(TaskRequest $request, $task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('update', $task);

            $validated = $request->validated();
            $validated = array_filter($validated, function ($value) {
                return !is_null($value);
            });

            if ($validated['deletedFile']) {
                if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                    Storage::delete($task->file_path);
                }
                $validated['file_path'] = null;
                $validated['file_name'] = null;
                $validated['file_size'] = null;
            }

            if ($request->hasFile('file_path')) {
                if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                    Storage::delete($task->file_path);
                }
                $filePath = $request->file('file_path')->store('file/tugas');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            if (!$validated['allow_late_submission']) {
                $validated['late_submission_time'] = null;
            }

            $task->update($validated);

            return $this->sendResponse('Tugas berhasil diperbarui', $task);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('delete', $task);

            if (!empty($task->file_path) && Storage::exists($task->file_path)) {
                Storage::delete($task->file_path);
            }

            $task->delete();

            return $this->sendResponse('Tugas berhasil dihapus');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function getFile(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        if (!$request->hasValidSignature()) {
            $this->authorize('view', $task);
        }

        if (!empty($task->file_path) && Storage::exists($task->file_path)) {
            $path = storage_path('app/' . $task->file_path);
            return response()->file($path, [
                'Content-Type' => Storage::mimeType($task->file_path),
                'Content-Disposition' => 'inline; filename="' . $task->file_name . '"'
            ]);
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function downloadFile($task_id)
    {
        $task = Task::findOrFail($task_id);
        $this->authorize('view', $task);

        if (!empty($task->file_path) && Storage::exists($task->file_path)) {
            return Storage::download($task->file_path, $task->file_name);
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function value_displayed(Request $request, $task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $this->authorize('update', $task);

            $task->update([
                'value_displayed' => !$task->value_displayed
            ]);

            return $this->sendResponse('Tugas berhasil ditampilkan nilai.');
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }
}
