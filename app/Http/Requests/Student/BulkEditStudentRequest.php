<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class BulkEditStudentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'ids' => 'required|array|min:1',
      'ids.*' => 'exists:students,id',
      'class_id' => 'nullable|exists:classes,id',
      'homeroom_teacher_id' => 'nullable|exists:teachers,id',
      'status' => 'nullable|in:active,transferred,graduated,dropout',
    ];
  }

  public function messages(): array
  {
    return [
      'ids.required' => 'Pilih minimal satu siswa.',
      'ids.*.exists' => 'Siswa tidak ditemukan.',
      'class_id.exists' => 'Kelas tidak ditemukan.',
      'homeroom_teacher_id.exists' => 'Wali kelas tidak ditemukan.',
      'status.in' => 'Status tidak valid.',
    ];
  }
}
