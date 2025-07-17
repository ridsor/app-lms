<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AttendanceStatusRequest extends FormRequest
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
            'attendances' => 'required|array|min:1',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:H,I,S,A',
        ];
    }

    public function messages(): array
    {
        return [
            'attendances.required' => 'Data kehadiran wajib diisi.',
            'attendances.array' => 'Format data kehadiran tidak valid.',
            'attendances.min' => 'Minimal satu data kehadiran harus diisi.',
            'attendances.*.user_id.required' => 'User ID wajib diisi.',
            'attendances.*.user_id.exists' => 'User ID tidak ditemukan.',
            'attendances.*.status.required' => 'Status kehadiran wajib diisi.',
            'attendances.*.status.in' => 'Status kehadiran harus berupa Hadir (H), Izin (I), Sakit (S), atau Absen (A).',
            'attendances' => 'File harus berupa gambar',
        ];
    }

    public function attributes()
    {
        return [
            'attendances' => "kehadiran",
            'attendances.*.user_id' => "User ID",
            'attendances.*.status' => "status",
        ];
    }
}
