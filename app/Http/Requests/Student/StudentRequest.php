<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StudentRequest extends FormRequest
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
        $studentId = $this->route('siswa');
        $uniqueNisn = 'unique:students,nisn';

        if ($studentId) {
            $uniqueNisn .= ',' . $studentId;
        }

        return [
            'name' => 'required|string|max:100',
            'nisn' => 'required|string|max:100|' . $uniqueNisn,
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'class_id' => 'nullable|exists:classes,id',
            'date_of_birth' => 'required|date_format:d/m/Y',
            'birthplace' => 'required|string|max:50',
            'gender' => 'required|in:M,F',
            'religion' => 'required|string|max:50',
            'status' => 'required|in:active,transferred,graduated,dropout'
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
            'user_id.required' => 'User ID wajib diisi',
            'user_id.exists' => 'User tidak ditemukan',
            'user_id.unique' => 'User sudah terdaftar sebagai siswa',
            'name.required' => 'Nama siswa wajib diisi',
            'name.string' => 'Nama siswa harus berupa teks',
            'name.max' => 'Nama siswa maksimal 100 karakter',
            'nisn.required' => 'NISN wajib diisi',
            'nisn.string' => 'NISN harus berupa teks',
            'nisn.max' => 'NISN maksimal 100 karakter',
            'nisn.unique' => 'NISN sudah terdaftar',
            'homeroom_teacher_id.exists' => 'Wali kelas tidak ditemukan',
            'class_id.exists' => 'Kelas tidak ditemukan',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid',
            'birthplace.required' => 'Tempat lahir wajib diisi',
            'birthplace.string' => 'Tempat lahir harus berupa teks',
            'birthplace.max' => 'Tempat lahir maksimal 50 karakter',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'religion.required' => 'Agama wajib diisi',
            'religion.string' => 'Agama harus berupa teks',
            'religion.max' => 'Agama maksimal 50 karakter',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus aktif, pindah, lulus, atau keluar'
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
            'user_id' => 'user ID',
            'name' => 'nama siswa',
            'nisn' => 'NISN',
            'homeroom_teacher_id' => 'wali kelas',
            'class_id' => 'kelas',
            'date_of_birth' => 'tanggal lahir',
            'birthplace' => 'tempat lahir',
            'gender' => 'jenis kelamin',
            'religion' => 'agama',
            'status' => 'status'
        ];
    }
}
