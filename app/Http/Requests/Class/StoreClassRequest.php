<?php

namespace App\Http\Requests\Class;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
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
        $classId = $this->route('kela');

        return [
            'name' => [
                'required',
                Rule::unique('classes')->where(function ($query) {
                    return $query->where('level', $this->input('level'))
                        ->where('major_id', $this->input('major_id'));
                })->ignore($classId),
            ],
            'level' => 'required|string|max:50',
            'major_id' => 'nullable|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
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
            'name.required' => 'Nama kelas wajib diisi',
            'name.string' => 'Nama kelas harus berupa teks',
            'name.max' => 'Nama kelas maksimal 255 karakter',
            'name.unique' => 'Nama kelas sudah ada',
            'level.required' => 'Tingkat wajib diisi',
            'level.string' => 'Tingkat harus berupa teks',
            'level.max' => 'Tingkat maksimal 50 karakter',
            'major_id.exists' => 'Jurusan tidak ditemukan',
            'homeroom_teacher_id.exists' => 'Guru tidak ditemukan',
            'name.unique' => 'Kelas sudah ada'
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
            'name' => 'nama kelas',
            'level' => 'tingkat',
            'major_id' => 'jurusan',
            'homeroom_teacher_id' => 'guru wali kelas',
        ];
    }
}
