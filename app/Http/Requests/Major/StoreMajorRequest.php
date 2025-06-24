<?php

namespace App\Http\Requests\Major;

use Illuminate\Foundation\Http\FormRequest;

class StoreMajorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('jurusan') ?? null;
        return [
            'name' => 'required|string|max:255|unique:majors,name' . ($id ? ',' . $id : ''),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama jurusan wajib diisi',
            'name.string' => 'Nama jurusan harus berupa teks',
            'name.max' => 'Nama jurusan maksimal 255 karakter',
            'name.unique' => 'Nama jurusan sudah ada',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama jurusan',
        ];
    }
}
