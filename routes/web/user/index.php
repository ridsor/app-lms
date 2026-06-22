<?php

use App\Http\Controllers\User\UKKOperatorController;
use App\Http\Controllers\User\UKKController;
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
use App\Http\Controllers\User\ExamController;
use App\Http\Controllers\User\MeetingController;
use App\Http\Controllers\User\MeetingTextController;
use App\Http\Controllers\User\QuestionBankController;
use App\Http\Controllers\User\QuestionController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\User\TaskSubmissionController;
use App\Http\Controllers\User\TeachingJournalController;
use Illuminate\Support\Facades\Route;

Route::get('/ukk/{id}/file', [UKKController::class, 'getFile'])->name('user.ukk.file.get');
Route::get('/materi/{materi_id}/file', [MaterialController::class, 'getFile'])->name('user.material.file.get');
Route::get('/ukk/praktik/file-jawaban/{result_id}/{filename}', [UKKController::class, 'getFileSubmission'])->name('user.ukk.praktik.file.get');
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

    Route::delete('operator-ukk/hapus', [UKKOperatorController::class, 'bulkDestroy'])->name('user.ukk-operator.bulkDestroy');
    Route::get('operator-ukk/export', [UKKOperatorController::class, 'export'])->name('user.ukk-operator.export');
    Route::resource('/operator-ukk', UKKOperatorController::class)->except(['create', 'show'])->names('user.ukk-operator');

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
    Route::post('/jadwal/kelas/{classId}/sinkronisasi', [ScheduleController::class, 'syncByClass'])->name('user.schedule.syncByClass');
    Route::get('/jadwal/{id}', [ScheduleController::class, 'showBySchedule'])->name('user.schedule.showBySchedule');
    Route::put('/jadwal/{id}/{schedule_time_id}', [ScheduleController::class, 'update'])->name('user.schedule.update');
    Route::delete('/jadwal/{schedule_time_id}', [ScheduleController::class, 'destroy'])->name('user.schedule.destroy');
    Route::get('/jadwal/{id}/{schedule_time_id}/edit', [ScheduleController::class, 'edit'])->name('user.schedule.edit');
    Route::get('/jadwal/{id}/pertemuan/{meeting_id}', [ScheduleController::class, 'showByMeeting'])->name('user.schedule.showByMeeting');
    Route::put('/jadwal/{id}/pertemuan/{meeting_id}', [MeetingController::class, 'update'])->name('user.schedule.update');

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
    Route::get('/jadwal/pertemuan/tugas/{id}/hasil/export', [TaskController::class, 'exportResult'])->name('user.task.result.export');

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
    Route::get('/kehadiran/jadwal/{id}/report', [AttendanceController::class, 'reportAttendancRecap'])->name('user.attendance.reportAttendancRecap');

    Route::get('/tugas', [TaskController::class, 'index'])->name('user.task.index');
    Route::get('/tugas/{task_id}/file', [TaskController::class, 'getFile'])->name('user.task.file.get');
    Route::get('/tugas/{task_id}/file/download', [TaskController::class, 'downloadFile'])->name('user.task.file.download');
    Route::get('/tugas/{task_id}', [TaskSubmissionController::class, 'show'])->name('user.tasksubmission.show');
    Route::post('/tugas/{task_id}', [TaskSubmissionController::class, 'submitTask'])->name('user.tasksubmission.store');

    Route::get('/jurnal-mengajar/kelas', [TeachingJournalController::class, 'classList'])->name('user.journal.classlist');
    Route::get('/jurnal-mengajar/pertemuan/{meeting_id}', [TeachingJournalController::class, 'showJournal'])->name('user.journal.showMeeting');
    Route::get('/jurnal-mengajar/kelas/{classId}', [TeachingJournalController::class, 'scheduleByKelas'])->name('user.journal.schedulebyclass');
    Route::get('/jurnal-mengajar/jadwal/{schedule_id}/pertemuan', [TeachingJournalController::class, 'meetingBySchedule'])->name('user.journal.meetingBySchedule');
    Route::get('/jurnal-mengajar/{id}/export', [TeachingJournalController::class, 'export'])->name('user.journal.export');

    Route::get('/bank-soal', [QuestionBankController::class, 'index'])->name('user.question-bank.index');
    Route::get('/bank-soal/copy', [QuestionBankController::class, 'copy'])->name('user.question-bank.copy');
    Route::post('/bank-soal', [QuestionBankController::class, 'store'])->name('user.question-bank.store');
    Route::get('/bank-soal/{id}', [QuestionBankController::class, 'show'])->name('user.question-bank.show');
    Route::get('/bank-soal/{id}/edit', [QuestionBankController::class, 'edit'])->name('user.question-bank.edit');
    Route::put('/bank-soal/{id}', [QuestionBankController::class, 'update'])->name('user.question-bank.update');
    Route::delete('/bank-soal/{id}', [QuestionBankController::class, 'destroy'])->name('user.question-bank.destroy');

    Route::get('/soal/{id}/edit', [QuestionController::class, 'edit'])->name('user.question.edit');
    Route::post('/soal/{id}', [QuestionController::class, 'store'])->name('user.question.store');
    Route::put('/soal/{id}', [QuestionController::class, 'update'])->name('user.question.update');
    Route::delete('/soal/{id}', [QuestionController::class, 'destroy'])->name('user.question.destroy');
    Route::get('/soal/{id}/file', [QuestionController::class, 'getFile'])->name('user.question.file.get');
    Route::get('/soal/{id}/{option}/file', [QuestionController::class, 'getFileOption'])->name('user.question.option.file.get');
    Route::get('/soal/{id}/file/download', [QuestionController::class, 'downloadFile'])->name('user.question.file.download');

    Route::get('/ujian', [ExamController::class, 'index'])->name('user.exam.index');
    Route::post('/ujian', [ExamController::class, 'store'])->name('user.exam.store');
    Route::put('/ujian/{id}', [ExamController::class, 'update'])->name('user.exam.update');
    Route::get('/ujian/{id}', [ExamController::class, 'show'])->name('user.exam.show');
    Route::get('/ujian/{id}/edit', [ExamController::class, 'edit'])->name('user.exam.edit');
    Route::delete('/ujian/{id}', [ExamController::class, 'destroy'])->name('user.exam.destroy');
    Route::get('/ujian/{id}/soal', [ExamController::class, 'showQuestion'])->name('user.exam.question.show');
    Route::get('/ujian/template-soal/download', [ExamController::class, 'downloadTemplate'])->name('user.exam.template.download');
    Route::post('/ujian/{id}/import-soal', [ExamController::class, 'importQuestions'])->name('user.exam.importQuestions');
    Route::post('/ujian/{exam_id}/copy/{id}', [ExamController::class, 'copyQuestions'])->name('user.exam.copyQuestions');
    Route::get('/ujian/{id}/hasil', [ExamController::class, 'showResult'])->name('user.exam.result.show');
    Route::patch('/ujian/{id}/hasil/reset', [ExamController::class, 'resetResult'])->name('user.exam.result.reset');
    Route::patch('/ujian/{id}/hasil/{exam_result_id}/reset', [ExamController::class, 'resetResultById'])->name('user.exam.result.reset.byId');
    Route::get('/ujian/{id}/hasil/export', [ExamController::class, 'exportResult'])->name('user.exam.result.export');

    Route::get('/ujian/{id}/info', [ExamController::class, 'info'])->name('user.exam.info');
    Route::get('/ujian/{id}/pengerjaan', [ExamController::class, 'workmanship'])->middleware('exam_time')->name('user.exam.workmanship');
    Route::post('/ujian/{id}/pengerjaan/answer', [ExamController::class, 'setAnswerByExamResult'])->name('user.exam.workmanship.answer');
    Route::post('/ujian/{id}/pengerjaan', [ExamController::class, 'workmanshipSubmit'])->name('user.exam.workmanship.submit');
    Route::post('/ujian/{id}/mulai', [ExamController::class, 'examStart'])->name('user.exam.start');
    Route::get('/ujian/{id}/pengerjaan/hasil', [ExamController::class, 'workmanshipResult'])->name('user.exam.workmanship.result');
    Route::get('/ujian/{exam_id}/evaluasi/{page?}', [ExamController::class, 'evaluation'])->name('user.exam.evaluation');
    Route::get('/ujian/{id}/pengerjaan/soal', [ExamController::class, 'getRandomQuestions'])->name('user.exam.workmanship.soal');
    Route::post('/ujian/{id}/score/{answer_id}', [ExamController::class, 'updateAnswerScore'])->name('user.exam.update-answer-score');

    Route::get('/ukk', [UKKController::class, 'index'])->name('user.ukk.index');
    Route::post('/ukk', [UKKController::class, 'store'])->name('user.ukk.store');
    Route::get('/ukk/{id}/edit', [UKKController::class, 'edit'])->name('user.ukk.edit');
    Route::get('/ukk/{id}', [UKKController::class, 'show'])->name('user.ukk.show');
    Route::put('/ukk/{id}', [UKKController::class, 'update'])->name('user.ukk.update');
    Route::delete('/ukk/{id}', [UKKController::class, 'destroy'])->name('user.ukk.destroy');
    Route::get('/ukk/{id}/soal', [UKKController::class, 'showQuestion'])->name('user.ukk.question.show');
    Route::get('/ukk/template-soal/download', [UKKController::class, 'downloadTemplate'])->name('user.ukk.template.download');
    Route::post('/ukk/{id}/import-soal', [UKKController::class, 'importQuestions'])->name('user.ukk.importQuestions');
    Route::get('/ukk/{id}/hasil/teori', [UKKController::class, 'showResultTeori'])->name('user.ukk.result.teori');
    Route::get('/ukk/{id}/hasil/teori/export', [UKKController::class, 'exportResultTeori'])->name('user.ukk.result.teori.export');
    Route::get('/ukk/{id}/download', [UKKController::class, 'downloadFile'])->name('user.ukk.file.download');
    Route::patch('/ukk/{id}/hasil/teori/reset', [UKKController::class, 'resetResult'])->name('user.ukk.result.teori.reset');
    Route::patch('/ukk/{id}/hasil/teori/{ukk_result_id}/reset', [UKKController::class, 'resetResultById'])->name('user.ukk.result.teori.reset.byId');
    Route::get('/ukk/{id}/hasil/praktik', [UKKController::class, 'showResultPraktik'])->name('user.ukk.result.praktik');
    Route::get('/ukk/{id}/teori/info', [UKKController::class, 'theoryInfo'])->name('user.ukk.teori.info');
    Route::post('/ukk/{id}/teori/mulai', [UKKController::class, 'theoryStart'])->name('user.ukk.teori.start');
    Route::get('/ukk/{id}/teori/pengerjaan', [UKKController::class, 'theoryWorkmanship'])->middleware('ukk_time')->name('user.ukk.teori.workmanship');
    Route::get('/ukk/{id}/teori/pengerjaan/soal', [UKKController::class, 'getRandomQuestions'])->name('user.ukk.teori.workmanship.soal');
    Route::post('/ukk/{id}/teori/pengerjaan/answer', [UKKController::class, 'setAnswerByUKKResult'])->name('user.ukk.teori.workmanship.answer');
    Route::post('/ukk/{id}/teori/pengerjaan', [UKKController::class, 'theorySubmit'])->name('user.ukk.teori.workmanship.submit');
    Route::get('/ukk/{id}/teori/pengerjaan/hasil', [UKKController::class, 'theoryWorkmanshipResult'])->name('user.ukk.teori.workmanship.result');
    Route::get('/ukk/{id}/teori/evaluasi/{page?}', [UKKController::class, 'evaluation'])->name('user.ukk.evaluation');
    Route::post('/ukk/{id}/teori/score/{answer_id}', [UKKController::class, 'updateAnswerScore'])->name('user.ukk.updateAnswerScore');
    Route::get('/ukk/{id}/praktik/info', [UKKController::class, 'practiceInfo'])->name('user.ukk.praktik.info');
    Route::post('/ukk/{id}/praktik/submit', [UKKController::class, 'practiceSubmit'])->name('user.ukk.praktik.submit');
    Route::get('/ukk/{id}/praktik/evaluasi/{page?}', [UKKController::class, 'evaluationPraktik'])->name('user.ukk.praktik.evaluation');
    Route::post('/ukk/praktik/update-skor/{result_id}', [UKKController::class, 'updatePracticeScore'])->name('user.ukk.praktik.updateScore');
    Route::get('/ukk/{id}/praktik/export', [UKKController::class, 'exportResultPraktik'])->name('user.ukk.result.praktik.export');
    Route::get('/ukk/praktik/print/{result_id}', [UKKController::class, 'printResultPraktik'])->name('user.ukk.result.praktik.print');
    Route::get('/ukk/{id}/praktik/print-student/{student_id}', [UKKController::class, 'printResultPraktikByStudent'])->name('user.ukk.result.praktik.printByStudent');
});
