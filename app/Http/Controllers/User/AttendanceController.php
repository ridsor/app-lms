<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceStatusRequest;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Major;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Models\Subject;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Period;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewPossession', Attendance::class);
    $activePeriod = Period::where('status', true)->first();

    $hasMajor = Major::count() > 0;

    if ($request->ajax()) {
      $data = Schedule::select([
        'schedules.id as id',
        'schedules.class_id',
        'schedules.period_id',
        'subjects.name as subject_name',
        'teachers.name as teacher_name',
        'classes.name as class_name',
        'classes.level as class_level',
        'majors.name as major_name'
      ])
        ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->join('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->join('classes', 'schedules.class_id', '=', 'classes.id')
        ->leftJoin('majors', 'classes.major_id', '=', 'majors.id');

      $data->filterByPermission($request->user());

      // Handle search
      if ($request->filled('search.value')) {
        $search = $request->search['value'];
        $data->where(function ($q) use ($search) {
          $q->where('subjects.name', 'like', '%' . $search . '%')
            ->orWhere('teachers.name', 'like', '%' . $search . '%')
            ->orWhere('classes.name', 'like', '%' . $search . '%')
            ->orWhere('majors.name', 'like', '%' . $search . '%');
        });
      }

      // Additional filters
      if ($request->filled('major')) {
        $data->where('majors.name', 'like', '%' . $request->major . '%');
      }

      if ($request->filled('level')) {
        $data->where('classes.level', $request->level);
      }

      if ($request->filled('class')) {
        $data->where('classes.name', 'like', '%' . $request->class . '%');
      }

      if ($request->filled('periode')) {
        $data->where('schedules.period_id', $request->periode);
      } else {
        $data->where('schedules.period_id', $activePeriod->id ?? 0);
      }

      // Gunakan DataTables langsung tanpa get()
      return DataTables::eloquent($data)
        ->addColumn('No', function ($row) use ($request) {
          static $count = 0;
          $count++;
          return $count;
        })
        ->addColumn('Mata Pelajaran', function ($row) {
          return '
            <div class="product-names">
                <p>' . $row->subject_name . '</p>
            </div>
        ';
        })
        ->addColumn('Kelas', function ($row) {
          if (auth()->user()->hasRole('teacher')) {
            return '
                <span class="badge badge-light-primary">' .
              $row->class_name . ' ' .
              $row->class_level . ' ' .
              ($row->major_name ?: '') .
              '</span>
            ';
          }
          return '';
        })
        ->addColumn('Guru', function ($row) {
          if (auth()->user()->hasRole('student') || auth()->user()->hasRole('parent')) {
            return '
                <div class="product-names">
                    <p>' . $row->teacher_name . '</p>
                </div>
            ';
          }
          return '';
        })
        ->addColumn('Rekap', function ($row) use ($request) {
          $attendancePercentage = $this->calculateAttendancePercentage($row, $request);

          $url = route('user.attendance.showAttendancRecap', ['id' => $row->id]);
          return '
            <div>
                <a href="' . $url . '" class="badge badge-light-primary fs-6">' .
            $attendancePercentage . '%</a>
            </div>
        ';
        })
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.meetingBySchedule', ['schedule_id' => $row->id]);
          return '
            <div>
                <a href="' . $url . '" class="badge badge-light-info">Lihat Pertemuan</a>
            </div>
        ';
        })
        ->rawColumns(['Mata Pelajaran', 'Kelas', 'Guru', 'Rekap', 'Aksi'])
        ->make(true);
    }

    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name')->get();
    $activePeriod = Period::where('status', true)->first();
    $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();

    return view('user.attendance.index', compact('majors', 'classLevels', 'classNames', 'activePeriod', 'periods'));
  }

  public function meetingBySchedule(Request $request, $schedule_id)
  {
    $schedule = Schedule::with(['subject:id,code', 'class' => fn($q) => $q->withCount(['students'])])->findOrFail($schedule_id);
    $this->authorize('view', $schedule);

    if ($request->ajax()) {
      $data = Meeting::select('meetings.id as id', 'meetings.schedule_id as schedule_id', 'date', 'started_at', 'schedule_time_id', 'schedule_times.start_time as start_time', 'schedule_times.end_time as end_time')
        ->join('schedule_times', 'schedule_times.id', '=', 'meetings.schedule_time_id')
        ->withCount([
          'attendances as present_count' => function ($query) {
            $query->where('status', 'H');
          }
        ])
        ->with([
          'schedule.class' => function ($query) {
            $query->withCount('students');
          },
          'schedule_time:id,start_time,end_time',
          'attendances' => function ($q) use ($request) {
            $q->select(['status', 'user_id', 'meeting_id']);
            if ($request->user()->hasRole('student')) {
              $q->where('user_id', $request->user()->id);
            } else if ($request->user()->hasRole('parent')) {
              $q->where('user_id', $request->user()->parent->user->id);
            }
          }
        ])
        ->where('meetings.schedule_id', $schedule_id)->orderBy('meetings.date', 'asc')->orderBy('schedule_times.start_time', 'asc');

      $data = $data->get();

      $data = $data->map(function ($meeting) use ($schedule) {
        $totalStudents = $schedule->class->students_count;
        $attendancePercentage = 0.0;

        if ($totalStudents > 0) {
          $attendancePercentage = round(($meeting->present_count / $totalStudents) * 100, 1);
        }

        $meeting->attendance_percentage = $attendancePercentage;
        return $meeting;
      });

      $dataTable = DataTables::of($data);

      $dataTable
        ->addColumn('Waktu', function ($row) {
          $html = '
          <div class="product-names">
          <p class="text-nowrap">' . ($row->formatted_date . ' ' . $row->schedule_time->formatted_staart_time . ' - ' . $row->schedule_time->formatted_end_time . ' WIT') . '</p>
          </div>
          ';
          return $html;
        })
        ->addColumn('', function ($row) use ($schedule, $request) {
          if ($request->user()->hasRole('student') || $request->user()->hasRole('parent')) {
            return '<button
                        class="bg-transparent border-0 p-0 m-0 text-start"
                        onclick="handleDetailMeeting(' . $row->id . ')">
                        ' . Helper::getAttendanceLabel(optional($row->attendances)[0]->status ?? null) . '
                    </button>';
          } else {
            $url = route('user.attendance.edit', [
              'schedule_id' => $schedule->id,
              'meeting_id' => $row->id,
            ]);
            return '
            <div class="">
            <a href="' . $url . '" class="badge ' . (is_null($row->started_at) ? 'badge-light-secondary' : 'badge-light-primary') . ' fs-6">' . $row->attendance_percentage . '%</a>
            </div>
            ';
          }
        });

      return $dataTable
        ->rawColumns(['Waktu', ''])
        ->make(true);
    }

    return view('user.attendance.meeting-by-schedule', compact('schedule'));
  }

  public function showAttendancRecap(Request $request, $id)
  {
    $this->authorize('viewAny', Attendance::class);

    $schedule = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'class.students' => function ($query) use ($request) {
        $query->select('id', 'name', 'nisn', 'user_id', 'class_id', 'parent_id');
        if ($request->user()->hasRole('parent')) {
          $query->where('parent_id', $request->user()->id);
        } elseif ($request->user()->hasRole('student')) {
          $query->where('user_id', $request->user()->id);
        }
      },
      'class.students.user' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id', 'semester', 'academic_year'),
      'subject' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'meetings' => function ($query) {
        $query->select('id', 'schedule_id', 'started_at', 'schedule_time_id', 'date')->orderBy('date', 'asc');
      },
      'meetings.attendances:id,status,user_id,meeting_id',
    ])->filterByPermission($request->user())
      ->findOrFail($id);

    $meetings = $schedule->meetings;
    $students = $schedule->class->students;
    $attendances = [];

    foreach ($students as $student) {
      $studentAttendance = [];
      $totalAttendance = 0;
      $totalSick = 0;
      $totalPermission = 0;
      $totalAbsence = 0;
      foreach ($meetings as $meeting) {
        $attendance = $meeting->attendances->firstWhere('user_id', $student->user_id);
        $status = $attendance ? $attendance->status : null;
        $studentAttendance[] = $status;
        if ($status === 'H') {
          $totalAttendance++;
        }
        if ($status === 'S') {
          $totalSick++;
        }
        if ($status === 'I') {
          $totalPermission++;
        }
        if ($status === 'A') {
          $totalAbsence++;
        }
      }
      $attendances[] = [
        'student' => $student,
        'attendances' => $studentAttendance,
        'total_attendance' => $totalAttendance,
        'total_sick' => $totalSick,
        'total_permission' => $totalPermission,
        'total_absence' => $totalAbsence,
      ];
    }

    return view('user.attendance.show-attendance-recap', compact('schedule', 'attendances'));
  }

  public function reportAttendancRecap(Request $request, $id)
  {
    $this->authorize('viewAny', Attendance::class);

    $schedule = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'class.students' => function ($query) use ($request) {
        $query->select('id', 'name', 'nisn', 'user_id', 'class_id', 'parent_id');
        if ($request->user()->hasRole('parent')) {
          $query->where('parent_id', $request->user()->id);
        } elseif ($request->user()->hasRole('student')) {
          $query->where('user_id', $request->user()->id);
        }
      },
      'class.students.user' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id', 'semester', 'academic_year'),
      'subject' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'meetings' => function ($query) {
        $query->select('id', 'schedule_id', 'started_at', 'schedule_time_id', 'date')->orderBy('date', 'asc');
      },
      'meetings.attendances:id,status,user_id,meeting_id',
    ])
      ->withCount('meetings')
      ->filterByPermission($request->user())
      ->findOrFail($id);

    $meetings = $schedule->meetings;
    $students = $schedule->class->students;
    $attendances = [];

    foreach ($students as $student) {
      $studentAttendance = [];
      $totalAttendance = 0;
      $totalSick = 0;
      $totalPermission = 0;
      $totalAbsence = 0;
      foreach ($meetings as $meeting) {
        $attendance = $meeting->attendances->firstWhere('user_id', $student->user_id);
        $status = $attendance ? $attendance->status : null;
        $studentAttendance[] = $status;
        if ($status === 'H') {
          $totalAttendance++;
        }
        if ($status === 'S') {
          $totalSick++;
        }
        if ($status === 'I') {
          $totalPermission++;
        }
        if ($status === 'A') {
          $totalAbsence++;
        }
      }
      $attendances[] = [
        'student' => $student,
        'attendances' => $studentAttendance,
        'total_attendance' => $totalAttendance,
        'total_sick' => $totalSick,
        'total_permission' => $totalPermission,
        'total_absence' => $totalAbsence,
      ];
    }

    $data = [
      'subject' => $schedule->subject->name,
      'class' => '' . $schedule->class->name . ' ' . $schedule->class->level . ' ' . ($schedule->class->major?->name ?? ''),
      'teacher' => $schedule->teacher->name,
      'period' => '' . Helper::getSemesterLabel($schedule->period->semester) . ' TA ' . $schedule->period->academic_year,
      'attendances' => $attendances,
      'total_meetings' => $schedule->meetings_count,
    ];

    $pdf = Pdf::loadView('pdf.attendance', $data)
      ->setPaper('a4', 'landscape');

    $filename = "Absensi - "
      . (strtoupper($schedule->subject->name))
      . " "
      . ($schedule->period->semester == 'odd' ? 'Ganjil' : 'Genap')
      . " TA "
      . str_replace(['/', '\\'], '-', $schedule->period->academic_year)
      . " "
      . $schedule->subject->code
      . ".pdf";

    return $pdf->stream($filename);
  }

  public function edit(Request $request, $schedule_id, $meeting_id)
  {
    $meeting = Meeting::select([
      'id',
      'schedule_id',
      'schedule_time_id',
      'started_at',
      'title',
      'description',
      'meeting_method',
      'date',
      'type'
    ])
      ->with([
        'schedule:id,class_id,subject_id,teacher_id',
        'schedule.teacher:id,name,user_id',
        'schedule.teacher.user:id,image',
        'schedule.subject:id,name',
        'schedule.class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
        'schedule.class.major:id,name',
        'schedule.class.students:id,class_id,user_id,nisn,name',
        'attendances:id,meeting_id,user_id,status,edit_by,updated_at',
        'attendances.editby:id,name',
        'schedule_time:id,schedule_id,day,meeting_method,start_time,end_time',
      ])->withCount('attendances')->find($meeting_id);

    $this->authorize('view', $meeting);

    $meetings = $meeting->schedule->meetings()->get();
    $index = $meetings->search(function ($item) use ($meeting_id) {
      return $item->id == $meeting_id;
    });
    $meeting->meeting_at = $index + 1;

    $attendances = [];
    foreach ($meeting->schedule->class->students as $student) {
      $attendance = $meeting->attendances->firstWhere('user_id', $student->user_id);
      $status = $attendance ? $attendance->status : null;

      $attendances[] = [
        'student' => $student,
        'status' => $status,
        'editby' => $attendance?->editby,
        'updated_at' => $attendance?->updated_at,
      ];
    }

    $attendanceValue = ['H', 'A', 'I', 'S'];

    return view('user.attendance.edit', compact('meeting', 'attendances', 'attendanceValue'));
  }

  public function update(AttendanceStatusRequest $request, $meeting_id)
  {
    try {
      DB::beginTransaction();

      $meeting = Meeting::with('attendances')->findOrFail($meeting_id);
      $this->authorize('update', $meeting);

      $validated = $request->validated();
      foreach ($validated['attendances'] as $x) {
        $attendance = Attendance::where('meeting_id', $meeting_id)->where('user_id', $x['user_id'])->first();
        if ($attendance) {
          $attendance->update(['status' => $x['status'], 'edit_by' => $request->user()->id]);
        } else {
          $attendance = Attendance::create([
            'meeting_id' => $meeting_id,
            'user_id' => $x['user_id'],
            'status' => $x['status'],
            'edit_by' => $request->user()->id,
          ]);
        }

        activity()
          ->useLog('Kehadiran')
          ->performedOn($attendance)
          ->causedBy($request->user())
          ->withProperties(['status' => $attendance->status])
          ->log('Pengguna mengisi kehadiran');
      }

      DB::commit();

      return $this->sendResponse('Status kehadiran berhasil diperbarui.');
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function updateByMeeting(AttendanceStatusRequest $request, $meeting_id)
  {
    try {
      DB::beginTransaction();

      $meeting = Meeting::findOrFail($meeting_id);
      $this->authorize('update', $meeting);

      $today = Carbon::now();
      $isToday = $today->isSameDay(Carbon::parse($meeting->date));
      $isDuringSchedule = $isToday && $meeting->schedule_time->start_time <= now()
        && now() <= $meeting->schedule_time->end_time->addHours(2);

      if (!$isDuringSchedule) {
        return $this->sendError('Kehadiran hanya dapat diisi selama waktu pertemuan hingga 2 jam setelahnya.', [], 400);
      }

      $validated = $request->validated();
      foreach ($validated['attendances'] as $x) {
        $attendance = Attendance::where('meeting_id', $meeting_id)->where('user_id', $x['user_id'])->first();
        if ($attendance) {
          $attendance->update(['status' => $x['status'], 'edit_by' => $request->user()->id]);
        } else {
          $attendance = Attendance::create([
            'meeting_id' => $meeting_id,
            'user_id' => $x['user_id'],
            'status' => $x['status'],
            'edit_by' => $request->user()->id,
          ]);
        }

        activity()
          ->useLog('Kehadiran')
          ->performedOn($attendance)
          ->causedBy($request->user())
          ->withProperties(['status' => $attendance->status])
          ->log('Pengguna mengisi kehadiran');
      }

      DB::commit();

      return $this->sendResponse('Status kehadiran berhasil disimpan.');
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function classList(Request $request)
  {
    if (!$request->user()->hasRole('vice-principal')) abort(403);

    $activePeriod = Period::where('status', true)->first();
    $hasMajor = Major::count() > 0;
    if ($request->ajax()) {
      $data = SchoolClass::query();

      // Apply filters based on request and major availability
      if ($hasMajor) {
        $data->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
          ->select(['classes.id', 'classes.name', 'classes.level', 'majors.name as major_name']);

        if ($request->filled('major')) {
          $data->where('majors.name', $request->major);
        }
      } else {
        $data->select(['classes.id', 'classes.name', 'classes.level']);
      }

      if ($request->filled('level')) {
        $data->where('classes.level', $request->level);
      }

      if ($request->filled('class')) {
        $data->where('classes.name', $request->class);
      }

      // Eager load relationships with optimized queries
      $data->with([
        'schedules' => function ($q) use ($request, $activePeriod) {
          $q->select(['id', 'class_id']);
          if ($request->filled('periode')) {
            $q->where('schedules.period_id', $request->periode);
          } else {
            $q->where('schedules.period_id', $activePeriod->id ?? 0);
          }
        },
        'schedules.meetings' => function ($query) {
          $query->select(['id', 'schedule_id', 'type', 'date'])
            ->withCount([
              'attendances as present_count' => function ($query) {
                $query->where('status', 'H');
              }
            ]);
        },
      ])
        ->withCount('students');

      // Execute the query and process results
      $data = $data->get()->map(function ($class) {
        $totalStudents = $class->students_count;

        // Skip calculation if no students
        if ($totalStudents === 0) {
          $class->attendance_percentage = 0.0;
          return $class;
        }

        $totalSchedule = 0;
        $totalAttendancePercentage = 0.0;

        foreach ($class->schedules as $schedule) {
          $validMeetings = $schedule->meetings->filter(function ($meeting) {
            return $meeting->type != "Holiday";
          });

          if ($validMeetings->isEmpty()) {
            continue;
          }

          $totalSchedule++;

          $meetingPercentages = $validMeetings->map(function ($meeting) use ($totalStudents) {
            return round(($meeting->present_count / $totalStudents) * 100, 2);
          });

          $totalAttendancePercentage += $meetingPercentages->avg();
        }

        $class->attendance_percentage = $totalSchedule > 0
          ? round($totalAttendancePercentage / $totalSchedule, 2)
          : 0.0;

        return $class;
      });

      $dataTable = DataTables::of($data);

      if ($hasMajor) {
        $dataTable = $dataTable->addColumn('Jurusan', function ($row) {
          $html = '
          <span class="badge badge-light-primary">' . ($row->major_name) . '</span>
          ';
          return $html;
        });
      }

      $dataTable = $dataTable
        ->addColumn('Kelas', function ($row) {
          $html = '
          <span class="badge badge-light-primary">' . ($row->name) . '</span>
          ';
          return $html;
        })
        ->addColumn('Tingkat', function ($row) {
          $html = '
          <span class="badge badge-light-primary">' . ($row->level) . '</span>
          ';
          return $html;
        })
        ->addColumn('', function ($row) {
          $url = route('user.attendance.schedulebyclass', $row->id);
          return '
          <div>
          <a href="' . $url . '" class="badge badge-light-primary fs-6">' . $row->attendance_percentage . '%</a>
          </div>
          ';
        });

      $rawColumns = [];
      if ($hasMajor) {
        $rawColumns[] = 'Jurusan';
      }
      $rawColumns[] = 'Kelas';
      $rawColumns[] = 'Tingkat';
      $rawColumns[] = '';

      return $dataTable->rawColumns($rawColumns)->make(true);
    }
    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name')->get();

    $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();
    return view('user.attendance.class-list', compact('majors', 'classLevels', 'classNames', 'hasMajor', 'periods', 'activePeriod'));
  }

  public function scheduleByKelas(Request $request, $classId)
  {
    if (!$request->user()->hasRole('vice-principal')) abort(403);

    $class = SchoolClass::with('major:id,name')->findOrFail($classId);

    $activePeriod = Period::where('status', true)->first();
    if ($request->ajax()) {
      $data = Schedule::join('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->join('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->select([
          'schedules.id as id',
          'class_id',
          'subjects.name as subject_name',
          'teachers.name as teacher_name'
        ])
        ->where('schedules.class_id', $classId);

      // filter
      if ($request->filled('search')) {
        $search = $request->search['value'];
        $data->where('subjects.name', 'like', '%' . $search . '%');
      }

      if ($request->filled('guru')) {
        $data->where('teachers.name', 'like', '%' . $request->guru . '%');
      }

      if ($request->filled('mata_pelajaran')) {
        $data->where('subjects.name', 'like', '%' . $request->mata_pelajaran . '%');
      }

      if ($request->filled('periode')) {
        $data->where('schedules.period_id', $request->periode);
      } else {
        $data->where('schedules.period_id', $activePeriod->id ?? 0);
      }

      $data = $data->with([
        'meetings' => fn($query) =>  $query->select(['id', 'schedule_id', 'type', 'date'])->withCount([
          'attendances as present_count' => function ($query) {
            $query->where('status', 'H');
          }
        ]),
        'class' => function ($query) {
          $query->select(['id', 'name', 'level', 'major_id'])->withCount('students');
        },
      ]);

      $data = $data->get()->map(function ($schedule) use ($request) {
        $totalStudents = $schedule->class->students_count;

        $totalAttendancePercentage = 0.0;
        $totalMeetings = 0;
        foreach ($schedule->meetings as $meeting) {
          if ($meeting->type != "Holiday") {
            $totalMeetings++;
            $attendancePercentage = 0.0;
            if ($totalStudents > 0) {
              $attendancePercentage = round(($meeting->present_count / $totalStudents) * 100, 2);
            }
            $totalAttendancePercentage += $attendancePercentage;
          }
        }

        if ($totalMeetings) {
          $schedule->attendance_percentage =  round(($totalAttendancePercentage / $totalMeetings), 2);
        } else {
          $schedule->attendance_percentage = 0.0;
        }

        return $schedule;
      });

      return DataTables::of($data)
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
        <div class="product-names">
        <p>' . $row->subject_name . '</p>
        </div>
        ';
          return $html;
        })
        ->addColumn('Pengajar', function ($row) {
          $html = '
        <div class="product-names">
        <p>' . $row->teacher_name . '</p>
        </div>
        ';
          return $html;
        })
        ->addColumn('Rekap', function ($row) {
          $url = route('user.attendance.showAttendancRecap', [
            'id' => $row->id,
          ]);
          return '
        <div>
        <a href="' . $url . '" class="badge badge-light-primary fs-6">' . $row->attendance_percentage . '%</a>
        </div>
        ';
        })
        ->addColumn('', function ($row) {
          $url = route('user.attendance.meetingBySchedule', [
            'schedule_id' => $row->id,
          ]);
          return '
        <div>
        <a href="' . $url . '" class="badge badge-light-info">Lihat Pertemuan</a>
        </div>
        ';
        })
        ->rawColumns(['Mata Pelajaran', 'Pengajar', 'Rekap', ''])->make(true);
    }

    $teachers = Teacher::select('id', 'name')->get();
    $subjects = Subject::select('id', 'name')->get();
    $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();

    return view('user.attendance.schedule-by-class', compact('class', 'teachers', 'subjects', 'periods', 'activePeriod'));
  }

  public function showMeeting($meeting_id)
  {

    $meeting = Meeting::select([
      'id',
      'schedule_id',
      'schedule_time_id',
      'started_at',
      'date',
      'title',
      'description',
      'meeting_method',
      'type'
    ])
      ->with([
        'schedule:id,class_id,subject_id,teacher_id',
        'schedule.teacher:id,name,user_id',
        'schedule.teacher.user:id,image',
        'schedule.subject:id,name',
        'schedule.class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
        'schedule.class.major:id,name',
        'schedule_time'
      ])->withCount('attendances')->find($meeting_id);

    $this->authorize('view', $meeting);

    $meetings = $meeting->schedule->meetings()->get();
    $index = $meetings->search(function ($item) use ($meeting_id) {
      return $item->id == $meeting_id;
    });
    $meeting->meeting_at = $index + 1;

    return $this->sendResponse('Berhasil mengambil data', $meeting);
  }

  private function calculateAttendancePercentage($schedule, $request)
  {
    // Load relationships yang diperlukan
    $schedule->load([
      'class.students',
      'meetings' => function ($query) {
        $query->where('type', '!=', 'Holiday');
      },
      'meetings.attendances' => function ($query) use ($request) {
        $query->where('status', 'H');
        if ($request->user()->hasRole('student')) {
          $query->where('user_id', $request->user()->id);
        } else if ($request->user()->hasRole('parent')) {
          $query->where('user_id', $request->user()->parent->user->id);
        }
      }
    ]);

    $totalStudents = $schedule->class->students->count();
    if ($request->user()->hasRole('student') || $request->user()->hasRole('parent')) {
      $totalStudents = 1;
    }

    $totalAttendancePercentage = 0;
    $totalMeetings = 0;

    foreach ($schedule->meetings as $meeting) {
      $totalMeetings++;
      $presentCount = $meeting->attendances->count();
      $attendancePercentage = $totalStudents > 0 ?
        round(($presentCount / $totalStudents) * 100, 1) : 0;
      $totalAttendancePercentage += $attendancePercentage;
    }

    return $totalMeetings > 0 ?
      round($totalAttendancePercentage / $totalMeetings, 1) : 0;
  }
}
