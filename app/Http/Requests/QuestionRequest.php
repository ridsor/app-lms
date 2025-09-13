<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
    public function rules(): array
    {
        Log::info($this);
        return [
            'question_text'     => ['required', 'string'],
            'question_type'     => ['nullable', 'in:multiple_choice,essay,true_false'],
            'option_a'          => ['required', 'string', 'max:255', 'different:option_b,option_c,option_d,option_e'],
            'option_b'          => ['required', 'string', 'max:255', 'different:option_a,option_c,option_d,option_e'],
            'option_c'          => ['required', 'string', 'max:255', 'different:option_b,option_a,option_d,option_e'],
            'option_d'          => ['nullable', 'string', 'max:255', 'different:option_b,option_c,option_a,option_e'],
            'option_e'          => ['nullable', 'string', 'max:255', 'different:option_b,option_c,option_d,option_a'],

            'option_a_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'option_b_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'option_c_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'option_d_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'option_e_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'correct_answer'    => ['required', 'string', 'in:a,b,c,d,e'],

            'question_points'   => ['required', 'integer'],

            'question_file'     => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,png,jpg,jpeg,mp3,wav,mp4,webm', 'max:5120'],

            'deleteData' => 'nullable|array'
        ];
    }

    public function attributes(): array
    {
        return [
            'question_text'     => 'Teks Pertanyaan',
            'question_type'     => 'Tipe Pertanyaan',
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
