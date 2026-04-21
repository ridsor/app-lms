<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\MultipleQuestionRequest;
use App\Http\Requests\EssayQuestionRequest;

class QuestionRequest extends FormRequest
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
    public function rules()
    {
        $type = $this->input('question_type');

        if ($type === 'multiple_choice') {
            return (new MultipleQuestionRequest())->rules();
        }

        if ($type === 'essay') {
            return (new EssayQuestionRequest())->rules();
        }

        return [
            'question_type' => 'required|in:multiple_choice,essay',
            'model' => 'required|in:exam,question_bank,ukk',
        ];
    }

    public function attributes()
    {
        return [
            'question_text'     => 'Teks Pertanyaan',
            'option_a'          => 'Opsi A',
            'option_b'          => 'Opsi B',
            'option_c'          => 'Opsi C',
            'option_d'          => 'Opsi D',
            'option_e'          => 'Opsi E',

            'option_a_image'    => 'Gambar Opsi A',
            'option_b_image'    => 'Gambar Opsi B',
            'option_c_image'    => 'Gambar Opsi C',
            'option_d_image'    => 'Gambar Opsi D',
            'option_e_image'    => 'Gambar Opsi E',

            'correct_answer'    => 'Jawaban',
            'question_points'   => 'Poin Pertanyaan',
            'question_file'     => 'File Pertanyaan'
        ];
    }
}
