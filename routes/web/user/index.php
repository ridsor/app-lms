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
use App\Http\Controllers\User\QuestionBankController;
use App\Http\Controllers\User\QuestionController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\User\TaskSubmissionController;
use App\Http\Controllers\User\TeachingJournalController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:vice-principal|teacher|student|parent|operator"])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('user.home');

    Route::delete('ruangan/hapus', [RoomController::class, 'bulkDestroy'])->name('user.room.bulkDestroy');
    Route::resource('/ruangan', RoomController::class)->except(['create', 'show'])->names('user.room');

    Route::delete('periode/hapus', [PeriodController::class, 'bulkDestroy'])->name('user.period.bulkDestroy');
    Route::post('/periode/active/{id}', [PeriodController::class, 'active'])->name('user.period.active');
    Route::resource('/periode', PeriodController::class)->except(['create', 'show'])->names('user.period');

    Route::delete('kelas/hapus', [ClassController::class, 'bulkDestroy'])->name('user.class.bulkDestroy');
    Route::resource('/kelas', ClassController::class)->except(['create', 'show'])->names('user.class');

    Route::delete('siswa/hapus', [StudentController::class, 'bulkDestroy'])->name('user.student.bulkDestroy');
    Route::patch('siswa/{id}/reset-password', [StudentController::class, 'resetPassword'])->name('user.student.resetPassword');
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
    Route::get('/jadwal/pertemuan/tugas/{task_id}/pengumpulan', [TaskController::class, 'collection'])->name('user.task.collection');
    Route::get('/jadwal/pertemuan/tugas/{task_id}/edit', [TaskController::class, 'edit'])->name('user.task.edit');
    Route::put('/jadwal/pertemuan/tugas/{task_id}', [TaskController::class, 'update'])->name('user.task.update');
    Route::delete('/jadwal/pertemuan/tugas/{task_id}', [TaskController::class, 'destroy'])->name('user.task.destroy');
    Route::patch('/jadwal/pertemuan/tugas/{task_id}/tampilkan_nilai', [TaskController::class, 'value_displayed'])->name('user.task.value_displayed');
    Route::get('/jadwal/pertemuan/tugas/{task_id}/penilaian/{page?}', [TaskSubmissionController::class, 'evaluation'])->name('user.task.evaluation');
    Route::post('/jadwal/pertemuan/tugas/penilaian/{task_submissio_id}', [TaskSubmissionController::class, 'postEvaluation'])->name('user.task.evaluation.post');
    Route::get('/jadwal/pertemuan/tugas/penilaian/{task_submission_id}/{id}/file', [TaskSubmissionController::class, 'downloadFile'])->name('user.task.evaluation.file.download');

    Route::post('/jadwal/pertemuan/{meeting_id}/text', [MeetingTextController::class, 'store'])->name('user.meeting_text.store');
    Route::get('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'index'])->name('user.meeting_text.index');
    Route::put('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'update'])->name('user.meeting_text.update');
    Route::delete('/jadwal/pertemuan/text/{meeting_text_id}', [MeetingTextController::class, 'destroy'])->name('user.meeting_text.destroy');

    Route::get('/kehadiran/{schedule_id}/pertemuan/{meeting_id}', [AttendanceController::class, 'edit'])->name('user.attendance.edit');
    Route::get('/kehadiran/pertemuan/{meeting_id}', [AttendanceController::class, 'showMeeting'])->name('user.attendance.showMeeting');
    Route::patch('/kehadiran/pertemuan/{meeting_id}', [AttendanceController::class, 'update'])->name('user.attendance.update');
    Route::get('/kehadiran', [AttendanceController::class, 'index'])->name('user.attendance.index');
    Route::get('/kehadiran/kelas', [AttendanceController::class, 'classList'])->name('user.attendance.classlist');
    Route::get('/kehadiran/kelas/{classId}', [AttendanceController::class, 'scheduleByKelas'])->name('user.attendance.schedulebyclass');
    Route::get('/kehadiran/jadwal/{schedule_id}/pertemuan', [AttendanceController::class, 'meetingBySchedule'])->name('user.attendance.meetingBySchedule');
    Route::get('/kehadiran/jadwal/{id}', [AttendanceController::class, 'showAttendancRecap'])->name('user.attendance.showAttendancRecap');

    Route::get('/tugas', [TaskController::class, 'index'])->name('user.task.index');
    Route::get('/tugas/{task_id}/file', [TaskController::class, 'getFile'])->name('user.task.file.get');
    Route::get('/tugas/{task_id}/file/download', [TaskController::class, 'downloadFile'])->name('user.task.file.download');
    Route::get('/tugas/{task_id}', [TaskSubmissionController::class, 'show'])->name('user.tasksubmission.show');
    Route::post('/tugas/{task_id}', [TaskSubmissionController::class, 'submitTask'])->name('user.tasksubmission.store');

    Route::get('/jurnal-mengajar/kelas', [TeachingJournalController::class, 'classList'])->name('user.journal.classlist');
    Route::get('/jurnal-mengajar/pertemuan/{meeting_id}', [TeachingJournalController::class, 'showJournal'])->name('user.journal.showMeeting');
    Route::get('/jurnal-mengajar/kelas/{classId}', [TeachingJournalController::class, 'scheduleByKelas'])->name('user.journal.schedulebyclass');
    Route::get('/jurnal-mengajar/jadwal/{schedule_id}/pertemuan', [TeachingJournalController::class, 'meetingBySchedule'])->name('user.journal.meetingBySchedule');

    Route::get('/bank-soal', [QuestionBankController::class, 'index'])->name('user.question-bank.index');
    Route::post('/bank-soal', [QuestionBankController::class, 'store'])->name('user.question-bank.store');
    Route::get('/bank-soal/{id}', [QuestionBankController::class, 'show'])->name('user.question-bank.show');
    Route::get('/bank-soal/{id}/edit', [QuestionBankController::class, 'edit'])->name('user.question-bank.edit');
    Route::put('/bank-soal/{id}', [QuestionBankController::class, 'update'])->name('user.question-bank.update');
    Route::delete('/bank-soal/{id}', [QuestionBankController::class, 'destroy'])->name('user.question-bank.destroy');

    Route::get('/soal/{id}/edit', [QuestionController::class, 'edit'])->name('user.question.edit');
    Route::put('/soal/{id}', [QuestionController::class, 'update'])->name('user.question.update');
    Route::delete('/soal/{id}', [QuestionController::class, 'destroy'])->name('user.question.destroy');
    Route::post('/soal/{id}/bank-soal', [QuestionController::class, 'storeForQuestionBank'])->name('user.question.store');
    Route::post('/soal/{id}/ujian', [QuestionController::class, 'storeForExam'])->name('user.question.store');
    Route::get('/soal/{id}/file', [QuestionController::class, 'getFile'])->name('user.question.file.get');
    Route::get('/soal/{id}/{option}/file', [QuestionController::class, 'getFileOption'])->name('user.question.option.file.get');
    Route::get('/soal/{id}/file/download', [QuestionController::class, 'downloadFile'])->name('user.question.file.download');
});
