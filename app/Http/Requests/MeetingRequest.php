<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class MeetingRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meeting_method' => 'required|in:Online,Offline,Hybrid',
            'type' => 'required|in:Learning,Midterm,Final',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul pertemuan harus diisi.',
            'title.string' => 'Judul pertemuan harus berupa string.',
            'title.max' => 'Judul pertemuan maksimal 255 karakter.',
            'description.string' => 'Deskripsi pertemuan harus berupa string.',
            'meeting_method.required' => 'Metode pertemuan harus dipilih.',
            'meeting_method.in' => 'Metode pertemuan tidak valid.',
            'type.required' => 'Tipe pertemuan harus dipilih.',
            'type.in' => 'Tipe pertemuan tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul pertemuan',
            'description' => 'Deskripsi pertemuan',
            'meeting_method' => 'Metode pertemuan',
            'type' => 'Tipe pertemuan',
        ];
    }
}
