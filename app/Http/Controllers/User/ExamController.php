<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Major;
use App\Models\Period;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);

        $exams = Exam::with([
            'schedule:id,subject_id,class_id',
            'schedule.meetings:id,schedule_id',
            'schedule.subject:id,name,code',
            'schedule.class:id,name,level,major_id',
            'schedule.class.major:id,name',
            'results' => function ($q) use ($request) {
                if ($request->user()->hasRole('student')) {
                    $q->select(["id", "exam_id"])->where('student_id', $request->user()->student->id);
                } elseif ($request->user()->hasRole('parent')) {
                    $q->select(["id", "exam_id"])->where('student_id', $request->user()->parent->id);
                }
            }
        ])
            ->filter($request->all())
            ->filterByPermission($request->user())
            ->paginate(10);

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
        $examTypes = [
            ['value' => 'Midterm', 'label' => 'UTS'],
            ['value' => 'Final', 'label' => 'UAS'],
        ];
        $schedules = Schedule::select('id', 'subject_id')->with(['subject:id,code,name'])->get();

        return view('user.exam.index', compact('exams', 'schedules', 'classes', 'classLevels', 'classNames', 'majors', 'hasMajors', 'subjects', 'periods', 'examTypes', 'activePeriod'));
    }

    public function store(ExamRequest $request)
    {
        try {
            $this->authorize('create', Exam::class);

            $validated = $request->validated();

            $task = Exam::create($validated);

            return $this->sendResponse('Ujian berhasil ditambahkan', $task, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $exam = Exam::with([
            'schedule:id,subject_id,class_id',
            'schedule.meetings:id,schedule_id',
            'schedule.subject:id,name,code',
            'schedule.class:id,name,level,major_id',
            'schedule.class.major:id,name',
        ])
            ->withCount([
                'questions'
            ])
            ->findOrFail($id);
        $this->authorize('update', $exam);

        $schedules = Schedule::select('id', 'subject_id')->with(['subject:id,code,name'])->get();
        $examTypes = [
            ['value' => 'Midterm', 'label' => 'UTS'],
            ['value' => 'Final', 'label' => 'UAS'],
        ];

        return view('user.exam.show', compact('exam', 'examTypes', 'schedules'));
    }

    public function edit($id)
    {
        try {
            $exam = Exam::find($id);
            $this->authorize('view', $exam);

            if (!$exam) {
                return $this->sendError('Ujian tidak ditemukan.', [], 404);
            }

            return $this->sendResponse(
                'Ujian berhasil ditemukan.',
                $exam
            );
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function update(ExamRequest $request, $id)
    {
        try {
            $exam = Exam::find($id);
            $this->authorize('update', $exam);

            if (!$exam) {
                return $this->sendError('Ujian tidak ditemukan.', [], 404);
            }

            $validated = $request->validated();

            $exam->update($validated);

            return $this->sendResponse('Ujian berhasil diedit.', $exam, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->authorize('delete', $exam);

            DB::beginTransaction();

            $exam->questions()->delete();

            $exam->delete();

            DB::commit();

            return $this->sendResponse('Ujian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function showQuestion(Request $request, $id)
    {
        $exam = Exam::select('id')
            ->withCount([
                'questions'
            ])
            ->withSum('questions', 'question_points')
            ->findOrFail($id);
        $this->authorize('update', $exam);

        $questionsQuery = $exam->questions();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $questionsQuery->where('question_text', 'like', "%{$search}%");
        }

        $questions = $questionsQuery->paginate(5);

        $majors = Major::with(['classes' => function ($query) {
            $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
        }])->select('id', 'name')->orderBy('name', 'asc')->get();
        $subjects = Subject::select('id', 'name', 'curriculum_id')->with(['curriculum:id,name'])->get();

        return view('user.exam.question.show', compact('exam', 'questions', 'majors', 'subjects'));
    }

    public function copyQuestions(Request $request, $exam_id, $id)
    {
        try {
            $exam = Exam::find($exam_id);
            $this->authorize('update', $exam);

            if (!$exam) {
                return $this->sendError(
                    'Ujian tidak ditemukan.',
                    [],
                    404
                );
            }

            $questions = Question::where('questionable_id', $id)->get();

            $exam->questions()->createMany($questions->toArray());

            return $this->sendResponse('Soal berhasil disalin.', $exam);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function showResult(Request $request, $id)
    {
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            return abort(403);
        }

        $exam = Exam::findOrFail($id);

        $this->authorize('view', $exam);

        if (request()->ajax()) {
            $data = ExamResult::select([
                'exam_results.*'
            ])
                ->leftJoin('students', 'student_id', '=', 'students.id')
                ->with('grader:id,name')
                ->where('exam_id', $exam->id);

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
                ->addColumn('Pengerjaan', function ($row) {
                    $html = '
                        <p class="f-light">' . $row->start_time->translatedFormat('j M Y H:i') . ' - ' . $row->end_time->translatedFormat('j M Y H:i') . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Nilai', function ($row) {
                    $html = '
                    <span class="badge badge-light-primary">' . ($row->formatted_score ?? '-') . '</span>
                    ';
                    return $html;
                })
                ->rawColumns(['Nama', 'Pengumpulan', 'Nilai'])
                ->make(true);
        }

        return view('user.exam.result.show', compact('exam'));
    }
}
