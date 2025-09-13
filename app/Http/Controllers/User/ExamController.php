<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamAnswerRequest;
use App\Http\Requests\ExamRequest;
use App\Http\Requests\WorkmanshipRequest;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Major;
use App\Models\Period;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\ExamAnswer;
use App\Models\QuestionBank;
use App\Services\ExamScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

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
        $schedules = Schedule::select('id', 'subject_id', 'class_id',)->with(['subject:id,code,name', 'class:id,name,level,major_id', 'class.major:id,name'])->get();

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
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            return abort(403);
        }

        $exam = Exam::with([
            'schedule:id,subject_id,class_id,teacher_id',
            'schedule.meetings:id,schedule_id',
            'schedule.subject:id,name,code',
            'schedule.class:id,name,level,major_id',
            'schedule.class.major:id,name',
        ])
            ->withCount([
                'questions'
            ])
            ->findOrFail($id);
        $this->authorize('view', $exam);

        $schedules = Schedule::select('id', 'subject_id', 'class_id')->with(['subject:id,code,name', 'class:id,name,level,major_id', 'class.major:id,name'])->get();
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
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            return abort(403);
        }

        $exam = Exam::select('id', 'schedule_id')
            ->with('schedule:id,teacher_id')
            ->withCount([
                'questions'
            ])
            ->withSum('questions', 'question_points')
            ->findOrFail($id);
        $this->authorize('view', $exam);

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

            if (!$exam) {
                return $this->sendError(
                    'Ujian tidak ditemukan.',
                    [],
                    404
                );
            }

            $this->authorize('create', $exam);

            $questions = Question::where('questionable_id', $id)
                ->where('questionable_type', QuestionBank::class)
                ->get();

            if ($questions->isEmpty()) {
                return $this->sendError(
                    'Tidak ada soal yang disalin.',
                    [],
                    404
                );
            }

            DB::beginTransaction();

            foreach ($questions as $question) {
                $fileFields = [
                    'question_file',
                    'option_a_image',
                    'option_b_image',
                    'option_c_image',
                    'option_d_image',
                    'option_e_image'
                ];

                $newQuestionData = $question->toArray();

                // Process each file field
                foreach ($fileFields as $field) {
                    if (!empty($question->$field) && Storage::exists($question->$field)) {
                        $originalPath = $question->$field;
                        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                        $newFilename = 'file/ujian/' . Str::random(44) . '.' . $extension;

                        if (Storage::copy($originalPath, $newFilename)) {
                            $newQuestionData[$field] = $newFilename;
                        } else {
                            $newQuestionData[$field] = null;
                        }
                    } else {
                        $newQuestionData[$field] = null;
                    }
                }

                $exam->questions()->create($newQuestionData);
            }

            DB::commit();

            return $this->sendResponse('Soal berhasil disalin.', $exam);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->sendError(
                'Anda tidak memiliki izin untuk mengubah ujian ini.',
                [],
                403
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'Terjadi kesalahan: ' . $e->getMessage(),
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
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            return abort(403);
        }

        $exam = Exam::findOrFail($id);

        $this->authorize('view', $exam);

        if (request()->ajax()) {
            $data = ExamResult::select([
                'exam_results.*',
                'students.name',
                'students.nis',
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

            $data->get();

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
                    $start = optional($row->start_time)->translatedFormat('j M Y H:i');
                    $end   = optional($row->end_time)->translatedFormat('j M Y H:i');
                    $html = '<p class="f-light">'
                        . ($start ?? '-') . ' &middot; ' . ($end ?? '-') .
                        '</p>';
                    return $html;
                })
                ->addColumn('Nilai', function ($row) {
                    $html = '
                    <span class="badge badge-light-primary">' . ($row->formatted_score ?? '-') . '</span>
                    ';
                    return $html;
                })
                ->addColumn('', function ($row) {
                    return '
                        <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                            <a class="reset-result btn btn-danger btn-sm p-1 px-2 rounded-2" data-id="' . $row->id . '" data-exam-id="' . $row->exam_id . '" >
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['Nama', 'Pengerjaan', 'Nilai', ''])
                ->make(true);
        }

        return view('user.exam.result.show', compact('exam'));
    }

    public function info(Request $request, $id, $page = 1)
    {
        if (!$request->user()->hasRole('student') && !$request->user()->hasRole('parent')) {
            return abort(403);
        }

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
        $this->authorize('view', $exam);

        $exam_result = $exam->results();
        if ($request->user()->hasRole('student')) {
            $exam_result->where('student_id', $request->user()->student->id);
        } elseif ($request->user()->hasRole('parent')) {
            $exam_result->where('student_id', $request->user()->parent->id);
        }
        $exam_result = $exam_result->first();

        return view('user.exam.info', [
            'exam' => $exam,
            'exam_result' => $exam_result,
        ]);
    }

    public function workmanship(Request $request, $id)
    {
        if (!auth()->user()->hasRole('student')) {
            return abort(403);
        }

        $exam = Exam::with([
            'questions'
        ])
            ->withCount([
                'questions'
            ])
            ->findOrFail($id);
        $this->authorize('view', $exam);

        $now = now();
        $examResult = ExamResult::with('answers:question_id,answer,exam_result_id')->where('exam_id', $exam->id)
            ->where('student_id', auth()->user()->student->id)
            ->first();

        if (!$examResult) {
            return redirect()->route('user.exam.info', $exam->id);
        }

        if ($examResult->status === 'completed') {
            return redirect()->route('user.exam.workmanship.result', $exam->id)
                ->with('error', 'Anda sudah mengerjakan ujian ini.');
        }

        if ($examResult->end_time && $now->gt($examResult->end_time)) {
            return redirect()->route('user.exam.workmanship.result', $exam->id)
                ->with('error', 'Waktu ujian telah berakhir.');
        }

        $order = session("exam_order_{$exam->id}");

        if (!$order) {
            $order = $exam->questions->shuffle()->toArray();
            session(["exam_order_{$exam->id}" => $order]);
        }

        $questions = $order;

        return view('user.exam.workmanship', compact('exam', 'examResult', 'questions'));
    }

    public function setAnswerByExamResult(ExamAnswerRequest $request, $id)
    {
        try {
            if (!auth()->user()->hasRole('student')) {
                return abort(403);
            }

            $validated = $request->validated();

            $student = auth()->user()->student;

            $exam_result = ExamResult::where('student_id', $student->id)->where('exam_id', $id)->firstOrFail();
            $exam_result->answers()->updateOrCreate(
                [
                    'question_id' => $validated['question_id'],
                ],
                [
                    'answer' => $validated['answer'],
                ]
            );

            return $this->sendResponse('Jawab berhasil disimpan.');
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function getRandomQuestions(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $exam = Exam::with([
                'questions'
            ])
                ->findOrFail($id);
            $this->authorize('view', $exam);

            $order = session("exam_order_{$exam->id}");

            if (!$order) {
                $order = $exam->questions->shuffle()->toArray();
                session(["exam_order_{$exam->id}" => $order]);
            }

            $index = $request->query('q', 1);
            $question = $order[$index - 1] ?? null;

            if (!$question) {
                return $this->sendError('Soal tidak ditemukan.', [], 404);
            }

            $answer = ExamAnswer::where('question_id', $question['id'])->first();

            $response = [
                'exam' => $exam,
                'question' => $question,
                'index' => $index,
                'total' => count($order),
                'answer' => $answer,
            ];

            return $this->sendResponse('Soal ditemukan.', $response);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function examStart(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $exam = Exam::withCount('questions')->findOrFail($id);
            $this->authorize('view', $exam);

            $student = auth()->user()->student;

            $existingExamResult = ExamResult::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->first();


            $now = now();
            if ($existingExamResult) {
                if ($existingExamResult->status === 'completed') {
                    return $this->sendError('Anda sudah memulai ujian ini.', [], 403);
                }
                if ($now->gt($existingExamResult->end_time)) {
                    return $this->sendError('Waktu ujian telah berakhir.', [], 403);
                }

                return $this->sendResponse('Ujian dimulai!', $existingExamResult, 200);
            }

            if ($now->lt($exam->start_time)) {
                return $this->sendError('Ujian belum dimulai.', [], 403);
            }

            if ($now->gt($exam->end_time)) {
                return $this->sendError('Ujian telah berakhir.', [], 403);
            }

            if ($exam->questions_count <= 0) {
                return $this->sendError('Pertanyan belum tersedia.', [], 403);
            }

            $examResult = ExamResult::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'start_time' => $now,
                'end_time' => $exam->duration ? $now->copy()->addMinutes($exam->duration) : null,
                'status' => 'in_progress',
            ]);

            session()->forget("exam_order_{$exam->id}");

            return $this->sendResponse('Ujian dimulai. Semoga sukses!', $examResult, 201);
        } catch (\Exception $e) {
            Log::error('Error memulai ujian: ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function workmanshipSubmit(WorkmanshipRequest $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $exam = Exam::findOrFail($id);
            $this->authorize('view', $exam);

            $student = auth()->user()->student;

            $examResult = ExamResult::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$examResult) {
                return $this->sendError('Anda belum memulai ujian ini.', [], 403);
            }

            $validated = $request->validated();

            $answersData = [];
            if (isset($validated['answered'])) {
                foreach ($validated['answered'] as $answer) {
                    $answersData[] = [
                        'question_id' => $answer['question_id'],
                        'exam_result_id' => $examResult->id,
                        'answer' => $answer['answer']
                    ];
                }
            }

            $examResult->answers()->upsert(
                $answersData,
                ['question_id', 'exam_result_id'],
                ['answer']
            );

            app(ExamScoringService::class)->autoScoreAndSave($examResult, $student->id);

            return $this->sendResponse('Ujian selesai. Terima kasih!', $examResult);
        } catch (\Exception $e) {
            Log::error('Error : ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function workmanshipResult(Request $request, $id)
    {
        if (!$request->user()->hasRole('student') && !$request->user()->hasRole('parent')) {
            return abort(403);
        }

        $exam = Exam::withCount('questions')->findOrFail($id);
        $this->authorize('view', $exam);

        $student = null;
        if ($request->user()->hasRole('student')) {
            $student = auth()->user()->student;
        } elseif ($request->user()->hasRole('parent')) {
            $student = auth()->user()->parent;
        }

        $examResult = ExamResult::withCount([
            'answers as correct_answers_count' => fn($q) => $q->join('questions', 'exam_answers.question_id', '=', 'questions.id')
                ->whereColumn('exam_answers.answer', 'questions.correct_answer')
        ])->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$examResult) {
            return redirect()->route('user.exam.info', $exam->id)
                ->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        if (now()->gt($examResult->end_time) && $examResult->status !== 'completed') {
            app(ExamScoringService::class)->autoScoreAndSave($examResult, $student->id);
            return $this->sendError('Waktu ujian telah berakhir.', [], 403);
        }

        if ($examResult->status !== 'completed') {
            return redirect()->route('user.exam.info', $exam->id)
                ->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        return view('user.exam.workmanship_result', compact('examResult', 'student', 'exam'));
    }

    public function resetResult(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
                return abort(403);
            }

            $exam = Exam::findOrFail($id);

            $this->authorize('update', $exam);

            $exam->results()->delete();

            return $this->sendResponse('Hasil ujian berhasil direset.');
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function resetResultById(Request $request, $id, $exam_result_id)
    {
        try {
            if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
                return abort(403);
            }

            $exam = Exam::findOrFail($id);

            $this->authorize('update', $exam);

            $exam->results()->where('id', $exam_result_id)->delete();

            return $this->sendResponse('Hasil ujian berhasil direset.');
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function exportResult(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
                return abort(403);
            }

            $exam = Exam::with([
                'results',
                'results.student',
                'schedule:id,subject_id,class_id,teacher_id,period_id',
                'schedule.subject:id,code,name',
                'schedule.period:id,academic_year,semester',
                'schedule.class:id,name,level,major_id',
                'schedule.class.major:id,name',
                'schedule.teacher:id,name,nip',
            ])->findOrFail($id);

            $this->authorize('update', $exam);

            $headerRows = [
                ['HASIL UJIAN' => ''],
                ['Periode', 'Periode' => $exam->schedule->period ? $exam->schedule->period->academic_year . ' ' . Helper::getSemesterLabel($exam->schedule->period->semester) : '-'],
                ['Mata Pelajaran', 'Mata Pelajaran' => $exam->schedule->subject ? $exam->schedule->subject->code . ' - ' . strtoupper($exam->schedule->subject->name) : '-'],
                ['Kelas', 'Kelas' => $exam->schedule->class ? $exam->schedule->class->name . $exam->schedule->class->level . ($exam->schedule->class->major ? ' ' . $exam->schedule->class->major->name : '') : '-'],
                ['Guru', 'Guru' => $exam->schedule->teacher ? $exam->schedule->teacher->name . ' (' . $exam->schedule->teacher->nip . ')' : '-'],
                ['Jenis Ujian', 'Jenis Ujian' => Helper::getExamTypeLabel($exam->exam_type) ?: '-'],
                ['Waktu Ujian', 'Waktu Ujian' => $exam->start_time && $exam->end_time ? $exam->start_time->translatedFormat('j F Y H:i') . ' - ' . $exam->end_time->translatedFormat('j F Y H:i') : '-'],
                ['Durasi', 'Durasi' => $exam->duration ? $exam->duration . ' menit' : '-'],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'NIS' => 'NIS', 'Nilai' => 'Nilai'],
            ];

            $exportData = $exam->results->map(function ($result, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $result->student ? $result->student->name : '-',
                    'NIS' => $result->student ? $result->student->nis : '-',
                    'Nilai' => $result->formatted_score ? $result->formatted_score : '-',
                ];
            })->toArray();

            $filename = 'Nilai Ujian ' . now()->format('Y-m-d') . '.xlsx';

            $exportData = array_merge($headerRows, $exportData);

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
}
