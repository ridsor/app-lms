<?php

namespace App\Http\Requests;

use App\Rules\GroupMembers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TaskSubmissionRequest extends FormRequest
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
            'files' => 'sometimes|array',
            'links' => 'sometimes|array',
            'group_members' => 'sometimes|array',
            'files.*.file' => 'required|file|mimes:zip,rar,pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'links.*.url' => 'required|url',
            'files.*.id' => 'required|string',
            'links.*.id' => 'required|string',
            'group_members.*' => ['nullable', 'string',  new GroupMembers],
            'deleteContent' => 'nullable|array'
        ];

        return $rules;
    }

    public function messagges()
    {
        return [
            'files.*.mimes' => 'File tugas harus memiliki format yang valid yaitu zip, rar, pdf, jpg, jpeg, png, doc, docx, xls, xlsx, ppt, atau pptx.',
            'files.*.file' => 'File tugas harus berupa file.',
        ];
    }

    public function attributes()
    {
        $attributeNames = [];

        if ($this->has('files')) {
            foreach ($this->file('files', []) as $i => $file) {
                $attributeNames["files.$i.file"] = "File " . ($i + 1);
            }
        }

        if ($this->has('group_members')) {
            foreach ($this->input('group_members', []) as $i => $value) {
                $attributeNames["group_members.$i"] = "Anggota " . ($i + 1);
            }
        }

        if ($this->has('links')) {
            foreach ($this->input('links', []) as $i => $link) {
                $attributeNames["links.$i.url"] = "Link " . ($i + 1);
            }
        }

        return $attributeNames;
    }
}
