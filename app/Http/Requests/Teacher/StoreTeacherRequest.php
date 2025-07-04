<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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
        $teacherId = $this->route('guru') ?? null;
        $uniqueNip = 'unique:teachers,nip';
        if ($teacherId) {
            $uniqueNip .= ',' . $teacherId;
        }
        return [
            'name' => 'required|string|max:100',
            'nip' => 'required|string|max:100|' . $uniqueNip,
            'specialization' => 'nullable|string',
            'date_of_birth' => 'required|date_format:d/m/Y',
            'birthplace' => 'required|string|max:50',
            'gender' => 'required|in:M,F',
            'religion' => 'required|string|max:50',
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
            'name.required' => 'Nama guru wajib diisi',
            'name.string' => 'Nama guru harus berupa teks',
            'name.max' => 'Nama guru maksimal 100 karakter',
            'nip.required' => 'NIP wajib diisi',
            'nip.string' => 'NIP harus berupa teks',
            'nip.max' => 'NIP maksimal 100 karakter',
            'nip.unique' => 'NIP sudah terdaftar',
            'specialization.string' => 'Spesialisasi harus berupa teks',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.date_format' => 'Format tanggal lahir harus dd/mm/yyyy',
            'birthplace.required' => 'Tempat lahir wajib diisi',
            'birthplace.string' => 'Tempat lahir harus berupa teks',
            'birthplace.max' => 'Tempat lahir maksimal 50 karakter',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'religion.required' => 'Agama wajib diisi',
            'religion.string' => 'Agama harus berupa teks',
            'religion.max' => 'Agama maksimal 50 karakter',
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
            'name' => 'nama guru',
            'nip' => 'NIP',
            'specialization' => 'spesialisasi',
            'date_of_birth' => 'tanggal lahir',
            'birthplace' => 'tempat lahir',
            'gender' => 'jenis kelamin',
            'religion' => 'agama',
        ];
    }
}
