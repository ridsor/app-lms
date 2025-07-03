<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'class_id' => 'required|exists:classes,id',
      'subject_id' => 'required|exists:subjects,id',
      'teacher_id' => 'required|exists:teachers,id',
      'room_id' => 'required|exists:rooms,id',
      'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
      'meeting_method' => 'nullable|in:Offline,Online,Hybrid',
    ];
  }

  public function messages(): array
  {
    return [
      'class_id.required' => 'Kelas wajib dipilih',
      'class_id.exists' => 'Kelas tidak ditemukan',
      'subject_id.required' => 'Mata pelajaran wajib dipilih',
      'subject_id.exists' => 'Mata pelajaran tidak ditemukan',
      'teacher_id.required' => 'Guru wajib dipilih',
      'teacher_id.exists' => 'Guru tidak ditemukan',
      'room_id.required' => 'Ruangan wajib dipilih',
      'room_id.exists' => 'Ruangan tidak ditemukan',
      'day.required' => 'Hari wajib dipilih',
      'day.in' => 'Hari tidak valid',
      'start_time.required' => 'Jam mulai wajib diisi',
      'start_time.date_format' => 'Format jam mulai tidak valid',
      'end_time.required' => 'Jam selesai wajib diisi',
      'end_time.date_format' => 'Format jam selesai tidak valid',
      'end_time.after' => 'Jam selesai harus setelah jam mulai',
      'meeting_method.in' => 'Metode pertemuan tidak valid',
    ];
  }

  public function attributes(): array
  {
    return [
      'class_id' => 'kelas',
      'subject_id' => 'mata pelajaran',
      'teacher_id' => 'guru',
      'room_id' => 'ruangan',
      'day' => 'hari',
      'start_time' => 'jam mulai',
      'end_time' => 'jam selesai',
      'meeting_method' => 'metode pertemuan',
    ];
  }
}
