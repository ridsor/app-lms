<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamAnswerRequest;
use App\Http\Requests\ExamRequest;
use App\Http\Requests\WorkmanshipRequest;
use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Major;
use App\Models\Period;
use App\Models\MultipleQuestion;
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
use Illuminate\Pagination\LengthAwarePaginator;

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
        ])->selectRaw('exams.*,(
                SELECT COUNT(*) FROM exam_answers 
                WHERE exam_answers.score IS NULL
            ) as not_yet_rated')
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
        $schedules = Schedule::select('id', 'subject_id', 'class_id')->with(['subject:id,code,name', 'class:id,name,level,major_id', 'class.major:id,name'])->where('period_id', $activePeriod->id ?? 0)->get();

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
        ])->selectRaw('exams.*,(
                SELECT COUNT(*) FROM exam_answers 
                WHERE exam_answers.score IS NULL
            ) as not_yet_rated')
            ->findOrFail($id);
        $this->authorize('view', $exam);

        $activePeriod = Period::where('status', true)->first();
        $schedules = Schedule::select('id', 'subject_id', 'class_id')->with(['subject:id,code,name', 'class:id,name,level,major_id', 'class.major:id,name'])->where('period_id', $activePeriod->id ?? 0)->get();
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
            ->with('schedule:id,teacher_id', 'multipleQuestions', 'essayQuestions')
            ->findOrFail($id);

        $this->authorize('view', $exam);

        $multipleQuery = $exam->multipleQuestions();
        $essayQuery = $exam->essayQuestions();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $multipleQuery->where('question_text', 'like', "%{$search}%");
            $essayQuery->where('question_text', 'like', "%{$search}%");
        }

        $questions = $multipleQuery->get()
            ->concat($essayQuery->get())
            ->sortByDesc('created_at')
            ->values();

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $paginatedQuestions = new LengthAwarePaginator(
            $questions->forPage($currentPage, $perPage),
            $questions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $majors = Major::with(['classes' => function ($query) {
            $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
        }])->select('id', 'name')->orderBy('name', 'asc')->get();

        $subjects = Subject::select('id', 'name', 'curriculum_id')
            ->with(['curriculum:id,name'])
            ->get();

        return view('user.exam.question.show', [
            'exam' => $exam,
            'questions' => $paginatedQuestions,
            'majors' => $majors,
            'subjects' => $subjects
        ]);
    }

    public function copyQuestions(Request $request, $exam_id, $id)
    {
        try {
            $exam = Exam::find($exam_id);

            if (!$exam) {
                return $this->sendError('Ujian tidak ditemukan.', [], 404);
            }

            $this->authorize('create', $exam);

            // 1. Ambil soal Multiple Choice dari Bank Soal
            $multipleQuestions = MultipleQuestion::where('questionable_id', $id)
                ->where('questionable_type', QuestionBank::class)
                ->get();

            // 2. Ambil soal Essay dari Bank Soal
            $essayQuestions = EssayQuestion::where('questionable_id', $id)
                ->where('questionable_type', QuestionBank::class)
                ->get();

            // Gabungkan untuk mengecek apakah ada data yang disalin
            $totalQuestions = $multipleQuestions->count() + $essayQuestions->count();

            if ($totalQuestions === 0) {
                return $this->sendError('Tidak ada soal yang disalin.', [], 404);
            }

            DB::beginTransaction();

            // 3. Proses Salin Soal Pilihan Ganda (Multiple Choice)
            foreach ($multipleQuestions as $question) {
                $fileFields = [
                    'question_file',
                    'option_a_image',
                    'option_b_image',
                    'option_c_image',
                    'option_d_image',
                    'option_e_image'
                ];

                $newQuestionData = $question->toArray();

                // Hapus ID agar digenerate baru oleh database
                unset($newQuestionData['id']);

                // Copy File Fisik jika ada
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

                // Insert ke tabel multiple_questions terkait exam ini
                $exam->multipleQuestions()->create($newQuestionData);
            }

            // 4. Proses Salin Soal Essay
            foreach ($essayQuestions as $question) {
                // Essay biasanya hanya punya question_file
                $fileFields = ['question_file'];

                $newQuestionData = $question->toArray();

                // Hapus ID agar digenerate baru oleh database
                unset($newQuestionData['id']);

                // Copy File Fisik jika ada
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

                // Insert ke tabel essay_questions terkait exam ini
                $exam->essayQuestions()->create($newQuestionData);
            }

            DB::commit();

            return $this->sendResponse('Soal berhasil disalin.', $exam);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->sendError('Anda tidak memiliki izin untuk mengubah ujian ini.', [], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan: ' . $e->getMessage(), [], 500);
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

        $exam = Exam::selectRaw('exams.*, (
                SELECT COUNT(*) FROM exam_answers 
                WHERE exam_answers.score IS NULL
            ) as not_yet_rated')->with(['results'])->withCount(['results'])->findOrFail($id);

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
                ->addColumn('Nama', fn($row) => '<div class="product-names"><p>' . $row->name . '</p></div>')
                ->addColumn('NIS', fn($row) => '<p class="f-light">' . $row->nis . '</p>')
                ->addColumn('Nilai', fn($row) => '<span class="badge badge-light-primary">' . ($row->formatted_score) . '</span>')
                ->addColumn('Status', function ($row) {
                    return Helper::getExamStatusLabel($row->status);
                })
                ->addColumn('Pengerjaan', fn($row) => $row->end_time ? $row->end_time->translatedFormat('d/m/Y H:i') : '-')
                ->rawColumns(['Nama', 'NIS', 'Nilai', 'Status', 'Pengerjaan'])
                ->make(true);
        }

        return view('user.exam.result.show', compact('exam'));
    }

    public function info(Request $request, $id)
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
            'multipleQuestions',
            'essayQuestions'
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
            'multipleQuestions',
            'essayQuestions',
        ])
            ->findOrFail($id);
        $this->authorize('view', $exam);

        $now = now();
        $examResult = ExamResult::with('answers')->where('exam_id', $exam->id)
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
            if ($exam->is_shuffle_questions) {
                $order = Helper::fisherYatesShuffle($exam->questions->toArray());
            } else {
                $order = $exam->questions->toArray();
            }
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
                    'questionable_id' => $validated['question_id'],
                    'questionable_type' => $validated['question_type'] === 'multiple' ? MultipleQuestion::class : EssayQuestion::class,
                ],
                [
                    'answer' => $validated['answer'],
                    'answered_at' => now(),
                ]
            );

            return $this->sendResponse('Jawab berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error menyimpan jawaban: ' . $e->getMessage());
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

            $exam = Exam::findOrFail($id);
            $this->authorize('view', $exam);

            $order = session("exam_order_{$exam->id}");

            if (!$order) {
                if ($exam->is_shuffle_questions) {
                    // $order = $exam->questions->shuffle()->toArray();
                    $order = Helper::fisherYatesShuffle($exam->questions->toArray());
                } else {
                    $order = $exam->questions->toArray();
                }

                session(["exam_order_{$exam->id}" => $order]);
            }

            $index = $request->query('q', 1);
            $question = $order[$index - 1] ?? null;

            if (!$question) {
                return $this->sendError('Soal tidak ditemukan.', [], 404);
            }

            $student = auth()->user()->student;
            $examResult = ExamResult::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->first();

            $answer = $examResult ? $examResult->answers()
                ->where('questionable_id', $question['id'])
                ->where('questionable_type', $question['question_type'] === 'multiple' ? MultipleQuestion::class : EssayQuestion::class)
                ->first() : null;

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

            $exam = Exam::findOrFail($id);
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
                if ($existingExamResult->end_time && $now->gt($existingExamResult->end_time)) {
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

            if (($exam?->multipleQuestions->count() + $exam?->essayQuestions->count() ?? 0) <= 0) {
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

            DB::beginTransaction();

            if (isset($validated['answered']) && is_array($validated['answered'])) {
                foreach ($validated['answered'] as $answer) {
                    $examResult->answers()->updateOrCreate(
                        [
                            'questionable_id' => $answer['question_id'],
                            'questionable_type' => $answer['question_type'] === "multiple" ? MultipleQuestion::class : EssayQuestion::class,
                        ],
                        [
                            'answer' => $answer['answer'],
                            'answered_at' => now(),
                        ]
                    );
                }
            }

            app(ExamScoringService::class)->saveScore($examResult, $student->id);

            DB::commit();

            return $this->sendResponse('Ujian selesai. Terima kasih!', $examResult);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submit ujian: ' . $e->getMessage());
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

        $exam = Exam::findOrFail($id);
        $this->authorize('view', $exam);

        $student = null;
        if ($request->user()->hasRole('student')) {
            $student = auth()->user()->student;
        } elseif ($request->user()->hasRole('parent')) {
            $student = auth()->user()->parent;
        }

        $examResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$examResult) {
            return redirect()->route('user.exam.info', $exam->id)
                ->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        if (($examResult->end_time && now()->gt($examResult->end_time)) && $examResult->status !== 'completed') {
            app(ExamScoringService::class)->saveScore($examResult, $student->id);
            return $this->sendError('Waktu ujian telah berakhir.', [], 403);
        }

        if ($examResult->status !== 'completed') {
            return redirect()->route('user.exam.info', $exam->id)
                ->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        $totalPoints = $exam->multipleQuestions->sum('question_points') + $exam->essayQuestions->sum('question_points');
        $totalCorrectAnswers = $examResult->answers->sum('score');

        // cek jika jawaban masih ada yang null, maka berikan pesan untuk menunggu penilaian guru
        $hasPendingScores = $examResult->answers()->whereNull('score')->exists();

        return view('user.exam.workmanship_result', compact('examResult', 'student', 'exam', 'totalPoints', 'totalCorrectAnswers', 'hasPendingScores'));
    }

    public function resetResult(Request $request, $id)
    {
        // 1. Authorization and binding OUTSIDE the try-catch
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            abort(403, 'Akses ditolak.');
        }

        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        // 2. Wrap database operations in a transaction
        try {
            DB::beginTransaction();

            // 3. OPTIMIZED BULK DELETION (Transforms 1000+ queries into just 2 queries)
            $resultIds = $exam->results()->pluck('id');

            if ($resultIds->isNotEmpty()) {
                // NOTE: Replace 'exam_result_id' with your actual foreign key column name in the exam_answers table
                DB::table('exam_answers')->whereIn('exam_result_id', $resultIds)->delete();

                // Delete the results
                $exam->results()->delete();
            }

            activity()
                ->useLog('Ujian')
                ->performedOn($exam)
                ->causedBy($request->user())
                ->log('Pengguna ' . $request->user()->name . ' mereset semua hasil ujian untuk ujian ID: ' . $exam->id);

            DB::commit();

            return $this->sendResponse('Semua hasil ujian berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Optional but recommended: Log the actual error for debugging
            Log::error('Failed to reset all exam results: ' . $e->getMessage());

            return $this->sendError(
                'Terjadi kesalahan server. Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function resetResultById(Request $request, $id, $exam_result_id)
    {
        // 1. Move Authorization & Model Binding OUTSIDE the try-catch block.
        // This allows Laravel's exception handler to properly return 403 and 404 errors.
        if (!$request->user()->hasRole('teacher') && !$request->user()->hasRole('operator')) {
            abort(403, 'Akses ditolak.');
        }

        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        // 2. Only use try-catch for the actual database manipulation
        try {
            DB::beginTransaction();

            // 3. Find the specific result directly (throws 404 if not found)
            $result = $exam->results()->findOrFail($exam_result_id);

            // Delete child records first, then the parent
            $result->answers()->delete();
            $result->delete();

            activity()
                ->useLog('Ujian')
                ->performedOn($exam)
                ->causedBy($request->user())
                ->log('Pengguna ' . $request->user()->name . ' mereset hasil ujian untuk ujian ID: ' . $exam->id . ', hasil ujian ID: ' . $exam_result_id);

            DB::commit();

            return $this->sendResponse('Hasil ujian berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack(); // Revert changes if anything fails

            // Recommended: Log the actual error so you can debug it later
            // \Log::error('Failed to reset exam result: ' . $e->getMessage());

            return $this->sendError(
                'Terjadi kesalahan server. Silakan coba lagi.',
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
                ['Jenis Ujian', 'Jenis Ujian' => Helper::getExamTypeLabel($exam->type) ?: '-'],
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

    public function evaluation(Request $request, $exam_id, $page = 1)
    {
        $query = ExamResult::where('exam_id', $exam_id)
            ->with(['student', 'exam', 'answers']);

        $exam_results = $query->simplePaginate(1, ['*'], 'page', $page);
        $exam_result = $query->simplePaginate(1, ['*'], 'page', $page)->first();

        $exam = $exam_result->exam;
        $this->authorize('update', $exam);

        $multipleQuery = $exam->multipleQuestions();
        $essayQuery = $exam->essayQuestions();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $multipleQuery->where('question_text', 'like', "%{$search}%");
            $essayQuery->where('question_text', 'like', "%{$search}%");
        }

        $multipleQuestions = $multipleQuery->get()->map(function ($q) {
            $q->q_type = 'App\Models\MultipleQuestion';
            return $q;
        });

        $essayQuestions = $essayQuery->get()->map(function ($q) {
            $q->q_type = 'App\Models\EssayQuestion';
            return $q;
        });

        $questions = $multipleQuestions
            ->concat($essayQuestions)
            ->sortByDesc('created_at')
            ->values();

        $studentAnswers = $exam_result->answers->keyBy(function ($answer) {
            return $answer->questionable_type . '_' . $answer->questionable_id;
        });

        $questions->transform(function ($question) use ($studentAnswers) {
            // Ini akan membuat properti baru 'student_answer' di object $question
            $matchingKey = $question->q_type . '_' . $question->id;
            $question->student_answer = $studentAnswers->get($matchingKey);
            return $question;
        });

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $paginatedQuestions = new LengthAwarePaginator(
            $questions->forPage($currentPage, $perPage),
            $questions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('user.exam.evaluation', [
            'exam_result' => $exam_result,
            'exam' => $exam,
            'exam_results' => $exam_results,
            'questions' => $paginatedQuestions
        ]);
    }

    public function updateAnswerScore(Request $request, $id, $answer_id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        $answer = ExamAnswer::with('questionable')->find($answer_id);

        if (!$answer) {
            return $this->sendError(
                'Data jawaban tidak ditemukan.',
                [],
                404
            );
        }

        $maxPoints = $answer->questionable ? $answer->questionable->question_points : 0;

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $maxPoints
        ], [
            'score.max' => "Skor tidak boleh lebih dari {$maxPoints} poin untuk soal ini.",
            'score.min' => 'Skor tidak boleh bernilai negatif.'
        ]);

        $answer->score = $request->score;
        $answer->save();
        $answer->examResult()->update(['graded_by' => auth()->user()->teacher->id, 'graded_at' => now()]);

        activity()
            ->useLog('Jawaban Ujian')
            ->performedOn($exam)
            ->causedBy($request->user())
            ->log('Pengguna ' . $request->user()->name . ' menilai jawaban ujian untuk ujian: ' . $exam->id . ', jawaban ID: ' . $answer->id . ', skor: ' . $answer->score);

        return $this->sendResponse('Skor berhasil diubah.');
    }
}
