<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskSubmissionEvaluationRequest extends FormRequest
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
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string'
        ];
    }

    public function attributes()
    {
        return [
            'score' => "Nilai",
            'feedback' => "Feedback",
        ];
    }
}
