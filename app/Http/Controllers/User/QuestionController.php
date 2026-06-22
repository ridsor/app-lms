<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Models\UKK;
use App\Models\MultipleQuestion;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function store(QuestionRequest $request, $id)
    {
        try {
            if (!$request->user()->can(['question.create'])) return abort(403);

            $validated = $request->validated();

            // Set default points if null or empty string (especially for UKK)
            if (!isset($validated['question_points']) || is_null($validated['question_points']) || $validated['question_points'] === '') {
                $validated['question_points'] = 0;
            }

            // Set polymorphic relation
            $validated['questionable_id'] = $id;
            if ($request->input('model') === 'exam') {
                $validated['questionable_type'] = Exam::class;
            } else if ($request->input('model') === 'question_bank') {
                $validated['questionable_type'] = QuestionBank::class;
            } else if ($request->input('model') === 'ukk') {
                $validated['questionable_type'] = UKK::class;
            }

            // Upload file
            if ($request->hasFile('question_file')) {
                $validated['question_file'] = $request->file('question_file')->store('file/ujian');
            }

            if ($request->input('question_type') === 'multiple_choice') {
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

                $question = MultipleQuestion::create($validated);
            } else if ($request->input('question_type') === 'essay') {
                $question = EssayQuestion::create($validated);
            }

            return $this->sendResponse('Soal berhasil disimpan', $question, 201);
        } catch (\Exception $e) {
            Log::error('Error creating question:', ['error' => $e->getMessage()]);
            return $this->sendError('Silakan coba lagi.', [], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            if (!$request->user()->can(['question.edit'])) return abort(403);

            if ($request->input('question_type') === 'multiple') {
                $question = MultipleQuestion::find($id);
                $question->question_type = 'multiple';
            } else if ($request->input('question_type') === 'essay') {
                $question = EssayQuestion::find($id);
                $question->question_type = 'essay';
            }

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
            if (!$request->user()->can(['question.edit'])) return abort(403);

            $question = null;
            $type = $request->input('question_type');
            if ($type === 'multiple_choice' || $type === 'multiple') {
                $question = MultipleQuestion::find($id);
            } else if ($type === 'essay') {
                $question = EssayQuestion::find($id);
            }

            if (!$question) {
                return $this->sendError('Soal tidak ditemukan.', [], 404);
            }

            $validated = $request->validated();

            // Set default points if null or empty string (especially for UKK)
            if (!isset($validated['question_points']) || is_null($validated['question_points']) || $validated['question_points'] === '') {
                $validated['question_points'] = 0;
            }

            // Prevent accidental nulling of existing files
            unset($validated['question_file']);
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                unset($validated["option_{$opt}_image"]);
            }

            if ($request->has('deleteData')) {
                $deleteData = $request->input('deleteData');
                if (in_array('question_file', $deleteData)) {
                    if (!empty($question->question_file) && Storage::exists($question->question_file)) {
                        Storage::delete($question->question_file);
                    }
                    $validated['question_file'] = null;
                }
                foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                    if (in_array("option_{$opt}_image", $deleteData)) {
                        $imageKey = "option_{$opt}_image";
                        if (!empty($question->$imageKey) && Storage::exists($question->$imageKey)) {
                            Storage::delete($question->$imageKey);
                        }
                        $validated[$imageKey] = null;
                    }
                }
            }

            if ($request->hasFile('question_file')) {
                if (!empty($question->question_file) && Storage::exists($question->question_file)) {
                    Storage::delete($question->question_file);
                }
                $validated['question_file'] = $request->file('question_file')->store('file/ujian');
            }

            if ($type === 'multiple_choice' || $type === 'multiple') {
                foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                    $fileKey = "option_{$opt}_image";
                    if ($request->hasFile($fileKey)) {
                        if (!empty($question->$fileKey) && Storage::exists($question->$fileKey)) {
                            Storage::delete($question->$fileKey);
                        }
                        $validated[$fileKey] = $request->file($fileKey)->store('file/ujian');
                    }
                }

                if (!$request->has('option_d')) {
                    $validated['option_d'] = null;
                    if (!empty($question->option_d_image) && Storage::exists($question->option_d_image)) {
                        Storage::delete($question->option_d_image);
                    }
                    $validated['option_d_image'] = null;
                }
                if (!$request->has('option_e')) {
                    $validated['option_e'] = null;
                    if (!empty($question->option_e_image) && Storage::exists($question->option_e_image)) {
                        Storage::delete($question->option_e_image);
                    }
                    $validated['option_e_image'] = null;
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

            $question = null;
            if ($request->input('question_type') === 'multiple') {
                $question = MultipleQuestion::find($id);
            } else if ($request->input('question_type') === 'essay') {
                $question = EssayQuestion::find($id);
            }

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
        // 1. Tangkap parameter 'type' (contoh URL: /file/1?type=essay)
        // Default disetel ke 'multiple' agar kode lama Anda yang tidak memakai parameter ini tidak error.
        $type = $request->input('type', 'multiple');

        // 2. Tentukan model mana yang dipanggil berdasarkan 'type'
        if ($type === 'essay') {
            $question = EssayQuestion::findOrFail($id);
        } else {
            $question = MultipleQuestion::findOrFail($id);
        }

        // 3. Logika otorisasi bawaan Anda (tetap sama)
        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) {
                return abort(403);
            }
        } else if ($question->questionable_type == Exam::class || $question->questionable_type == UKK::class) {
            $this->authorize('view', $question->questionable);
        }

        // 4. Logika pengambilan file (tetap sama)
        if ($question->question_file && Storage::exists($question->question_file)) {
            return response()->file(Storage::path($question->question_file));
        }

        return abort(404, 'File tidak ditemukan.');
    }


    public function getFileOption(Request $request, $id, $option)
    {
        // 1. Tangkap parameter type
        $type = $request->input('type', 'multiple');

        // 2. Cegah pencarian jika tipenya essay (karena essay tidak punya opsi A-E)
        if ($type === 'essay') {
            return abort(404, 'Soal essay tidak memiliki file opsi jawaban.');
        }

        // 3. Cari berdasarkan model MultipleQuestion
        $question = MultipleQuestion::findOrFail($id);

        // 4. Logika otorisasi (tetap sama)
        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['exam.create', 'exam.view', 'exam.edit', 'exam.delete'])) {
                return abort(403);
            }
        } else if ($question->questionable_type == Exam::class || $question->questionable_type == UKK::class) {
            $this->authorize('view', $question->questionable);
        }

        // 5. Validasi opsi agar hanya menerima a, b, c, d, atau e
        $validOptions = ['a', 'b', 'c', 'd', 'e'];
        if (!in_array($option, $validOptions)) {
            return abort(404, 'Opsi tidak valid.');
        }

        // 6. Ambil nama file secara dinamis (menggantikan if-else yang panjang)
        // Contoh: jika $option "a", maka akan memanggil $question->option_a_image
        $columnName = "option_{$option}_image";
        $file = $question->$columnName;

        // 7. Cek dan kembalikan file
        if ($file && Storage::exists($file)) {
            return response()->file(Storage::path($file));
        }

        return abort(404, 'File tidak ditemukan.');
    }

    public function downloadFile(Request $request, $id)
    {
        // 1. Tangkap parameter 'type' dari URL (default ke 'multiple')
        $type = $request->input('type', 'multiple');

        // 2. Tentukan model mana yang dicari berdasarkan 'type'
        if ($type === 'essay') {
            $question = EssayQuestion::findOrFail($id);
        } else {
            $question = MultipleQuestion::findOrFail($id);
        }

        // 3. Logika otorisasi (tetap sama sesuai kode asli Anda)
        if ($question->questionable_type == QuestionBank::class) {
            if (!$request->user()->can(['question.create', 'question.view', 'question.edit', 'question.delete'])) {
                return abort(403);
            }
        } else if ($question->questionable_type == Exam::class || $question->questionable_type == UKK::class) {
            $this->authorize('view', $question->questionable);
        }

        // 4. Logika download file (tetap sama)
        if (!empty($question->question_file) && Storage::exists($question->question_file)) {
            return Storage::download($question->question_file);
        }

        return abort(404, 'File tidak ditemukan.');
    }
}
