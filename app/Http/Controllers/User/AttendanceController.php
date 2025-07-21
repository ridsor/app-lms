<?php

namespace App\Http\Controllers\User;

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
      $data = Schedule::join('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->join('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->select([
          'schedules.id as id',
          'class_id',
          'subjects.name as subject_name',
          'teachers.name as teacher_name'
        ])
        ->with(['class:id,name,level,major_id', 'class.major:id,name'])
        ->where('schedules.period_id', $activePeriod->id ?? 0);

      $data
        ->filterByPermission($request->user());

      if ($request->filled('search')) {
        $search = $request->search['value'];
        $data->where(function ($q) use ($search) {
          $q->where('subjects.name', 'like', '%' . $search . '%');
        });
      }

      if ($request->filled('major')) {
        $data->whereHas('class.major', function ($q) use ($request) {
          $q->whereFullText('majors.name', $request->major);
        });
      }

      if ($request->filled('level')) {
        $data->whereHas('class', function ($q) use ($request) {
          $q->where('level', $request->level);
        });
      }

      if ($request->filled('class')) {
        $data->whereHas('class', function ($q) use ($request) {
          $q->where('name', $request->class);
        });
      }

      $data = $data->get();

      $dataTable = DataTables::of($data);

      if ($request->user()->hasRole('student') || $request->user()->hasRole('parent')) {
        $dataTable->addColumn('Guru', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . ($row->teacher_name) . '</p>
          </div>
          ';
          return $html;
        });
      } else if ($request->user()->has('teacher')) {
        $dataTable->addColumn('Kelas', function ($row) {
          $html = '
            <span class="badge badge-light-primary">' . ($row->class->name) . ($row->class->level) . ($row->class->major ? ' ' . $row->class->major->name : '') . '</span>
            ';
          return $html;
        });
      }

      $dataTable
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . ($row->subject_name) . '</p>
          </div>
          ';
          return $html;
        })
        ->addColumn('Rekap', function ($row) {
          $url = route('user.attendance.showAttendancRecap', [
            'id' => $row->id,
          ]);
          return '<a href="' . $url . '" class="badge badge-light-info">Lihat</a> ';
        });

      $rawColumns = ['Mata Pelajaran', 'Rekap'];
      if ($request->user()->hasRole('student') || $request->user()->hasRole('parent')) {
        $rawColumns[] = 'Guru';
      } else if ($request->user()->hasRole('teacher')) {
        $rawColumns[] = 'Kelas';
      }

      return $dataTable
        ->rawColumns($rawColumns)
        ->make(true);
    }
    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name')->get();
    return view('user.attendance.index', compact('majors', 'classLevels', 'classNames'));
  }

  public function showAttendancRecap(Request $request, $id)
  {
    $this->authorize('viewAny', Attendance::class);

    $activePeriod = Period::where('status', true)->first();
    $schedule = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'class.students' => function ($query) use ($request) {
        $query->select('id', 'name', 'nisn', 'user_id', 'class_id');
        // if ($request->user()->hasRole('student')) {
        //   $query->where('user_id', $request->user()->id);
        // }
      },
      'class.students.user' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id', 'semester', 'academic_year'),
      'subject' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'meetings' => function ($query) {
        $query->select('id', 'schedule_id', 'started_at', 'schedule_time_id');
      },
      'meetings.attendances:id,status,user_id,meeting_id',
    ])->filterByPermission($request->user())
      ->where('period_id', $activePeriod->id ?? 0)
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
        'attendances.editby:id,username',
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

      $meeting = Meeting::findOrFail($meeting_id);
      $this->authorize('update', $meeting);

      $validated = $request->validated();
      foreach ($validated['attendances'] as $x) {
        $attendance = Attendance::where('meeting_id', $meeting_id)->where('user_id', $x['user_id'])->first();
        if ($attendance) {
          $attendance->update(['status' => $x['status'], 'edit_by' => $request->user()->id]);
        } else {
          Attendance::create([
            'meeting_id' => $meeting_id,
            'user_id' => $x['user_id'],
            'status' => $x['status'],
            'edit_by' => $request->user()->id,
          ]);
        }
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

      $isDuringSchedule = $meeting->schedule_time->start_time <= now()
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
          Attendance::create([
            'meeting_id' => $meeting_id,
            'user_id' => $x['user_id'],
            'status' => $x['status'],
            'edit_by' => $request->user()->id,
          ]);
        }
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

    $hasMajor = Major::count() > 0;
    if ($request->ajax()) {
      $data = SchoolClass::query();
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
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.schedulebyclass', $row->id);
          return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat</a>';
        });

      $rawColumns = [];
      if ($hasMajor) {
        $rawColumns[] = 'Jurusan';
      }
      $rawColumns[] = 'Kelas';
      $rawColumns[] = 'Tingkat';
      $rawColumns[] = 'Aksi';

      return $dataTable->rawColumns($rawColumns)->make(true);
    }
    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name')->get();
    return view('user.attendance.class-list', compact('majors', 'classLevels', 'classNames', 'hasMajor'));
  }

  public function scheduleByKelas(Request $request, $classId)
  {
    if (!$request->user()->hasRole('vice-principal')) abort(403);

    $activePeriod = Period::where('status', true)->first();
    $class = SchoolClass::with('major:id,name')->findOrFail($classId);

    if ($request->ajax()) {
      $data = Schedule::join('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->join('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->select([
          'schedules.id as id',
          'subjects.name as subject_name',
          'teachers.name as teacher_name'
        ])
        ->where('schedules.class_id', $classId)
        ->where('schedules.period_id', $activePeriod->id ?? 0);

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

      $data = $data->get();

      return DataTables::of($data)
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . $row->subject_name . '</p>
          </div>
          ';
          return $html;
        })->addColumn('Pengajar', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . $row->teacher_name . '</p>
          </div>
          ';
          return $html;
        })
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.showAttendancRecap', [
            'id' => $row->id,
          ]);
          return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat </a>';
        })
        ->rawColumns(['Mata Pelajaran', 'Pengajar', 'Aksi'])->make(true);
    }

    $teachers = Teacher::select('id', 'name')->get();
    $subjects = Subject::select('id', 'name')->get();
    return view('user.attendance.schedule-by-class', compact('class', 'teachers', 'subjects'));
  }

  public function showMeeting(Request $request, $meeting_id, $schedule_time_id)
  {
    $this->authorize('viewAny', Attendance::class);

    $meeting = Meeting::select([
      'id',
      'schedule_id',
      'schedule_time_id',
      'started_at',
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


    $meetings = $meeting->schedule->meetings()->get();
    $index = $meetings->search(function ($item) use ($meeting_id) {
      return $item->id == $meeting_id;
    });
    $meeting->meeting_at = $index + 1;

    return $this->sendResponse('Berhasil mengambil data', $meeting);
  }
}
