<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'file_type' => 'required|in:eBook,Archive,Link',
        ];

        if ($this->file_type === 'Link') {
            $rules['material_link'] = 'required|url';
        } else if ($this->file_type === 'Archive') {
            $rules['file_path'] = 'required|file|mimes:zip,rar|max:5120';
        } else if ($this->file_type === 'eBook') {
            $rules['file_path'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'title.required' => 'Judul materi harus diisi.',
            'description.required' => 'Deskripsi materi harus diisi.',
            'file_type.required' => 'Jenis file harus dipilih.',
            'material_link.required' => 'Link materi harus diisi.',
            'file_path.required' => 'File materi wajib diisi.',
            'file_path.file' => 'File materi harus berupa file.',
            'file_path.max' => 'Ukuran file materi tidak boleh lebih dari 5MB.',
        ];
        if ($this->file_type === 'Archive') {
            $messages['file_path.mimes'] =  'File materi harus memiliki format yang valid yaitu zip atau rar.';
        } else if ($this->file_type === 'eBook') {
            $messages['file_path'] = 'File materi harus memiliki format yang valid yaitu pdf, doc, docx, xls, xlsx, ppt, atau pptx.';
        }

        return $messages;
    }
}
