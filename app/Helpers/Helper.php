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
            'admin' => 'Admin',
            default => $role
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
            default => $role
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
    public static function getAttendanceLabel($value): string
    {
        return match ($value) {
            'H' => '<span style="font-size: 11px" class="px-2 badge badge-light-success">Hadir</span>',
            'I' => '<span style="font-size: 11px" class="px-2 badge badge-light-primary">Izin</span>',
            'S' => '<span style="font-size: 11px" class="px-2 badge badge-light-warning">Sakit</span>',
            'A' => '<span style="font-size: 11px" class="px-2 badge badge-light-danger">Absen</span>',
            default => '<span style="font-size: 11px" class="px-2 badge badge-light-secondary">-</span>'
        };
    }

    public static function getGenderLabel(string $gender): string
    {
        return match ($gender) {
            'M' => 'Laki-laki',
            'F' => 'Perempuan',
            default => $gender
        };
    }

    public static function getMeetingMethodLabel($meeting_method)
    {
        return match ($meeting_method) {
            'Online' => 'Daring',
            'Offline' => 'Luring',
            'Hybrid' => 'Campuran',
            default => $meeting_method
        };
    }

    public static function getSemesterLabel($semester)
    {
        return match ($semester) {
            'odd' => 'Ganjil',
            'even' => "Genap",
            default => $semester
        };
    }
}
