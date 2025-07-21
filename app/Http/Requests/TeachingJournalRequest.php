<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeachingJournalRequest extends FormRequest
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
            'subject_matter' => 'required|string',
            'sub_subject_matter' => 'required|string',
            'additional_note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'subject_matter.required' => 'Subjek pembicaraan harus diisi',
            'sub_subject_matter.required' => 'Sub subjek pembicaraan harus diisi',
            'additional_note.string' => 'Catatan tambahan harus berupa teks',
        ];
    }

    public function attributes(): array
    {
        return [
            'subject_matter' => 'Subjek pembicaraan',
            'sub_subject_matter' => 'Sub subjek pembicaraan',
            'additional_note' => 'Catatan tambahan',
        ];
    }
}
