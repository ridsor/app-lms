<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TaskRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:individual,group',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'late_submission_time' => 'nullable|date|after:end_time',
            'allow_late_submission' => 'nullable|boolean',
            'deletedFile' => 'nullable|boolean',
            'file_path' => 'nullable|file|mimes:zip,rar,pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx|max:5120'
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas harus diisi.',
            'title.string' => 'Judul tugas harus berupa teks.',
            'description.string' => 'Deskripsi tugas harus berupa teks.',
            'type.required' => 'Jenis tugas harus dipilih.',
            'type.in' => 'Jenis tugas harus berupa individu atau kelompok.',
            'start_time.required' => 'Waktu mulai tugas harus diisi.',
            'start_time.date' => 'Waktu mulai tugas harus berupa tanggal yang valid.',
            'end_time.required' => 'Waktu selesai tugas harus diisi.',
            'end_time.date' => 'Waktu selesai tugas harus berupa tanggal yang valid.',
            'end_time.after' => 'Waktu selesai tugas harus setelah waktu mulai tugas.',
            'late_submission_time.date' => 'Waktu pengumpulan terlambat harus berupa tanggal yang valid.',
            'late_submission_time.after' => 'Waktu pengumpulan terlambat harus setelah waktu selesai tugas.',
            'allow_late_submission.boolean' => 'Nilai pengumpulan terlambat harus berupa boolean.',
            'file_path.required' => 'File tugas wajib diunggah.',
            'file_path.file' => 'File tugas harus berupa file.',
            'file_path.mimes' => 'File tugas harus memiliki format yang valid yaitu zip, rar, pdf, jpg, jpeg, png, doc, docx, xls, xlsx, ppt, atau pptx.',
            'file_path.max' => 'Ukuran file tugas tidak boleh lebih dari 5MB.',
        ];
    }
}
