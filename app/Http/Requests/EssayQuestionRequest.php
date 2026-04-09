<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class EssayQuestionRequest extends FormRequest
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
            'question_text'     => ['required', 'string'],
            'question_points'   => ['required', 'integer'],
            'question_file'     => ['nullable', 'file', 'mimes:png,jpg,jpeg,mp3,wav,mp4,webm', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question_text'     => 'Teks Pertanyaan',
            'question_points'   => 'Poin Pertanyaan',
            'question_file'     => 'File Pertanyaan'
        ];
    }
}
