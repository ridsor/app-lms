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

        $multipleQuestions = $multipleQuery->get()->map(function ($q) {
            $q->q_type = 'App\Models\MultipleQuestion';
            return $q;
        });

        $essayQuestions = $essayQuery->get()->map(function ($q) {
            $q->q_type = 'App\Models\EssayQuestion';
            return $q;
        });

        $questions = $multipleQuestions->concat($essayQuestions);

        $perPage = 10;
        $page = $request->input('page', 1);
        $questions = new LengthAwarePaginator(
            $questions->slice(($page - 1) * $perPage, $perPage)->values(),
            $questions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $subjects = Subject::select('id', 'name', 'curriculum_id')->with(['curriculum:id,name'])->get();

        return view('user.exam.question.show', compact('exam', 'questions', 'subjects'));
    }

    public function downloadTemplate()
    {
        $data = collect([
            [
                'Jenis Soal (Pilihan Ganda/Essay)' => 'Pilihan Ganda',
                'Poin' => 10,
                'Teks Soal' => 'Siapakah tokoh pada gambar di bawah ini?',
                'File Gambar Soal' => '',
                'Opsi A' => 'Ir. Soekarno',
                'File Gambar Opsi A' => 'soal1_opsi_a.png',
                'Opsi B' => 'Moh. Hatta',
                'File Gambar Opsi B' => 'soal1_opsi_b.png',
                'Opsi C' => 'Sutan Syahrir',
                'File Gambar Opsi C' => '',
                'Opsi D' => 'Ki Hajar Dewantara',
                'File Gambar Opsi D' => '',
                'Opsi E' => 'Jenderal Sudirman',
                'File Gambar Opsi E' => '',
                'Kunci Jawaban (A/B/C/D/E)' => 'A',
            ],
            [
                'Jenis Soal (Pilihan Ganda/Essay)' => 'Essay',
                'Poin' => 20,
                'Teks Soal' => 'Amati gambar tersebut dan jelaskan maknanya!',
                'File Gambar Soal' => 'soal2_soal.png',
                'Opsi A' => '',
                'File Gambar Opsi A' => '',
                'Opsi B' => '',
                'File Gambar Opsi B' => '',
                'Opsi C' => '',
                'File Gambar Opsi C' => '',
                'Opsi D' => '',
                'File Gambar Opsi D' => '',
                'Opsi E' => '',
                'File Gambar Opsi E' => '',
                'Kunci Jawaban (A/B/C/D/E)' => '',
            ],
        ]);

        return (new FastExcel($data))->download('Template_Soal_Ujian.xlsx');
    }

    public function importQuestions(Request $request, $id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->authorize('update', $exam);

            $request->validate([
                'import_file' => 'required|file',
            ]);

            $file = $request->file('import_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();

            $isZip = ($extension === 'zip' || in_array($mimeType, ['application/zip', 'application/x-zip-compressed', 'application/octet-stream']));

            DB::beginTransaction();

            if ($isZip) {
                $zip = new \ZipArchive;
                if ($zip->open($file->getRealPath()) === TRUE) {
                    $extractPath = storage_path('app/temp_import_' . uniqid());
                    $zip->extractTo($extractPath);
                    $zip->close();

                    Log::info("Import: ZIP extracted to {$extractPath}");

                    // Find Excel file recursively
                    $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractPath));
                    $excelPath = null;
                    foreach ($allFiles as $f) {
                        if (str_contains($f->getRealPath(), '__MACOSX')) continue;
                        if (in_array(strtolower(pathinfo($f->getFilename(), PATHINFO_EXTENSION)), ['xls', 'xlsx'])) {
                            $excelPath = $f->getRealPath();
                            break;
                        }
                    }

                    if (!$excelPath) {
                        \Illuminate\Support\Facades\File::deleteDirectory($extractPath);
                        throw new \Exception('File Excel tidak ditemukan di dalam ZIP.');
                    }

                    $collection = (new FastExcel)->import($excelPath);
                    
                    // Search for media folder
                    $mediaPath = null;
                    $directories = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($extractPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::SELF_FIRST
                    );
                    foreach ($directories as $dir) {
                        if (str_contains($dir->getRealPath(), '__MACOSX')) continue;
                        if ($dir->isDir() && strtolower($dir->getFilename()) === 'media') {
                            $mediaPath = $dir->getRealPath();
                            break;
                        }
                    }

                    foreach ($collection as $index => $row) {
                        $this->processImportRow($exam, $row, $mediaPath, $index + 1, $extractPath);
                    }

                    \Illuminate\Support\Facades\File::deleteDirectory($extractPath);
                } else {
                    throw new \Exception('Gagal membuka file ZIP.');
                }
            } else {
                $collection = (new FastExcel)->import($file);
                foreach ($collection as $index => $row) {
                    $this->processImportRow($exam, $row, null, $index + 1);
                }
            }

            DB::commit();

            return $this->sendResponse('Soal berhasil diimport.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            Log::error('Error importing questions: ' . $e->getMessage());
            return $this->sendError('Gagal mengimpor file. ' . $e->getMessage(), [], 500);
        }
    }

    private function processImportRow($exam, $row, $mediaPath = null, $rowNumber = 1, $basePath = null)
    {
        // 0. Robust Trim for Keys and Values
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanRow[trim($key)] = is_string($value) ? trim($value) : $value;
        }
        $row = $cleanRow;

        $jenis = strtolower($row['Jenis Soal (Pilihan Ganda/Essay)'] ?? '');
        $poin = floatval($row['Poin'] ?? 0);
        $teks = $row['Teks Soal'] ?? '';

        if (empty($teks)) return;

        $data = [
            'questionable_id' => $exam->id,
            'questionable_type' => Exam::class,
            'question_text' => $teks,
            'question_points' => $poin,
        ];

        // 1. Handle main question image
        $mainFileName = $row['File Gambar Soal'] ?? '';
        $imageFile = $this->findFileAnywhere($mainFileName, $mediaPath, $basePath);
        
        // Auto-discovery fallback for main question
        if (!$imageFile && $basePath) {
            $imageFile = $this->findFileByConvention($rowNumber, 'soal', null, $mediaPath, $basePath);
        }

        if ($imageFile) {
            $data['question_file'] = $this->saveImportedFile($imageFile);
            Log::info("Import: Row {$rowNumber} - Found main image: " . basename($imageFile));
        }

        if ($jenis === 'pilihan ganda' || $jenis === 'multiple') {
            $options = ['a', 'b', 'c', 'd', 'e'];
            foreach ($options as $opt) {
                $data["option_{$opt}"] = $row["Opsi " . strtoupper($opt)] ?? null;
                
                // 2. Handle option images
                $imgKey = "File Gambar Opsi " . strtoupper($opt);
                $optFileName = $row[$imgKey] ?? '';
                $optImageFile = $this->findFileAnywhere($optFileName, $mediaPath, $basePath);
                
                // Auto-discovery fallback for options
                if (!$optImageFile && $basePath) {
                    $optImageFile = $this->findFileByConvention($rowNumber, 'opsi', $opt, $mediaPath, $basePath);
                }

                if ($optImageFile) {
                    $data["option_{$opt}_image"] = $this->saveImportedFile($optImageFile);
                    Log::info("Import: Row {$rowNumber} - Found option {$opt} image: " . basename($optImageFile));
                }
            }
            $data['correct_answer'] = strtolower(trim($row['Kunci Jawaban (A/B/C/D/E)'] ?? ''));
            MultipleQuestion::create($data);
        } elseif ($jenis === 'essay' || $jenis === 'uraian') {
            EssayQuestion::create($data);
        }
    }

    private function findFileByConvention($rowNumber, $type, $option = null, $mediaPath = null, $basePath = null)
    {
        $extensions = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        
        // Patterns to try
        $patterns = [];
        if ($type === 'soal') {
            $patterns[] = "soal_{$rowNumber}_soal";
            $patterns[] = "soal{$rowNumber}_soal";
            $patterns[] = "soal_{$rowNumber}";
            $patterns[] = "soal{$rowNumber}";
            $patterns[] = "gambar_soal_{$rowNumber}";
            $patterns[] = "gambar_soal_{$rowNumber}_soal";
            $patterns[] = "gambar_{$rowNumber}";
            $patterns[] = "gambar{$rowNumber}";
            $patterns[] = "{$rowNumber}";
        } else {
            $patterns[] = "soal_{$rowNumber}_opsi_{$option}";
            $patterns[] = "soal{$rowNumber}_opsi_{$option}";
            $patterns[] = "gambar_soal_{$rowNumber}_opsi_{$option}";
            $patterns[] = "gambar_{$rowNumber}_opsi_{$option}";
            $patterns[] = "soal_{$rowNumber}_{$option}";
            $patterns[] = "soal{$rowNumber}_{$option}";
            $patterns[] = "{$rowNumber}_{$option}";
        }

        foreach ($patterns as $pattern) {
            foreach ($extensions as $ext) {
                $fileName = "{$pattern}.{$ext}";
                $file = $this->findFileAnywhere($fileName, $mediaPath, $basePath);
                if ($file) return $file;
            }
        }

        return null;
    }

    private function findFileAnywhere($fileName, $mediaPath, $basePath)
    {
        if (empty($fileName)) return null;

        // 1. Try in media path
        if ($mediaPath) {
            $file = $this->findFileInDir($mediaPath, $fileName);
            if ($file) return $file;
        }

        // 2. Try in base path (recursively, skipping __MACOSX)
        if ($basePath) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
            foreach ($iterator as $f) {
                if ($f->isDir()) continue;
                if (str_contains($f->getRealPath(), '__MACOSX')) continue;
                if (strtolower($f->getFilename()) === strtolower($fileName)) {
                    return $f->getRealPath();
                }
            }
        }

        return null;
    }

    private function findFileInDir($dir, $fileName)
    {
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($filePath)) return $filePath;

        $files = scandir($dir);
        foreach ($files as $file) {
            if (strtolower($file) === strtolower($fileName)) {
                return $dir . DIRECTORY_SEPARATOR . $file;
            }
        }
        return null;
    }

    private function saveImportedFile($fullPath)
    {
        $fileName = pathinfo($fullPath, PATHINFO_BASENAME);
        $newPath = 'file/ujian/' . uniqid() . '_' . $fileName;
        Storage::put($newPath, file_get_contents($fullPath));
        Log::info("Import: Saved file to {$newPath}");
        return $newPath;
    }

    public function copyQuestions(Request $request, $exam_id, $id)
    {
        try {
            $targetExam = Exam::findOrFail($exam_id);
            $sourceExam = Exam::findOrFail($id);

            $this->authorize('update', $targetExam);

            DB::beginTransaction();

            foreach ($sourceExam->multipleQuestions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->questionable_id = $targetExam->id;
                $newQuestion->save();
            }

            foreach ($sourceExam->essayQuestions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->questionable_id = $targetExam->id;
                $newQuestion->save();
            }

            DB::commit();

            return $this->sendResponse('Soal berhasil disalin.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error copying questions: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function showResult(Request $request, $id)
    {
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

        $this->authorize('view', $exam);

        if ($request->ajax()) {
            return DataTables::of($exam->results)
                ->addIndexColumn()
                ->addColumn('Nama', fn($row) => $row->student ? '<div class="product-names"><p>' . $row->student->name . '</p></div>' : '-')
                ->addColumn('NIS', fn($row) => $row->student ? '<p class="f-light">' . $row->student->nis . '</p>' : '-')
                ->addColumn('Nilai', fn($row) => '<span class="badge badge-light-primary">' . ($row->formatted_score) . '</span>')
                ->addColumn('Status', function ($row) {
                    return Helper::getExamStatusLabel($row->status);
                })
                ->addColumn('Pengerjaan', fn($row) => $row->status === 'completed' ? $row->updated_at->translatedFormat('d/m/Y H:i') : '-')
                ->addColumn('action', function ($row) use ($exam) {
                    $btn = '<div class="d-flex align-items-center gap-2">';
                    $btn .= '<button class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                              style="width: 32px; height: 32px;" onclick="handleResetResult(event, ' . $exam->id . ', ' . $row->id . ')">
                              <i data-feather="refresh-cw" style="width: 16px; height: 16px"></i>
                            </button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['Nama', 'NIS', 'Nilai', 'Status', 'Pengerjaan', 'action'])
                ->make(true);
        }

        return view('user.exam.result.show', compact('exam'));
    }

    public function resetResult(Request $request, $id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->authorize('update', $exam);

            DB::beginTransaction();

            $exam->results()->delete();

            activity()
                ->useLog('Ujian')
                ->performedOn($exam)
                ->causedBy($request->user())
                ->log('Pengguna ' . $request->user()->name . ' mereset hasil ujian untuk ujian ID: ' . $exam->id);

            DB::commit();

            return $this->sendResponse('Hasil ujian berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function resetResultById(Request $request, $id, $exam_result_id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->authorize('update', $exam);

            DB::beginTransaction();

            $exam_result = ExamResult::findOrFail($exam_result_id);
            $exam_result->answers()->delete();
            $exam_result->delete();

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
                ['Tipe Ujian', 'Tipe Ujian' => Helper::getExamTypeLabel($exam->type)],
                ['Waktu Ujian', 'Waktu Ujian' => $exam->start_time && $exam->end_time ? $exam->start_time->translatedFormat('j F Y H:i') . ' - ' . $exam->end_time->translatedFormat('j F Y H:i') : '-'],
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

        $questions = $multipleQuestions->concat($essayQuestions);

        $perPage = 10;
        $qPage = $request->input('q_page', 1);
        $questions = new LengthAwarePaginator(
            $questions->slice(($qPage - 1) * $perPage, $perPage)->values(),
            $questions->count(),
            $perPage,
            $qPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'q_page']
        );

        return view('user.exam.evaluation', compact('exam_result', 'exam_results', 'questions', 'page'));
    }

    public function updateAnswerScore(Request $request, $id, $answer_id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->authorize('update', $exam);

            $answer = ExamAnswer::findOrFail($answer_id);
            $answer->score = $request->score;
            $answer->save();

            $examResult = ExamResult::findOrFail($answer->exam_result_id);
            app(ExamScoringService::class)->saveScore($examResult, $examResult->student_id);

            return $this->sendResponse('Nilai berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Error updating answer score: ' . $e->getMessage());
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
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

        if ($now->gt($exam->end_time)) {
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
                if ($now->gt($exam->end_time)) {
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

        $examResult = ExamResult::with('answers')->where('exam_id', $exam->id)
            ->where('student_id', $student->id ?? 0)
            ->first();

        if (!$examResult) {
            return redirect()->route('user.exam.info', $exam->id)
                ->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        $totalPoints = $exam->multipleQuestions->sum('question_points') + $exam->essayQuestions->sum('question_points');
        $totalCorrectAnswers = $examResult->answers->sum('score');
        $hasPendingScores = $examResult->answers()->whereNull('score')->exists();

        return view('user.exam.workmanship_result', compact('exam', 'examResult', 'totalPoints', 'totalCorrectAnswers', 'hasPendingScores'));
    }
}
