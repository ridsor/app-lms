<?php

namespace App\Rules;

use App\Models\Student;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class GroupMembers implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Memisahkan nama dan NISN dari format "Nama (NISN)"
        if (!preg_match('/^(.+)\s\(([^)]+)\)$/', $value, $matches)) {
            $fail('Format anggota kelompok harus berupa "Nama (NISN)"');
            return;
        }

        $name = trim($matches[1]);
        $nisn = trim($matches[2]);

        // Cek di database
        $exists = Student::where('name', $name)
            ->where('nisn', $nisn)
            ->exists();

        if (!$exists) {
            $fail('Siswa dengan nama dan NISN tersebut tidak ditemukan');
        }
    }
}
