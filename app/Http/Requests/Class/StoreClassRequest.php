<?php

namespace App\Http\Requests\Class;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
            'capacity' => 'required|integer|min:1|max:100'
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
            'major_id.required' => 'Jurusan wajib diisi',
            'major_id.exists' => 'Jurusan tidak ditemukan',
            'capacity.required' => 'Kapasitas wajib diisi',
            'capacity.integer' => 'Kapasitas harus berupa angka',
            'capacity.min' => 'Kapasitas minimal 1',
            'capacity.max' => 'Kapasitas maksimal 100'
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
            'capacity' => 'kapasitas'
        ];
    }
}
