<?php

namespace App\Helpers;

class Helper
{
    /**
     * Mendapatkan label role dalam bahasa Indonesia
     *
     * @param string $role
     * @return string
     */
    public static function getRoleLabel(string $role): string
    {
        return match ($role) {
            'wakasek' => 'Wakil Kepala Sekolah Bidang Kurikulum',
            'admin' => 'Admin',
            'teacher' => 'Guru',
            'student' => 'Siswa',
            'parent' => 'Orang Tua',
            'super_admin' => 'Super Admin',
            default => 'Siswa'
        };
    }
}
