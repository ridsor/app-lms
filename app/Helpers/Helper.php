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
            'vice-principal' => 'Wakil Kepala Sekolah Bidang Kurikulum',
            'admin' => 'Admin',
            'teacher' => 'Guru',
            'student' => 'Siswa',
            'parent' => 'Orang Tua',
            default => 'Siswa'
        };
    }

    public static function getStudentStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Aktif',
            'transferred' => 'Pindah',
            'graduated' => 'Lulus',
            'dropout' => 'Keluar',
            default => $status
        };
    }

    /**
     * Mendapatkan route login berdasarkan role
     *
     * @param string $role
     * @return string
     */
    public static function getRouteByRole(string $role): string
    {
        return match ($role) {
            'teacher' => 'teacher',
            'student' => 'student',
            'parent' => 'parent',
            'admin' => 'admin',
            'vice-principal' => 'vice-principal',
            default => 'student'
        };
    }
}
