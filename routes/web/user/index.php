<?php

use App\Http\Controllers\User\MaterialController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\RoomController;
use App\Http\Controllers\User\PeriodController;
use App\Http\Controllers\User\ClassController;
use App\Http\Controllers\User\StudentController;
use App\Http\Controllers\User\MajorController;
use App\Http\Controllers\User\TeacherController;
use App\Http\Controllers\User\CurriculumController;
use App\Http\Controllers\User\SubjectController;
use App\Http\Controllers\User\ScheduleController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\MeetingController;
use App\Http\Controllers\User\MeetingTextController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\User\TeachingJournalController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:vice-principal|teacher|student|parent"])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('user.home');

    Route::delete('ruangan/hapus', [RoomController::class, 'bulkDestroy'])->name('user.room.bulkDestroy');
    Route::resource('/ruangan', RoomController::class)->except(['create', 'show'])->names('user.room');

    Route::delete('periode/hapus', [PeriodController::class, 'bulkDestroy'])->name('user.period.bulkDestroy');
    Route::post('/periode/active/{id}', [PeriodController::class, 'active'])->name('user.period.active');
    Route::resource('/periode', PeriodController::class)->except(['create', 'show'])->names('user.period');

    Route::delete('kelas/hapus', [ClassController::class, 'bulkDestroy'])->name('user.class.bulkDestroy');
    Route::resource('/kelas', ClassController::class)->except(['create', 'show'])->names('user.class');

    Route::delete('siswa/hapus', [StudentController::class, 'bulkDestroy'])->name('user.student.bulkDestroy');
    Route::patch('siswa/edit', [StudentController::class, 'bulkEdit'])->name('user.student.bulkEdit');
    Route::get('siswa/akun/export', [StudentController::class, 'exportStudentAccount'])->name('user.student.account.export');
    Route::get('siswa/orang-tua/akun/export', [StudentController::class, 'exportParentAccount'])->name('user.student.parent.account.export');
    Route::resource('/siswa', StudentController::class)->except(['create'])->except(['create'])->names('user.student');

    Route::delete('jurusan/hapus', [MajorController::class, 'bulkDestroy'])->name('user.major.bulkDestroy');
    Route::resource('/jurusan', MajorController::class)->except(['create', 'show'])->names('user.major');

    Route::delete('guru/hapus', [TeacherController::class, 'bulkDestroy'])->name('user.teacher.bulkDestroy');
    Route::patch('guru/edit', [TeacherController::class, 'bulkEdit'])->name('user.teacher.bulkEdit');
    Route::get('guru/akun/export', [TeacherController::class, 'exportAccount'])->name('user.teacher.account.export');
    Route::resource('/guru', TeacherController::class)->except(['create'])->names('user.teacher');

    Route::post('/kurikulum/active/{id}', [CurriculumController::class, 'active'])->name('user.curriculum.active');
    Route::delete('/kurikulum/hapus', [CurriculumController::class, 'bulkDestroy'])->name('user.curriculum.bulk-destroy');
    Route::resource('/kurikulum', CurriculumController::class)->except(['create', 'show'])->names('user.curriculum');

    Route::delete('kurikulum/{curriculum_id}/mata-pelajaran/hapus', [SubjectController::class, 'bulkDestroy'])->name('user.subject.bulkDestroy');
    Route::resource('/kurikulum/{curriculum_id}/mata-pelajaran', SubjectController::class)
        ->except(['create', 'show'])
        ->names('user.subject');

    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('user.schedule.index');
    Route::post('/jadwal', [ScheduleController::class, 'store'])->name('user.schedule.store');
    Route::delete('jadwal/hapus', [ScheduleController::class, 'bulkDestroy'])->name('user.schedule.bulkDestroy');
    Route::get('/jadwal/kelas', [ScheduleController::class, 'classList'])->name('user.schedule.classlist');
    Route::get('/jadwal/kelas/{classId}', [ScheduleController::class, 'viewByClass'])->name('user.schedule.byclass');
    Route::get('/jadwal/{code}', [ScheduleController::class, 'showBySchedule'])->name('user.schedule.showBySchedule');
    Route::put('/jadwal/{code}/{schedule_time_id}', [ScheduleController::class, 'update'])->name('user.schedule.update');
    Route::delete('/jadwal/{schedule_time_id}', [ScheduleController::class, 'destroy'])->name('user.schedule.destroy');
    Route::get('/jadwal/{id}/{schedule_time_id}/edit', [ScheduleController::class, 'edit'])->name('user.schedule.edit');
    Route::get('/jadwal/{code}/pertemuan/{meeting_id}', [ScheduleController::class, 'showByMeeting'])->name('user.schedule.showByMeeting');
    Route::put('/jadwal/{code}/pertemuan/{meeting_id}', [MeetingController::class, 'update'])->name('user.schedule.update');

    Route::post('/jadwal/pertemuan/{meeting_id}/jurnal', [TeachingJournalController::class, 'store'])->name('user.teaching_journal.store');
    Route::patch('/jadwal/pertemuan/{meeting_id}/mulai-belajar', [MeetingController::class, 'startLearning'])->name('user.schedule.startLearning');
    Route::patch('/jadwal/pertemuan/{meeting_id}/kehadiran', [AttendanceController::class, 'updateByMeeting'])->name('user.attendance.updateByMeeting');

    Route::post('/jadwal/pertemuan/{meeting_id}/materi', [MaterialController::class, 'store'])->name('user.material.store');
    Route::get('/jadwal/pertemuan/materi/{materi_id}', [MaterialController::class, 'show'])->name('user.material.show');
    Route::put('/jadwal/pertemuan/materi/{materi_id}', [MaterialController::class, 'update'])->name('user.material.update');
    Route::delete('/jadwal/pertemuan/materi/{materi_id}', [MaterialController::class, 'destroy'])->name('user.material.destroy');
    Route::get('/materi/{materi_id}/file/download', [MaterialController::class, 'downloadFile'])->name('user.material.file.download');

    Route::post('/jadwal/pertemuan/{meeting_id}/tugas', [TaskController::class, 'store'])->name('user.task.store');
    Route::get('/jadwal/pertemuan/tugas/{task_id}', [TaskController::class, 'show'])->name('user.task.show');
    Route::put('/jadwal/pertemuan/tugas/{task_id}', [TaskController::class, 'update'])->name('user.task.update');
    Route::delete('/jadwal/pertemuan/tugas/{task_id}', [TaskController::class, 'destroy'])->name('user.task.destroy');
    Route::get('/tugas/{task_id}/file', [TaskController::class, 'getFile'])->name('user.task.file.get');
    Route::get('/tugas/{task_id}/file/download', [TaskController::class, 'downloadFile'])->name('user.task.file.download');

    Route::post('/jadwal/pertemuan/{meeting_id}/text', [MeetingTextController::class, 'store'])->name('user.meeting_text.store');
    Route::get('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'index'])->name('user.meeting_text.index');
    Route::put('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'update'])->name('user.meeting_text.update');
    Route::delete('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'destroy'])->name('user.meeting_text.destroy');

    Route::get('/kehadiran/{schedule_id}/pertemuan/{meeting_id}', [AttendanceController::class, 'edit'])->name('user.attendance.edit');
    Route::get('/kehadiran/pertemuan/{meeting_id}/{schedule_time_id}', [AttendanceController::class, 'showMeeting'])->name('user.attendance.showMeeting');
    Route::patch('/kehadiran/pertemuan/{meeting_id}', [AttendanceController::class, 'update'])->name('user.attendance.update');
    Route::get('/kehadiran', [AttendanceController::class, 'index'])->name('user.attendance.index');
    Route::get('/kehadiran/kelas', [AttendanceController::class, 'classList'])->name('user.attendance.classlist');
    Route::get('/kehadiran/kelas/{classId}', [AttendanceController::class, 'scheduleByKelas'])->name('user.attendance.schedulebyclass');
    Route::get('/kehadiran/jadwal/{id}', [AttendanceController::class, 'showAttendancRecap'])->name('user.attendance.showAttendancRecap');
});
