<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function storeForExam(QuestionRequest $request, $id)
    {
        try {
            if (!$request->user()->can(['question.create'])) return abort(403);

            $validated = $request->validated();

            // Upload file
            if ($request->hasFile('question_file')) {
                $validated['question_file'] = $request->file('question_file')->store('file/ujian');
            }
            if ($request->hasFile('option_a_image')) {
                $validated['option_a_image'] = $request->file('option_a_image')->store('file/ujian');
            }
            if ($request->hasFile('option_b_image')) {
                $validated['option_b_image'] = $request->file('option_b_image')->store('file/ujian');
            }
            if ($request->hasFile('option_c_image')) {
                $validated['option_c_image'] = $request->file('option_c_image')->store('file/ujian');
            }
            if ($request->hasFile('option_d_image')) {
                $validated['option_d_image'] = $request->file('option_d_image')->store('file/ujian');
            }
            if ($request->hasFile('option_e_image')) {
                $validated['option_e_image'] = $request->file('option_e_image')->store('file/ujian');
            }

            // Set polymorphic relation
            $validated['questionable_id'] = $id;
            $validated['questionable_type'] = Exam::class;

            $question = Question::create($validated);

            return $this->sendResponse('Soal berhasil disimpan', $question, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function storeForQuestionBank(QuestionRequest $request, $id)
    {
        try {
            if (!$request->user()->can(['question.create'])) return abort(403);

            $validated = $request->validated();

            // Upload file
            if ($request->hasFile('question_file')) {
                $validated['question_file'] = $request->file('question_file')->store('file/ujian');
            }
            if ($request->hasFile('option_a_image')) {
                $validated['option_a_image'] = $request->file('option_a_image')->store('file/ujian');
            }
            if ($request->hasFile('option_b_image')) {
                $validated['option_b_image'] = $request->file('option_b_image')->store('file/ujian');
            }
            if ($request->hasFile('option_c_image')) {
                $validated['option_c_image'] = $request->file('option_c_image')->store('file/ujian');
            }
            if ($request->hasFile('option_d_image')) {
                $validated['option_d_image'] = $request->file('option_d_image')->store('file/ujian');
            }
            if ($request->hasFile('option_e_image')) {
                $validated['option_e_image'] = $request->file('option_e_image')->store('file/ujian');
            }

            // Set polymorphic relation
            $validated['questionable_id'] = $id;
            $validated['questionable_type'] = QuestionBank::class;

            $question = Question::create($validated);

            return $this->sendResponse('Soal berhasil disimpan', $question, 201);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if (!$request->user()->can(['question.edit'])) return abort(403);

            $question = Question::find($id);

            if (!$question) {
                return $this->sendError(
                    'Soal tidak ditemukan.',
                    [],
                    404
                );
            }

            return $this->sendResponse('Soal ditemukan.', $question);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function update(QuestionRequest $request, $id)
    {
        try {
            $question = Question::findOrFail($id);
            if (!$request->user()->can(['question.edit'])) return abort(403);

            $validated = $request->validated();

            if ($request->hasFile('question_file')) {
                if (!empty($question->question_file) && Storage::exists($question->question_file)) {
                    Storage::delete($question->question_file);
                }
                $validated['question_file'] = $request->file('question_file')->store('file/ujian');
            }
            if ($request->hasFile('option_a_image')) {
                if (!empty($question->option_a_image) && Storage::exists($question->option_a_image)) {
                    Storage::delete($question->option_a_image);
                }
                $validated['option_a_image'] = $request->file('option_a_image')->store('file/ujian');
            }
            if ($request->hasFile('option_b_image')) {
                if (!empty($question->option_b_image) && Storage::exists($question->option_b_image)) {
                    Storage::delete($question->option_b_image);
                }
                $validated['option_b_image'] = $request->file('option_b_image')->store('file/ujian');
            }
            if ($request->hasFile('option_c_image')) {
                if (!empty($question->option_c_image) && Storage::exists($question->option_c_image)) {
                    Storage::delete($question->option_c_image);
                }
                $validated['option_c_image'] = $request->file('option_c_image')->store('file/ujian');
            }
            if ($request->hasFile('option_d_image')) {
                if (!empty($question->option_d_image) && Storage::exists($question->option_d_image)) {
                    Storage::delete($question->option_d_image);
                }
                $validated['option_d_image'] = $request->file('option_d_image')->store('file/ujian');
            }
            if ($request->hasFile('option_e_image')) {
                if (!empty($question->option_e_image) && Storage::exists($question->option_e_image)) {
                    Storage::delete($question->option_e_image);
                }
                $validated['option_e_image'] = $request->file('option_e_image')->store('file/ujian');
            }

            if (!$request->has('option_d')) {
                $validated['option_d'] = null;
                if (!empty($question->option_d_image) && Storage::exists($question->option_d_image)) {
                    Storage::delete($question->option_d_image);
                }
            }
            if (!$request->has('option_e')) {
                $validated['option_e'] = null;
                if (!empty($question->option_e_image) && Storage::exists($question->option_e_image)) {
                    Storage::delete($question->option_e_image);
                }
            }

            $question->update($validated);

            return $this->sendResponse('Soal berhasil diperbarui.', $question);
        } catch (\Exception $e) {
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (!$request->user()->can(['question.delete'])) return abort(403);

            $question = Question::find($id);

            if (!$question) {
                return $this->sendError(
                    'Soal tidak ditemukan.',
                    [],
                    404
                );
            }

            if (!empty($question->question_file) && Storage::exists($question->question_file)) {
                Storage::delete($question->question_file);
            }
            if (!empty($question->option_a_image) && Storage::exists($question->option_a_image)) {
                Storage::delete($question->option_a_image);
            }

            if (!empty($question->option_b_image) && Storage::exists($question->option_b_image)) {
                Storage::delete($question->option_b_image);
            }

            if (!empty($question->option_c_image) && Storage::exists($question->option_c_image)) {
                Storage::delete($question->option_c_image);
            }

            if (!empty($question->option_d_image) && Storage::exists($question->option_d_image)) {
                Storage::delete($question->option_d_image);
            }
            if (!empty($question->option_e_image) && Storage::exists($question->option_e_image)) {
                Storage::delete($question->option_e_image);
            }

            $question->delete();

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

    public function getFile(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);
        } else if ($question->questionable_type == Exam::class) {
            $this->authorize('view', $question->questionable);
        }

        if (Storage::exists($question->question_file)) {
            return response()->file(Storage::path($question->question_file));
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function getFileOption(Request $request, $id, $option)
    {
        $question = Question::findOrFail($id);

        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) return abort(403);
        } else if ($question->questionable_type == Exam::class) {
            $this->authorize('view', $question->questionable);
        }

        $file = null;
        if ($option == "a") {
            $file = $question->option_a_image;
        } else if ($option == "b") {
            $file = $question->option_b_image;
        } else if ($option == "c") {
            $file = $question->option_c_image;
        } else if ($option == "d") {
            $file = $question->option_d_image;
        } else if ($option == "e") {
            $file = $question->option_e_image;
        }

        if ($file && Storage::exists($file)) {
            return response()->file(Storage::path($file));
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function downloadFile(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['question.create', 'question.view', 'question.edit', 'question.delete'])) return abort(403);
        } else if ($question->questionable_type == Exam::class) {
            $this->authorize('view', $question->questionable);
        }

        if (!empty($question->question_file) && Storage::exists($question->question_file)) {
            return Storage::download($question->question_file);
        }

        return abort(404, 'File tidak ditemukan.');
    }
}
