<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkmanshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answered' => 'array',
            'answered.*.question_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInMultiple = DB::table('multiple_questions')->where('id', $value)->exists();
                    $existsInEssay = DB::table('essay_questions')->where('id', $value)->exists();

                    if (!$existsInMultiple && !$existsInEssay) {
                        $fail('Soal tidak ditemukan di database.');
                    }
                },
            ],
            'answered.*.answer' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'answered.array' => 'Format data jawaban tidak valid.',

            'answered.*.question_id.required' => 'ID pertanyaan harus diisi.',
            'answered.*.question_id.exists' => 'Pertanyaan dengan ID :input tidak ditemukan.',

            'answered.*.answer.required' => 'Jawaban untuk pertanyaan harus diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'answered' => 'kumpulan jawaban',
            'answered.*.question_id' => 'nomor pertanyaan',
            'answered.*.answer' => 'pilihan jawaban',
        ];
    }
}
