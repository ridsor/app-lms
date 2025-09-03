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
            'operator' => 'Operator',
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
            'operator' => 'operator',
            'admin' => 'admin',
            default => $role
        };
    }

    public static function getDayName($day)
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
            'I' => '<span style="font-size: 11px" class="px-2 badge badge-light-info">Izin</span>',
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

    // getMeetingTypeLabel
    public static function getMeetingTypeLabel($type)
    {
        return match ($type) {
            'Learning' => '<span class="badge px-2 py-1 badge-light-info">Belajar</span>',
            'Midterm' => '<span class="badge px-2 py-1 badge-light-warning">UTS</span>',
            'Final' => '<span class="badge px-2 py-1 badge-light-warning">UAS</span>',
            'Holiday' => '<span class="badge px-2 py-1 badge-light-danger">Libur</span>',
            default => $type
        };
    }

    public static function getTaskTypeLabel($type)
    {
        return match ($type) {
            'individual' => 'Individu',
            'group' => 'Kelompok',
            default => $type
        };
    }

    public static function getExamTypeLabel($type)
    {
        return match ($type) {
            'Midterm' => 'UTS',
            'Final' => 'UAS',
            'Quiz' => 'Kuis',
            default => $type
        };
    }

    public static function getExamModeLabel($type)
    {
        return match ($type) {
            'Closed Book' => 'Tutup Buku',
            'Open Book' => 'Buku Terbuka',
            default => $type
        };
    }

    public static function getExamDisplayStatusLabel($type)
    {
        return match ($type) {
            'Visible' => 'Tutup Buku',
            'Hiddem' => 'Buku Terbuka',
            default => $type
        };
    }

    public static function isValidUrl($value)
    {
        // Simple URL check - you might want a more robust validation
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function getContentIcon($value)
    {
        $parts = explode('.', $value);
        $ext = strtolower(end($parts));

        if (in_array($ext, ['doc', 'docx'])) {
            return "fa fa-file-word text-primary";
        }
        if (in_array($ext, ['pdf'])) {
            return "fa fa-file-pdf text-danger";
        }
        if (in_array($ext, ['xls', 'xlsx'])) {
            return "fa fa-file-excel text-success";
        }
        if (in_array($ext, ['ppt', 'pptx'])) {
            return "fa fa-file-powerpoint text-warning";
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            return "fa fa-file-image text-info";
        }

        return "fa fa-file";
    }

    public static function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xlsx', 'xls', 'ppt', 'pptx'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        } elseif (in_array($extension, $archiveExtensions)) {
            return 'archive';
        } else {
            return 'other';
        }
    }
}
