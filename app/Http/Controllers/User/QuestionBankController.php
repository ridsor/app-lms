<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionBankRequest;
use App\Models\Exam;
use App\Models\Major;
use App\Models\Period;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

        if ($request->ajax()) {
            $data = QuestionBank::join('subjects', 'subject_id', '=', 'subjects.id')
                ->select([
                    'question_banks.id',
                    'subject_id',
                    'title',
                    'question_banks.created_at',
                    'subjects.name as subject_name',
                ])
                ->with([
                    'subject:id,curriculum_id',
                    'subject.curriculum:id,name'
                ])
                ->withCount([
                    'questions'
                ]);

            // filter
            if ($request->filled('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $data->whereFullText('title', $search);
            }

            if ($request->filled('jurusan')) {
                $data->whereHas('subject.major', function ($q) use ($request) {
                    $q->where('name', $request->jurusan);
                });
            }

            if ($request->filled('mata_pelajaran')) {
                $data->where('subjects.name', $request->mata_pelajaran);
            }

            $data->get();

            return DataTables::of($data)
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
                ->addColumn('Judul', function ($row) {
                    $html = '
                        <div class="product-names">
                        <p>' . $row->title . '</p>
                        </div>
                        ';
                    return $html;
                })
                ->addColumn('Mata Pelajaran', function ($row) {
                    $html = '
                        <div class="product-names">
                        <p>' . $row->subject_name . ' - ' . $row->subject->curriculum->name . '</p>
                        </div> 
                        ';
                    return $html;
                })
                ->addColumn('Soal', function ($row) {
                    $html = '
                        <div>
                        <span class="badge badge-light-primary">' . $row->questions_count . '</span>
                        </div>
                        ';
                    return $html;
                })
                ->addColumn('Waktu', function ($row) {
                    return $row->created_at->translatedFormat('d/m/Y H:i');
                })
                ->addColumn('', function ($row) {
                    return '
                        <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                            <a class="square-white view" href="' . route('user.question-bank.show', $row->id) . '"><svg>
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
                        </div>';
                })
                ->rawColumns(['id', 'Waktu', 'Judul', 'Mata Pelajaran', 'Soal', ''])->make(true);
        } else {
            $hasMajors = Major::count() > 0;
            $majors = Major::with(['classes' => function ($query) {
                $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
            }])->select('id', 'name')->orderBy('name', 'asc')->get();
            $subjects = Subject::select('id', 'name', 'curriculum_id')->with(['curriculum:id,name'])->get();
            return view('user.question-bank.index', compact('majors', 'hasMajors', 'subjects'));
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

            $question_bank = QuestionBank::find($id);

            if (!$question_bank) {
                return $this->sendError(
                    'Data siswa tidak ditemukan.',
                    [],
                    404
                );
            }

            return $this->sendResponse('Bank Soal ditemukan', $question_bank);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

        $question_bank = QuestionBank::withCount([
            'questions'
        ])->where('id', $id)->firstOrFail();

        return view('user.question-bank.show', compact('question_bank'));
    }

    public function store(QuestionBankRequest $request)
    {
        try {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

            $validated = $request->validated();

            $question_bank = QuestionBank::create($validated);

            return $this->sendResponse(
                'Bank Soal berhasil ditambahkan',
                [],
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

    public function update(QuestionBankRequest $request, $id)
    {
        try {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

            $question_bank = QuestionBank::findOrFail($id);

            $validated = $request->validated();
            $question_bank->update($validated);

            return $this->sendResponse('Bank Soal berhasil diperbarui', $question_bank);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);

            $question_bank = QuestionBank::findOrFail($id);

            $question_bank->delete();

            return $this->sendResponse(
                'Bank Soal berhasil dihapus.',
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }
}
