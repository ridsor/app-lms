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
            'vice-principal' => 'Wakil Kepala Sekolah',
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

    public static function getDayName(string $day): string
    {
        return match ($day) {
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
            default => $day
        };
    }

    public static function getDayValue(string $day): string
    {
        return match ($day) {
            'Senin' => 'Monday',
            'Selasa' => 'Tuesday',
            'Rabu' => 'Wednesday',
            'Kamis' => 'Thursday',
            'Jumat' => 'Friday',
            'Sabtu' => 'Saturday',
            'Minggu' => 'Sunday',
            default => $day
        };
    }
}
