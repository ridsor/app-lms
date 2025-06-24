<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
        $uniqueNis = 'unique:students,nis';
        $uniqueNisn = 'unique:students,nisn';

        if ($studentId) {
            $uniqueNis .= ',' . $studentId;
            $uniqueNisn .= ',' . $studentId;
        }

        return [
            'name' => 'required|string|max:100',
            'nis' => 'required|string|max:20|' . $uniqueNis,
            'nisn' => 'required|string|max:20|' . $uniqueNisn,
            'class_id' => 'nullable|exists:classes,id',
            'homeroom_teacher_id' => 'nullable|exists:homeroom_teachers,id',
            'date_of_birth' => 'required|date',
            'birthplace' => 'required|string|max:50',
            'gender' => 'required|in:M,F',
            'religion' => 'required|string|max:20',
            'admission_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
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
            'nis.required' => 'NIS wajib diisi',
            'nis.string' => 'NIS harus berupa teks',
            'nis.max' => 'NIS maksimal 20 karakter',
            'nis.unique' => 'NIS sudah terdaftar',
            'nisn.required' => 'NISN wajib diisi',
            'nisn.string' => 'NISN harus berupa teks',
            'nisn.max' => 'NISN maksimal 20 karakter',
            'nisn.unique' => 'NISN sudah terdaftar',
            'class_id.exists' => 'Kelas tidak ditemukan',
            'homeroom_teacher_id.exists' => 'Wali kelas tidak ditemukan',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid',
            'birthplace.required' => 'Tempat lahir wajib diisi',
            'birthplace.string' => 'Tempat lahir harus berupa teks',
            'birthplace.max' => 'Tempat lahir maksimal 50 karakter',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'gender.in' => 'Jenis kelamin harus L atau P',
            'religion.required' => 'Agama wajib diisi',
            'religion.string' => 'Agama harus berupa teks',
            'religion.max' => 'Agama maksimal 20 karakter',
            'admission_year.required' => 'Tahun masuk wajib diisi',
            'admission_year.integer' => 'Tahun masuk harus berupa angka',
            'admission_year.min' => 'Tahun masuk minimal 2000',
            'admission_year.max' => 'Tahun masuk maksimal ' . (date('Y') + 1),
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
            'nis' => 'NIS',
            'nisn' => 'NISN',
            'class_id' => 'kelas',
            'homeroom_teacher_id' => 'wali kelas',
            'date_of_birth' => 'tanggal lahir',
            'birthplace' => 'tempat lahir',
            'gender' => 'jenis kelamin',
            'religion' => 'agama',
            'admission_year' => 'tahun masuk',
            'status' => 'status'
        ];
    }
}
