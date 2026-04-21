<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UKKRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'operator_id' => 'required|exists:users,id',
            'type' => 'required|in:Praktik,Teori',
            'major' => 'required|exists:majors,name',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'nullable|integer',
            'instructions' => 'required|string',
            'is_shuffle_questions' => 'boolean',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png,txt|max:10240',
            'deletedFile' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.string' => 'Judul harus berupa teks',
            'title.max' => 'Judul maksimal 255 karakter',
            'operator_id.required' => 'Operator wajib diisi',
            'operator_id.exists' => 'Operator tidak ditemukan',
            'type.required' => 'Tipe wajib diisi',
            'type.in' => 'Tipe harus Praktik atau Teori',
            'major.required' => 'Jurusan wajib diisi',
            'major.exists' => 'Jurusan tidak ditemukan',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'start_time.date' => 'Waktu mulai harus berupa format tanggal dan waktu yang valid',
            'end_time.required' => 'Waktu selesai wajib diisi',
            'end_time.date' => 'Waktu selesai harus berupa format tanggal dan waktu yang valid',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'duration.integer' => 'Durasi harus berupa angka',
            'instructions.required' => 'Instruksi wajib diisi',
            'instructions.string' => 'Instruksi harus berupa teks',
            'file_path.file' => 'File harus berupa dokumen atau gambar',
            'file_path.mimes' => 'Format file tidak didukung',
            'file_path.max' => 'Ukuran file maksimal 10MB',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'operator_id' => 'operator',
            'type' => 'tipe',
            'major' => 'jurusan',
            'start_time' => 'waktu mulai',
            'end_time' => 'waktu selesai',
            'duration' => 'durasi',
            'instructions' => 'instruksi',
            'file_path' => 'file lampiran',
        ];
    }
}
