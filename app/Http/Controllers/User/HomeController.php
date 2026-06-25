<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\UKK;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $today = \Carbon\Carbon::now()->format('l');

        if ($request->user()->hasRole(['teacher', 'student', 'parent'])) {
            $schedules = Schedule::query()
                ->select('schedules.*')
                ->addSelect([
                    'first_day' => ScheduleTime::select('day')
                        ->whereColumn('schedule_id', 'schedules.id')
                        ->orderBy('start_time')
                        ->limit(1),
                    'first_start_time' => ScheduleTime::select('start_time')
                        ->whereColumn('schedule_id', 'schedules.id')
                        ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
                        ->orderBy('start_time')
                        ->limit(1),
                ])
                ->with([
                    'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
                    'class.major' => fn($query) => $query->select('id', 'name'),
                    'subject' => fn($query) => $query->select('id', 'name', 'code'),
                    'teacher' => fn($query) => $query->select('id', 'name', 'user_id')->with('user:id,image,username'),
                    'schedule_times' => function ($query) {
                        $query->orderByRaw(
                            "FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')"
                        )->orderBy('start_time');
                    },
                ])
                ->whereHas('schedule_times', function ($q) use ($today) {
                    $q->where('day', $today);
                })
                ->whereHas('period', function ($q) {
                    $q->where('status', true);
                })
                ->filterByPermission($request->user())
                ->orderByRaw("FIELD(first_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
                ->orderBy('first_start_time')
                ->get();

            $countTasks = 0;
            if ($request->user()->hasRole('teacher')) {
                $countTasks = Task::filterByPermission($request->user())->whereHas('submissions', function ($query) {
                    $query->whereNull('score');
                })->count();
            } elseif ($request->user()->hasRole('student')) {
                $countTasks = Task::filterByPermission($request->user())->whereDoesntHave('submissions', function ($query) use ($request) {
                    $query->where('student_id', $request->user()->student->id);
                })->count();
            } elseif ($request->user()->hasRole('parent')) {
                $countTasks = Task::filterByPermission($request->user())->whereDoesntHave('submissions', function ($query) use ($request) {
                    $query->where('student_id', $request->user()->parent->id);
                })->count();
            }

            $countExams = 0;
            if ($request->user()->hasRole('teacher')) {
                $countExams = Exam::filterByPermission($request->user())->whereHas('results', function ($query) {
                    $query->whereHas('answers', function ($subQuery) {
                        $subQuery->whereNull('score');
                    });
                })->count();
            } elseif ($request->user()->hasRole('student')) {
                $countExams = Exam::filterByPermission($request->user())->whereDoesntHave('results', function ($query) use ($request) {
                    $query->where('student_id', $request->user()->student->id);
                })->count();
            } elseif ($request->user()->hasRole('parent')) {
                $countExams = Exam::filterByPermission($request->user())->whereDoesntHave('results', function ($query) use ($request) {
                    $query->where('student_id', $request->user()->parent->id);
                })->count();
            }

            return view('user.home', compact('schedules', 'countTasks', 'countExams'));
        } elseif ($request->user()->hasRole('vice-principal')) {
            $teacherCount = Teacher::count();
            $studentCount = Student::where('status', 'active')->count();
            $classCount   = SchoolClass::count();

            $data = SchoolClass::withCount('students')
                ->with([
                    'schedules' => function ($q) {
                        // Gabungkan select kolom dan withCount di satu tempat
                        $q->select('id', 'class_id')
                            ->withCount([
                                // journal: meeting yang punya teaching_journal
                                'meetings as present_count' => fn($query) => $query
                                    ->where('type', '!=', 'Holiday')
                                    ->whereDate('date', '<=', now())
                                    ->has('teaching_journal'),

                                // total meeting
                                'meetings as meeting_count' => fn($query) => $query
                                    ->where('type', '!=', 'Holiday')
                                    ->whereDate('date', '<=', now())
                            ]);
                    },
                    // Load relasi meetings di dalam schedules beserta attendance count-nya
                    'schedules.meetings' => fn($query) => $query
                        ->select(['id', 'schedule_id', 'type', 'date'])
                        ->where('type', '!=', 'Holiday')
                        ->whereDate('date', '<=', Carbon::today())
                        ->withCount([
                            'attendances as present_count' => fn($q) => $q->where('status', 'H')
                        ]),
                ])->get();

            $totalAttendancePercentage = 0.0;
            $totalJournalPercentage    = 0.0;

            foreach ($data as $class) {
                $totalStudents = $class->students_count;

                if ($totalStudents === 0) {
                    $class->attendance_percentage = 0.0;
                    $class->journal_percentage    = 0.0;
                    continue;
                }

                $scheduleAttendance = [];
                $scheduleJournal    = [];

                foreach ($class->schedules as $schedule) {
                    // --- Attendance (per meeting) ---
                    if ($schedule->meetings->isNotEmpty()) {
                        $meetingPercentages = $schedule->meetings->map(
                            fn($m) => round(($m->present_count / $totalStudents) * 100, 2)
                        );

                        if ($meetingPercentages->isNotEmpty()) {
                            $scheduleAttendance[] = $meetingPercentages->avg();
                        }
                    }

                    // --- Journal ---
                    if ($schedule->meeting_count > 0) {
                        $scheduleJournal[] = round(($schedule->present_count / $schedule->meeting_count) * 100, 2);
                    }
                }

                $class->attendance_percentage = !empty($scheduleAttendance)
                    ? round(array_sum($scheduleAttendance) / count($scheduleAttendance), 2)
                    : 0.0;

                $class->journal_percentage = !empty($scheduleJournal)
                    ? round(array_sum($scheduleJournal) / count($scheduleJournal), 2)
                    : 0.0;

                $totalAttendancePercentage += $class->attendance_percentage;
                $totalJournalPercentage    += $class->journal_percentage;
            }

            // --- Rata-rata semua kelas ---
            $attendannce_percentage = $classCount > 0
                ? round($totalAttendancePercentage / $classCount, 2)
                : 0.0;

            $journal_percentage = $classCount > 0
                ? round($totalJournalPercentage / $classCount, 2)
                : 0.0;

            return view('user.home', compact(
                'teacherCount',
                'studentCount',
                'attendannce_percentage',
                'journal_percentage'
            ));
        } elseif ($request->user()->hasRole('operator') && $request->user()->can('ukk.evaluation')) {
            $countUKKTheory = \App\Models\UKKResultTheory::whereHas('ukk', function ($q) use ($request) {
                $q->filterByPermission($request->user());
            })->where('status', 'completed')->count();

            $countUKKPractice = \App\Models\UKKResultPractice::whereHas('ukk', function ($q) use ($request) {
                $q->filterByPermission($request->user());
            })->count();

            $ukks = UKK::filterByPermission($request->user())
                ->with(['period'])
                ->latest()
                ->limit(5)
                ->get();

            return view('user.home', compact('countUKKTheory', 'countUKKPractice', 'ukks'));
        } else {
            return view('user.home');
        }
    }
}
