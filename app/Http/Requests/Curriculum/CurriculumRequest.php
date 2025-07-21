<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    $id = $this->route('kurikulum') ?? $this->route('id');
    return [
      'name' => 'required|string|max:255|unique:curriculums,name' . ($id ? ',' . $id : ''),
      'description' => 'nullable|string'
    ];
  }

  public function messages()
  {
    return [
      'name.required' => 'Nama kurikulum harus diisi.',
      'name.string' => 'Nama kurikulum harus berupa string.',
      'name.max' => 'Nama kurikulum maksimal 255 karakter.',
      'name.unique' => 'Nama kurikulum sudah ada.',
      'description.string' => 'Deskripsi kurikulum harus berupa string.',
    ];
  }
}
