<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Period;
use App\Models\UKK;
use App\Models\UKKResultTheory;
use App\Models\MultipleQuestion;
use App\Models\EssayQuestion;
use App\Services\UKKScoringService;
use App\Http\Requests\UKKRequest;
use App\Http\Requests\WorkmanshipRequest;
use App\Http\Requests\ExamAnswerRequest;
use App\Models\UKKAnswerTheory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Yajra\DataTables\Facades\DataTables;
use Rap2hpoutre\FastExcel\FastExcel;

class UKKController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', UKK::class);

        $ukks = UKK::with([
            'period',
            'results'
        ])
            ->filter($request->all())
            ->filterByPermission($request->user())
            ->paginate(10);

        $majors = Major::with(['classes' => function ($query) {
            $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
        }])->select('id', 'name')->orderBy('name', 'asc')->get();
        $hasMajors = Major::count() > 0;
        $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();
        $ukkTypes = [
            ['value' => 'Praktik', 'label' => 'Praktik'],
            ['value' => 'Teori', 'label' => 'Teori'],
        ];
        $activePeriod = Period::where('status', true)->first();

        $studentId = null;
        if ($request->user()->hasRole('student')) {
            $studentId = $request->user()->student->id;
        } elseif ($request->user()->hasRole('parent')) {
            $studentId = $request->user()->parent->id;
        }

        $operators = User::role('operator')
            ->whereHas('permissions', function ($q) {
                $q->where('name', 'ukk.evaluation');
            })
            ->select('id', 'name')
            ->get();

        return view('user.ukk.index', compact('ukks', 'majors', 'hasMajors', 'periods', 'ukkTypes', 'activePeriod', 'studentId', 'operators'));
    }

    public function store(UKKRequest $request)
    {
        try {
            $this->authorize('create', UKK::class);

            $validated = $request->validated();

            $activePeriod = Period::where('status', true)->first();
            if (!$activePeriod) {
                return $this->sendError('Tidak ada periode aktif. Silakan aktifkan periode terlebih dahulu.', [], 400);
            }
            $validated['period_id'] = $activePeriod->id;

            if ($request->hasFile('file_path')) {
                $filePath = $request->file('file_path')->store('file/ukk');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            $ukk = UKK::create($validated);

            return $this->sendResponse('Uji Kompetensi Keahlian berhasil ditambahkan', $ukk, 201);
        } catch (\Exception $e) {
            Log::error('Error creating UKK: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function edit($id)
    {
        try {
            $ukk = UKK::findOrFail($id);
            $this->authorize('update', $ukk);

            return $this->sendResponse('Data UKK berhasil diambil', $ukk);
        } catch (\Exception $e) {
            return $this->sendError('Data tidak ditemukan.', [], 404);
        }
    }

    public function show(Request $request, $id)
    {
        $ukk = UKK::with([
            'period',
            'results',
            'operator'
        ])->findOrFail($id);
        $this->authorize('view', $ukk);

        $studentId = null;
        if ($request->user()->hasRole('student')) {
            $studentId = $request->user()->student->id;
        } elseif ($request->user()->hasRole('parent')) {
            $studentId = $request->user()->parent->id;
        }

        $operators = User::role('operator')
            ->whereHas('permissions', function ($q) {
                $q->where('name', 'ukk.evaluation');
            })
            ->select('id', 'name')
            ->get();

        $majors = Major::with(['classes' => function ($query) {
            $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
        }])->select('id', 'name')->orderBy('name', 'asc')->get();
        $ukkTypes = [
            ['value' => 'Praktik', 'label' => 'Praktik'],
            ['value' => 'Teori', 'label' => 'Teori'],
        ];

        return view('user.ukk.show', compact('ukk', 'majors', 'ukkTypes', 'studentId', 'operators'));
    }

    public function showQuestion(Request $request, $id)
    {
        if (!$request->user()->hasRole('operator')) {
            return abort(403);
        }

        $ukk = UKK::with('multipleQuestions', 'essayQuestions')->findOrFail($id);

        if ($ukk->type !== 'Teori') {
            return abort(403, 'Hanya UKK tipe Teori yang memiliki soal.');
        }

        $this->authorize('view', $ukk);

        $multipleQuery = $ukk->multipleQuestions();
        $essayQuery = $ukk->essayQuestions();

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

        return view('user.ukk.question.show', [
            'ukk' => $ukk,
            'questions' => $paginatedQuestions,
            'majors' => $majors,
        ]);
    }

    public function showResultTeori(Request $request, $id)
    {
        $ukk = UKK::selectRaw('ukk.*, (
                SELECT COUNT(*) FROM ukk_answer_theory
                JOIN ukk_result_theory ON ukk_answer_theory.ukk_result_id = ukk_result_theory.id
                WHERE ukk_result_theory.ukk_id = ukk.id AND ukk_answer_theory.score IS NULL
            ) as not_yet_rated')->with(['results', 'period'])->withCount(['results'])->findOrFail($id);

        $this->authorize('evaluate', $ukk);

        if ($request->ajax()) {
            $data = UKKResultTheory::where('ukk_id', $ukk->id)
                ->select([
                    'ukk_result_theory.*',
                    'students.name',
                    'students.nis',
                ])
                ->leftJoin('students', 'student_id', '=', 'students.id')
                ->with('user:id,name');

            if ($request->filled('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $data->where(function ($q) use ($search) {
                    $q->where('students.name', 'like', "%{$search}%")
                        ->orWhere('students.nis', 'like', "%{$search}%");
                });
            }

            return DataTables::of($data)
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

        return view('user.ukk.result.teori', compact('ukk'));
    }

    public function exportResultTeori($id)
    {
        try {
            $ukk = UKK::with([
                'results.student',
                'period',
            ])->findOrFail($id);

            $this->authorize('evaluate', $ukk);

            $headerRows = [
                ['HASIL UKK TEORI' => ''],
                ['Periode', 'Periode' => $ukk->period ? $ukk->period->academic_year . ' ' . Helper::getSemesterLabel($ukk->period->semester) : '-'],
                ['Nama UKK', 'Nama UKK' => $ukk->name],
                ['Waktu', 'Waktu' => $ukk->start_time && $ukk->end_time ? $ukk->start_time->translatedFormat('j F Y H:i') . ' - ' . $ukk->end_time->translatedFormat('j F Y H:i') : '-'],
                [],
                ['No' => 'No', 'Nama' => 'Nama', 'NIS' => 'NIS', 'Nilai' => 'Nilai'],
            ];

            $exportData = $ukk->results->map(function ($result, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $result->student ? $result->student->name : '-',
                    'NIS' => $result->student ? $result->student->nis : '-',
                    'Nilai' => $result->formatted_score ? $result->formatted_score : '-',
                ];
            })->toArray();

            $filename = 'Nilai UKK Teori ' . now()->format('Y-m-d') . '.xlsx';

            $exportData = array_merge($headerRows, $exportData);

            return (new FastExcel($exportData))->download($filename);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
    public function showResultPraktik(Request $request, $id)
    {
        $ukk = UKK::findOrFail($id);
        $this->authorize('evaluate', $ukk);

        return view('user.ukk.result.praktik', compact('ukk'));
    }

    public function update(UKKRequest $request, $id)
    {
        try {
            $ukk = UKK::findOrFail($id);
            $this->authorize('update', $ukk);

            $validated = $request->validated();
            $validated = array_filter($validated, function ($value) {
                return !is_null($value);
            });

            if ($validated['deletedFile'] ?? false) {
                if (!empty($ukk->file_path) && Storage::exists($ukk->file_path)) {
                    Storage::delete($ukk->file_path);
                }
                $validated['file_path'] = null;
                $validated['file_name'] = null;
                $validated['file_size'] = null;
            }

            if ($request->hasFile('file_path')) {
                if (!empty($ukk->file_path) && Storage::exists($ukk->file_path)) {
                    Storage::delete($ukk->file_path);
                }
                $filePath = $request->file('file_path')->store('file/ukk');
                $validated['file_path'] = $filePath;
                $file = $request->file('file_path');
                $file_name = $file->getClientOriginalName();
                $file_size = $file->getSize();
                $validated['file_name'] = $file_name;
                $validated['file_size'] = $file_size;
            }

            $ukk->update($validated);

            return $this->sendResponse('UKK berhasil diperbarui', $ukk);
        } catch (\Exception $e) {
            Log::error('Error updating UKK: ' . $e->getMessage());
            return $this->sendError('Gagal memperbarui UKK. Silakan coba lagi.', [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ukk = UKK::findOrFail($id);
            $this->authorize('delete', $ukk);

            if (!empty($ukk->file_path) && Storage::exists($ukk->file_path)) {
                Storage::delete($ukk->file_path);
            }

            $ukk->delete();

            return $this->sendResponse('UKK berhasil dihapus', null);
        } catch (\Exception $e) {
            return $this->sendError('Gagal menghapus UKK. Silakan coba lagi.', [], 500);
        }
    }

    public function getFile($id)
    {
        $ukk = UKK::findOrFail($id);
        $this->authorize('view', $ukk);

        if (!empty($ukk->file_path) && Storage::exists($ukk->file_path)) {
            $file = Storage::get($ukk->file_path);
            $type = Storage::mimeType($ukk->file_path);

            return response($file)->header('Content-Type', $type);
        }

        return abort(404);
    }

    public function downloadFile($id)
    {
        $ukk = UKK::findOrFail($id);
        $this->authorize('view', $ukk);

        if (!empty($ukk->file_path) && Storage::exists($ukk->file_path)) {
            return Storage::download($ukk->file_path, $ukk->file_name);
        }

        return abort(404);
    }

    public function theoryInfo(Request $request, $id)
    {
        if (!$request->user()->hasRole('student') && !$request->user()->hasRole('parent')) {
            return abort(403);
        }

        $ukk = UKK::with([
            'period',
            'multipleQuestions',
            'essayQuestions',
        ])->findOrFail($id);

        if ($ukk->type !== 'Teori') {
            return abort(404, 'UKK Teori tidak ditemukan.');
        }

        $this->authorize('view', $ukk);

        $ukk_result = $ukk->results();
        if ($request->user()->hasRole('student')) {
            $ukk_result->where('student_id', $request->user()->student->id);
        } elseif ($request->user()->hasRole('parent')) {
            $ukk_result->where('student_id', $request->user()->parent->id);
        }
        $ukk_result = $ukk_result->first();

        return view('user.ukk.info.theory', [
            'ukk' => $ukk,
            'ukk_result' => $ukk_result,
        ]);
    }

    public function theoryStart(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $ukk = UKK::findOrFail($id);
            $this->authorize('view', $ukk);

            if ($ukk->type !== 'Teori') {
                return $this->sendError('Hanya UKK Teori yang bisa dikerjakan secara online.', [], 400);
            }

            $student = auth()->user()->student;

            $existingResult = UKKResultTheory::where('ukk_id', $ukk->id)
                ->where('student_id', $student->id)
                ->first();

            $now = now();
            if ($existingResult) {
                if ($existingResult->status === 'completed') {
                    return $this->sendError('Anda sudah menyelesaikan UKK ini.', [], 403);
                }
                if ($existingResult->end_time && $now->gt($existingResult->end_time)) {
                    return $this->sendError('Waktu UKK telah berakhir.', [], 403);
                }

                return $this->sendResponse('UKK dilanjutkan!', $existingResult, 200);
            }

            if ($now->lt($ukk->start_time)) {
                return $this->sendError('UKK belum dimulai.', [], 403);
            }

            if ($now->gt($ukk->end_time)) {
                return $this->sendError('Waktu UKK sudah berakhir.', [], 403);
            }

            $result = UKKResultTheory::create([
                'ukk_id' => $ukk->id,
                'student_id' => $student->id,
                'start_time' => $now,
                'end_time' => $ukk->duration ? $now->copy()->addMinutes($ukk->duration) : null,
                'status' => 'in_progress',
            ]);

            session()->forget("ukk_order_{$ukk->id}");

            return $this->sendResponse('UKK dimulai!', $result, 200);
        } catch (\Exception $e) {
            Log::error('Error starting UKK: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function theoryWorkmanship(Request $request, $id)
    {
        if (!auth()->user()->hasRole('student')) {
            return abort(403);
        }

        $ukk = UKK::with([
            'multipleQuestions',
            'essayQuestions',
        ])
            ->findOrFail($id);
        $this->authorize('view', $ukk);

        if ($ukk->type !== 'Teori') {
            return abort(404, 'UKK Teori tidak ditemukan.');
        }

        $now = now();
        $ukkResult = UKKResultTheory::with('answers')->where('ukk_id', $ukk->id)
            ->where('student_id', auth()->user()->student->id)
            ->first();

        if (!$ukkResult) {
            return redirect()->route('user.ukk.teori.info', $ukk->id);
        }

        if ($ukkResult->status === 'completed') {
            return redirect()->route('user.ukk.teori.workmanship.result', $ukk->id)
                ->with('error', 'Anda sudah mengerjakan UKK ini.');
        }

        if ($ukkResult->end_time && $now->gt($ukkResult->end_time)) {
            $ukkResult->update(['status' => 'completed']);
            app(UKKScoringService::class)->saveScore($ukkResult, auth()->user()->student->id);
            return redirect()->route('user.ukk.teori.workmanship.result', $ukk->id)
                ->with('error', 'Waktu UKK telah berakhir.');
        }

        $order = session("ukk_order_{$ukk->id}");

        if (!$order) {
            if ($ukk->is_shuffle_questions) {
                $order = Helper::fisherYatesShuffle($ukk->questions->toArray());
            } else {
                $order = $ukk->questions->toArray();
            }
            session(["ukk_order_{$ukk->id}" => $order]);
        }

        $questions = $order;

        return view('user.ukk.workmanship', compact('ukk', 'ukkResult', 'questions'));
    }

    public function getRandomQuestions(Request $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $ukk = UKK::findOrFail($id);
            $this->authorize('view', $ukk);

            $order = session("ukk_order_{$ukk->id}");

            if (!$order) {
                if ($ukk->is_shuffle_questions) {
                    $order = Helper::fisherYatesShuffle($ukk->questions->toArray());
                } else {
                    $order = $ukk->questions->toArray();
                }
                session(["ukk_order_{$ukk->id}" => $order]);
            }

            $index = $request->query('q', 1);
            $question = $order[$index - 1] ?? null;

            if (!$question) {
                return $this->sendError('Soal tidak ditemukan.', [], 404);
            }

            $student = auth()->user()->student;
            $ukkResult = UKKResultTheory::where('student_id', $student->id)->where('ukk_id', $id)->firstOrFail();

            $answer = UKKAnswerTheory::where('ukk_result_id', $ukkResult->id)
                ->where('questionable_id', $question['id'])
                ->where('questionable_type', $question['question_type'] === 'multiple' ? MultipleQuestion::class : EssayQuestion::class)
                ->first();

            $response = [
                'ukk' => $ukk,
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

    public function setAnswerByUKKResult(ExamAnswerRequest $request, $id)
    {
        try {
            if (!auth()->user()->hasRole('student')) {
                return abort(403);
            }

            $validated = $request->validated();

            $student = auth()->user()->student;

            $ukk_result = UKKResultTheory::where('student_id', $student->id)->where('ukk_id', $id)->firstOrFail();
            $ukk_result->answers()->updateOrCreate(
                [
                    'questionable_id' => $validated['question_id'],
                    'questionable_type' => $validated['question_type'] === 'multiple' ? MultipleQuestion::class : EssayQuestion::class,
                ],
                [
                    'answer' => $validated['answer'],
                    'answered_at' => now()
                ]
            );

            return $this->sendResponse('Jawaban berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error menyimpan jawaban UKK: ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function theorySubmit(WorkmanshipRequest $request, $id)
    {
        try {
            if (!$request->user()->hasRole('student')) {
                return abort(403);
            }

            $ukk = UKK::findOrFail($id);
            $this->authorize('view', $ukk);

            $student = auth()->user()->student;

            $ukkResult = UKKResultTheory::where('ukk_id', $ukk->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$ukkResult) {
                return $this->sendError('Anda belum memulai UKK ini.', [], 403);
            }

            $validated = $request->validated();

            if (isset($validated['answered']) && is_array($validated['answered'])) {
                foreach ($validated['answered'] as $answer) {
                    $ukkResult->answers()->updateOrCreate(
                        [
                            'questionable_id' => $answer['question_id'],
                            'questionable_type' => $answer['question_type'] === 'multiple' ? MultipleQuestion::class : EssayQuestion::class,
                        ],
                        [
                            'answer' => $answer['answer'],
                            'answered_at' => now()
                        ]
                    );
                }
            }

            $ukkResult->update([
                'status' => 'completed',
                'end_time' => now()
            ]);

            app(UKKScoringService::class)->saveScore($ukkResult, $student->id);

            session()->forget("ukk_order_{$ukk->id}");

            return $this->sendResponse('UKK selesai. Terima kasih!', $ukkResult);
        } catch (\Exception $e) {
            Log::error('Error submit UKK: ' . $e->getMessage());
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function theoryWorkmanshipResult(Request $request, $id)
    {
        if (!$request->user()->hasRole('student') && !$request->user()->hasRole('parent')) {
            return abort(403);
        }

        $ukk = UKK::findOrFail($id);
        $this->authorize('view', $ukk);

        $student = null;
        if ($request->user()->hasRole('student')) {
            $student = auth()->user()->student;
        } elseif ($request->user()->hasRole('parent')) {
            $student = auth()->user()->parent;
        }

        $ukkResult = UKKResultTheory::with('answers')->where('ukk_id', $ukk->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$ukkResult) {
            return redirect()->route('user.ukk.teori.info', $ukk->id)
                ->with('error', 'Anda belum menyelesaikan UKK ini.');
        }

        if (($ukkResult->end_time && now()->gt($ukkResult->end_time)) && $ukkResult->status !== 'completed') {
            app(UKKScoringService::class)->saveScore($ukkResult, $student->id);
        }

        if ($ukkResult->status !== 'completed') {
            return redirect()->route('user.ukk.teori.info', $ukk->id)
                ->with('error', 'Anda belum menyelesaikan UKK ini.');
        }

        $totalPoints = $ukk->multipleQuestions->sum('question_points') + $ukk->essayQuestions->sum('question_points');
        $totalCorrectAnswers = $ukkResult->answers->sum('score');

        // cek jika jawaban masih ada yang null, maka berikan pesan untuk menunggu penilaian guru
        $hasPendingScores = $ukkResult->answers()->whereNull('score')->exists();

        return view('user.ukk.workmanship_result', compact('ukkResult', 'student', 'ukk', 'totalPoints', 'totalCorrectAnswers', 'hasPendingScores'));
    }

    public function evaluation(Request $request, $ukk_id, $page = 1)
    {
        $ukk = UKK::findOrFail($ukk_id);

        $this->authorize('evaluate', $ukk);

        $query = UKKResultTheory::where('ukk_id', $ukk_id)
            ->with(['student', 'ukk', 'answers']);

        $ukk_results = $query->simplePaginate(1, ['*'], 'page', $page);
        $ukk_result = $query->simplePaginate(1, ['*'], 'page', $page)->first();

        if (!$ukk_result) {
            return redirect()->route('user.ukk.result.teori', $ukk_id)->with('error', 'Belum ada hasil yang bisa dinilai.');
        }

        $multipleQuery = $ukk->multipleQuestions();
        $essayQuery = $ukk->essayQuestions();

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

        $studentAnswers = $ukk_result->answers->keyBy(function ($answer) {
            return $answer->questionable_type . '_' . $answer->questionable_id;
        });

        $questions->transform(function ($question) use ($studentAnswers) {
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

        return view('user.ukk.evaluation', [
            'ukk_result' => $ukk_result,
            'ukk' => $ukk,
            'ukk_results' => $ukk_results,
            'questions' => $paginatedQuestions
        ]);
    }

    public function updateAnswerScore(Request $request, $id, $answer_id)
    {
        $request->validate([
            'score' => 'required|numeric|min:0'
        ]);

        $answer = UKKAnswerTheory::with('questionable')->find($answer_id);
        if (!$answer) {
            return $this->sendError('Jawaban tidak ditemukan.', [], 404);
        }

        $ukk = UKK::findOrFail($id);
        $this->authorize('evaluate', $ukk);

        $answer->score = $request->score;
        $answer->save();

        // Update total score di UKKResultTheory
        $ukkResult = $answer->ukkResult;
        $totalScore = $ukkResult->answers()->sum('score');
        $ukkResult->update(['score' => $totalScore]);

        return $this->sendResponse('Skor berhasil diubah.');
    }

    public function resetResult(Request $request, $id)
    {
        $ukk = UKK::findOrFail($id);
        $this->authorize('evaluate', $ukk);

        try {
            DB::beginTransaction();

            $resultIds = $ukk->results()->pluck('id');

            if ($resultIds->isNotEmpty()) {
                DB::table('ukk_answer_theory')->whereIn('ukk_result_id', $resultIds)->delete();
                $ukk->results()->delete();
            }

            activity()
                ->useLog('UKK')
                ->performedOn($ukk)
                ->causedBy($request->user())
                ->log('Pengguna ' . $request->user()->name . ' mereset semua hasil teori UKK untuk UKK ID: ' . $ukk->id);

            DB::commit();

            return $this->sendResponse('Semua hasil teori UKK berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset all UKK theory results: ' . $e->getMessage());

            return $this->sendError(
                'Terjadi kesalahan server. Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function resetResultById(Request $request, $id, $ukk_result_id)
    {
        $ukk = UKK::findOrFail($id);
        $this->authorize('evaluate', $ukk);

        try {
            DB::beginTransaction();

            $result = $ukk->results()->findOrFail($ukk_result_id);
            $result->answers()->delete();
            $result->delete();

            activity()
                ->useLog('UKK')
                ->performedOn($ukk)
                ->causedBy($request->user())
                ->log('Pengguna ' . $request->user()->name . ' mereset hasil teori UKK untuk UKK ID: ' . $ukk->id . ', hasil UKK ID: ' . $ukk_result_id);

            DB::commit();

            return $this->sendResponse('Hasil teori UKK berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset UKK theory result: ' . $e->getMessage());

            return $this->sendError(
                'Terjadi kesalahan server. Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
