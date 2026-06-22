<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ExamAnswerRequest extends FormRequest
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
            'answer' => 'required|string',
            'question_type' => 'required|in:multiple,essay',
            'question_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInMultiple = DB::table('multiple_questions')->where('id', $value)->exists();
                    $existsInEssay = DB::table('essay_questions')->where('id', $value)->exists();

                    if (!$existsInMultiple && !$existsInEssay) {
                        $fail('Soal tidak ditemukan di database.');
                    }
                },
            ],
        ];
    }
}
