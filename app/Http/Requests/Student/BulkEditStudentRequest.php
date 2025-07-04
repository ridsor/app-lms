<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
      'class_id' => [
        'nullable',
        function ($attribute, $value, $fail) {
          if ($value === 'nothing') {
            return true;
          }
          if (!is_null($value) && !DB::table('classes')->where('id', $value)->exists()) {
            $fail('Kelas tidak ditemukan.');
          }
        },
      ],
      'homeroom_teacher_id' => [
        'nullable',
        function ($attribute, $value, $fail) {
          if ($value === 'nothing') {
            return true;
          }
          if (!is_null($value) && !DB::table('teachers')->where('id', $value)->exists()) {
            $fail('Wali kelas tidak ditemukan.');
          }
        },
      ],
      'status' => 'nullable|in:active,transferred,graduated,dropout',
    ];
  }

  public function messages(): array
  {
    return [
      'ids.required' => 'Pilih minimal satu siswa.',
      'ids.*.exists' => 'Siswa tidak ditemukan.',
      'class_id.exists' => 'Kelas tidak ditemukan.',
      'homeroom_teacher_id.exists' => 'Wali Kelas tidak ditemukan.',
      'status.in' => 'Status tidak valid.',
    ];
  }
}
