<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ExamRequest extends FormRequest
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
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'schedule_id' => 'required|exists:schedules,id',
            'type' => 'required|in:Midterm,Final',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'nullable|numeric',
            'exam_mode' => 'nullable|in:"Closed Book","Open Book"',
            'is_shuffle_questions' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => ':attribute wajib diisi.',
            'title.string' => ':attribute harus berupa teks.',

            'description.string' => ':attribute harus berupa teks.',

            'schedule_id.required' => ':attribute wajib diisi.',
            'schedule_id.exists' => ':attribute tidak ditemukan.',

            'type.required' => ':attribute wajib dipilih.',
            'type.in' => ':attribute harus salah satu dari: Midterm atau Final.',

            'start_time.required' => ':attribute wajib diisi.',
            'start_time.date' => ':attribute harus berupa tanggal.',

            'end_time.required' => ':attribute wajib diisi.',
            'end_time.date' => ':attribute harus berupa tanggal.',
            'end_time.after' => ':attribute harus setelah :date.',

            'duration.numeric' => ':attribute harus berupa angka.',

            'exam_mode.in' => ':attribute harus salah satu dari: Closed Book atau Open Book.',

            'is_shuffle_questions.boolean' => ':attribute harus bernilai true atau false.',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Judul Ujian',
            'description' => 'Deskripsi',
            'schedule_id' => 'Jadwal',
            'type' => 'Tipe Ujian',
            'start_time' => 'Waktu Mulai',
            'end_time' => 'Waktu Selesai',
            'duration' => 'Durasi',
            'exam_mode' => 'Mode Ujian',
            'is_shuffle_questions' => 'Acak Soal',
        ];
    }
}
