<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Schedule\ScheduleRequest;
use App\Models\Curriculum;
use App\Models\Major;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Queue;
use App\Jobs\CreateScheduleMeetings;
use App\Jobs\UpdateScheduleMeetings;
use App\Models\Meeting;
use App\Models\ScheduleTime;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
  public function index(Request $request)
  {
    $activePeriod = Period::where('status', true)->first();

    $this->authorize('viewPossession', Schedule::class);

    $schedules = Schedule::query()
      ->select('schedules.*')
      ->addSelect([
        'first_day' => ScheduleTime::select('day')
          ->whereColumn('schedule_id', 'schedules.id')
          ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
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
      ->filterByPermission($request->user())
      ->mainFilter($request->all())
      ->orderByRaw("FIELD(first_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
      ->orderBy('first_start_time')
      ->get();

    $activePeriod = Period::where('status', true)->first();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name', 'asc')->get();
    $majors = Major::with(['classes' => function ($query) {
      $query->select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc');
    }])->select('id', 'name')->orderBy('name', 'asc')->get();
    $classes = SchoolClass::select('id', 'name', 'level', 'major_id')->orderBy('name', 'asc')->get();
    $hasMajors = Major::count() > 0;
    $subjects = Subject::select('id', 'name', 'curriculum_id')->with(['curriculum:id,name'])->get();
    $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();
    $days = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat'
    ];

    return view('user.schedule.index', compact('schedules', 'days', 'classes', 'classLevels', 'classNames', 'majors', 'hasMajors', 'subjects', 'periods', 'activePeriod'));
  }

  public function classList(Request $request)
  {
    if (!$request->user()->can('schedule.*')) {
      return abort(403);
    }

    if ($request->ajax()) {
      $activePeriod = Period::where('status', true)->first();
      $majorsExist = Major::count() > 0;

      $data = SchoolClass::query();
      if ($majorsExist) {
        $data->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
          ->select([
            'classes.id',
            'classes.name',
            'classes.level',
            'majors.name as major_name',
          ]);
      } else {
        $data->select([
          'classes.id',
          'classes.name',
          'classes.level',
        ]);
      }
      $data->filterSchedule($request->all())
        ->withCount(['schedules as schedule_times_count' => function ($q) use ($activePeriod) {
          $q->select(DB::raw('COUNT(schedule_times.id)'))
            ->join('schedule_times', 'schedules.id', '=', 'schedule_times.schedule_id')
            ->where('schedules.period_id', $activePeriod->id);
        }]);

      $dataTable = DataTables::of($data);

      if ($majorsExist) {
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
        ->addColumn('Jumlah Jadwal', function ($row) {
          $html = '
              <span class="badge badge-light-primary">' . ($row->schedule_times_count ?? 0) . '</span>
          ';
          return $html;
        })
        ->addColumn('Aksi', function ($row) {
          $url = route('user.schedule.byclass', $row->id);
          return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat </a>';
        });

      // Tentukan kolom mana saja yang perlu di-raw
      $rawColumns = ['Kelas', 'Tingkat', 'Jumlah Jadwal', 'Aksi'];
      if ($majorsExist) {
        $rawColumns[] = 'Jurusan';
      }

      return $dataTable
        ->rawColumns($rawColumns)
        ->make(true);
    }

    $classes = SchoolClass::with('major')->orderBy('level')->orderBy('name')->get();
    $majors = Major::select('id', 'name')->orderBy('name', 'asc')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level', 'asc')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name', 'asc')->get();
    return view('user.schedule.class-list', [
      'classes' => $classes,
      'majors' => $majors,
      'classLevels' => $classLevels,
      'classNames' => $classNames
    ]);
  }

  public function viewByClass(Request $request, $classId)
  {
    if (!$request->user()->can('schedule.*')) {
      return abort(403);
    }

    $class = SchoolClass::with(['major' => fn($query) => $query->select('id', 'name')])->find($classId);

    $activePeriod = Period::where('status', true)->first();

    if ($request->ajax()) {
      $data = Schedule::query()
        ->leftJoin('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->leftJoin('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->leftJoin('rooms', 'schedules.room_id', '=', 'rooms.id')
        ->leftJoin('schedule_times', 'schedule_times.schedule_id', '=', 'schedules.id')
        ->select([
          'schedules.id',
          'subjects.name as subject_name',
          'teachers.name as teacher_name',
          'rooms.name as room_name',
          'schedule_times.id as schedule_time_id',
          'schedule_times.day as day',
          'schedule_times.start_time as start_time',
          'schedule_times.end_time as end_time',
        ])
        ->filter($request->all())
        ->where('class_id', $classId)
        ->orderBy('schedule_times.day', 'asc')
        ->orderBy('schedule_times.start_time', 'asc');

      if ($request->filled('periode')) {
        $data->where('schedules.period_id', $request->periode);
      } else {
        $data->where('schedules.period_id', $activePeriod->id ?? 0);
      }

      $data->get();

      return Datatables::of($data)
        ->filterColumn('subject_name', function ($query, $keyword) {
          $query->where('subjects.name', 'like', "%{$keyword}%");
        })
        ->addColumn('id', function ($row) {
          $html = '
              <div class="checkbox-checked">
                  <div class="form-check d-flex justify-content-center align-items-center">
                  <input class="form-check-input select-row" type="checkbox"
                          style="width: 12px; height: 12px;" value="' . $row->schedule_time_id . '" name="selected_ids[]" id="select-row-' . $row->id . '">
                  </div>
              </div>
              ';
          return $html;
        })
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
                    <div class="product-names">
                    <p>' . $row->subject_name . '</p>
                    </div>
                    ';
          return $html;
        })
        ->addColumn('Guru', function ($row) {
          $html = '
                    <p class="f-light">' . $row->teacher_name . '</p>
                    ';
          return $html;
        })
        ->addColumn('Ruangan', function ($row) {
          $html = '
                    <p class="f-light">' . $row->room_name . '</p>
                    ';
          return $html;
        })
        ->addColumn('Hari', function ($row) {
          $html = '
                    <span class="badge badge-light-primary">' . (Helper::getDayName($row->day)) . '</span>
                    ';
          return $html;
        })
        ->addColumn('Jam', function ($row) {
          $html = '
                        <span class="badge badge-light-primary">' . (Carbon::parse($row->start_time)->format('H:i') . ' - ' . Carbon::parse($row->end_time)->format('H:i')) . '</span>
                    ';
          return $html;
        })
        ->addColumn('Aksi', function ($row) {
          $html = '
                        <div class="common-align gap-2 justify-content-start">
                            <a class="square-white view" data-id="' . $row->id . '" data-schedule-time-id="' . $row->schedule_time_id . '" style="cursor: pointer;"><svg>
                                <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                                </use>
                            </svg>
                            </a>
                            <a class="square-white edit"  data-id="' . $row->id . '" data-schedule-time-id="' . $row->schedule_time_id . '"style="cursor: pointer;">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                            <a class="square-white trash"  data-schedule-time-id="' . $row->schedule_time_id . '" style="cursor: pointer;">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#trash1') . '"></use></svg>
                            </a>
                        </div>';
          return $html;
        })
        ->rawColumns(['id', 'Mata Pelajaran', 'Guru', 'Ruangan', 'Hari', 'Jam', 'Aksi'])
        ->make(true);
    } else {
      $activeCurriculum = Curriculum::with(['subjects' => fn($query) => $query->select('id', 'name', 'curriculum_id')])->select('id', 'name')->where('status', true)->get();
      $teachers = Teacher::select('id', 'name', 'specialization')->orderBy('name', 'asc')->get();
      $rooms = Room::select('id', 'name')->orderBy('name', 'asc')->get();
      $hasMajors = Major::count() > 0;
      $meetingMethods = [['value' => 'Offline', 'label' => 'Luring'], ['value' => 'Online', 'label' => 'Daring'], ['value' => 'Hybrid', 'label' => 'Campuran']];
      $days = [['value' => 'Monday', 'label' => 'Senin'], ['value' => 'Tuesday', 'label' => 'Selasa'], ['value' => 'Wednesday', 'label' => 'Rabu'], ['value' => 'Thursday', 'label' => 'Kamis'], ['value' => 'Friday', 'label' => 'Jumat']];

      return view('user.schedule.by-class', [
        'class' => $class,
        'teachers' => $teachers,
        'rooms' => $rooms,
        'hasMajors' => $hasMajors,
        'meetingMethods' => $meetingMethods,
        'activeCurriculum' => $activeCurriculum,
        'days' => $days
      ]);
    }
  }

  public function showBySchedule($id)
  {
    $schedule = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'class.students:class_id,user_id,name,nis',
      'class.students.user:id,image,username',
      'subject' => fn($query) => $query->select('id', 'name', 'code'),
      'teacher' => fn($query) => $query->select('id', 'name', 'user_id'),
      'teacher.user:id,image,username',
      'room' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id', 'academic_year', 'semester'),
      'schedule_times',
      'meetings' => fn($query) => $query->select('id', 'schedule_id', 'schedule_time_id', 'started_at', 'type', 'date'),
      'meetings.schedule_time:id,meeting_method,start_time,end_time'
    ])->find($id);

    $this->authorize('view', $schedule);

    return view('user.schedule.show', compact('schedule'));
  }
  public function showByMeeting($id, $meeting_id)
  {
    $meeting = Meeting::with([
      'schedule' => fn($query) => $query->select('id', 'class_id', 'subject_id', 'teacher_id', 'room_id', 'period_id'),
      'schedule.class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'schedule.class.students:class_id,user_id,name,nis',
      'schedule.class.major' => fn($query) => $query->select('id', 'name'),
      'schedule.subject' => fn($query) => $query->select('id', 'name', 'code'),
      'schedule.teacher' => fn($query) => $query->select('id', 'name', 'user_id'),
      'schedule.teacher.user:id,image',
      'schedule.room' => fn($query) => $query->select('id', 'name'),
      'schedule.period' => fn($query) => $query->select('id', 'academic_year', 'semester'),
      'schedule_time',
      'schedule.meetings' => fn($query) => $query->select('id', 'schedule_id', 'schedule_time_id', 'started_at', 'type', 'date'),
      'schedule.meetings.schedule_time:id,meeting_method,start_time,end_time',
      'teaching_journal',
      'attendances:id,meeting_id,user_id,status',
      'materials',
      'meeting_texts',
      'tasks' => fn($query) => $query->withCount([
        'submissions as not_yet_rated' => function ($query) {
          $query->whereNull('score');
        }
      ]),
    ])->findOrFail($meeting_id);

    $this->authorize('viewPossession', $meeting);

    $schedule = $meeting->schedule;

    $meetings = $meeting->schedule->meetings;
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
      ];
    }

    $attendanceValue = ['H', 'A', 'I', 'S'];

    $rooms = Room::select('id', 'name')->orderBy('name', 'asc')->get();
    $meetingMethods = [
      ['value' => 'Offline', 'label' => 'Luring'],
      ['value' => 'Online', 'label' => 'Daring'],
      ['value' => 'Hybrid', 'label' => 'Campuran']
    ];
    $meetingTypes = [
      ['value' => 'Learning', 'label' => 'Belajar'],
      ['value' => 'Midterm', 'label' => 'UTS'],
      ['value' => 'Final', 'label' => 'UAS'],
      ['value' => 'Holiday', 'label' => 'Libur'],
    ];
    $materialType = [
      ['value' => 'eBook', 'label' => 'eBook'],
      ['value' => 'Archive', 'label' => 'Arsip'],
      ['value' => 'Link', 'label' => 'Link']
    ];
    $taskType = [
      ['value' => 'individual', 'label' => 'Individu'],
      ['value' => 'group', 'label' => 'Kelompok'],
    ];

    $today = Carbon::now();
    $scheduleTime = $meeting->schedule_time;

    $isToday = $today->isSameDay(Carbon::parse($meeting->date));
    $isStartedAt = !$meeting->started_at && $isToday && $scheduleTime->start_time <= $today && $today <= $scheduleTime->end_time;
    $isRealization = $meeting->started_at && $isToday && $scheduleTime->start_time <= $today && $today <= $scheduleTime->end_time->addHours(2);

    $attendancePercentage = 0.0;
    $totalStudents = $meeting->schedule->class->students->count();
    $totalAttendances = $meeting->attendances->where('status', 'H')->count();

    if ($totalStudents > 0) {
      $attendancePercentage = round(($totalAttendances / $totalStudents) * 100, 2);
    }

    $contents = collect()
      ->merge($meeting->materials->map(function ($item) {
        $item->data_type = 'material';
        return $item;
      }))
      ->merge($meeting->meeting_texts->map(function ($item) {
        $item->data_type = 'meeting_text';
        return $item;
      }))
      ->merge($meeting->tasks->map(function ($item) {
        $item->data_type = 'task';
        return $item;
      }))
      ->sortByDesc('created_at')
      ->values();

    return view('user.schedule.meeting.show', compact('meeting', 'schedule', 'meetingTypes', 'rooms', 'meetingMethods', 'attendances', 'attendanceValue', 'isStartedAt', 'isRealization', 'attendancePercentage', 'materialType', 'taskType', 'contents'));
  }

  public function show($id)
  {

    $schedule = Schedule::with([
      'subject.curriculum' => fn($query) => $query->select('id', 'name'),
      'subject.curriculum.subjects' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'subject' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'room' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
    ])->findOrFail($id);

    $this->authorize('view', $schedule);

    return $this->sendResponse('Data jadwal ditemukan', $schedule);
  }

  public function edit($id, $schedule_time_id)
  {
    $this->authorize('update', Schedule::class);

    $schedule = Schedule::with([
      'subject.curriculum' => fn($query) => $query->select('id', 'name'),
      'subject.curriculum.subjects' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'subject' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'room' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'schedule_times' => fn($query) => $query->select('schedule_id', 'day', 'start_time', 'end_time', 'meeting_method')->where('id', $schedule_time_id)->limit(1),
      'class.major:id,name',
    ])->findOrFail($id);

    return $this->sendResponse('Data jadwal ditemukan', $schedule);
  }

  public function store(ScheduleRequest $formRequest)
  {
    try {
      DB::beginTransaction();

      $this->authorize('create', Schedule::class);

      $activePeriod = Period::where('status', true)->first();
      if (!$activePeriod) {
        return $this->sendError('Tidak ada periode aktif.', [], 400);
      }

      $scheduleCollide = Schedule::where('class_id', $formRequest->class_id)
        ->where('period_id', $activePeriod->id)
        ->whereHas('schedule_times', function ($query) use ($formRequest) {
          $query->where('day', $formRequest->day)
            ->where(function ($q) use ($formRequest) {
              $q->where(function ($q2) use ($formRequest) {
                $q2->where('start_time', '<', $formRequest->end_time)
                  ->where('end_time', '>', $formRequest->start_time);
              });
            });
        })
        ->exists();

      if ($scheduleCollide) {
        return $this->sendError('Jadwal sudah ada atau bentrok dengan jadwal lain.', [], 400);
      }

      $validated = $formRequest->validated();

      $validated['period_id'] = $activePeriod->id;

      $scheduleExist = Schedule::where('subject_id', $validated['subject_id'])
        ->where('teacher_id', $validated['teacher_id'])
        ->where('period_id', $activePeriod->id)
        ->where('class_id', $validated['class_id'])
        ->first();

      if ($scheduleExist) {
        $schedule = $scheduleExist;
      } else {
        $schedule = Schedule::create($validated);
      }

      $schedule_time = $schedule->schedule_times()->create($validated);

      Queue::push(new CreateScheduleMeetings($schedule, $schedule_time));

      foreach ($schedule->class->students as $student) {
        if ($student->schedules) {
          $student->schedules()->update([
            'schedule_ids' => array_merge([$schedule->id], optional($student->schedules)->schedule_ids ?? [])
          ]);
        } else {
          $student->schedules()->create([
            'student_id' => $student->id,
            'schedule_ids' => [$schedule->id]
          ]);
        }
      }

      DB::commit();

      return $this->sendResponse('Jadwal berhasil ditambahkan.', $schedule, 201);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function update(ScheduleRequest $formRequest, $id, $schedule_time_id)
  {
    try {
      $this->authorize('update', Schedule::class);

      $activePeriod = Period::where('status', true)->first();
      if (!$activePeriod) {
        return $this->sendError('Tidak ada periode aktif.', [], 400);
      }

      $schedule = Schedule::with(['schedule_times'])->findOrFail($id);
      // Simpan data lama untuk perbandingan
      $oldScheduleData = [
        'day' => $schedule->schedule_times[0]->day,
        'start_time' => $schedule->schedule_times[0]->start_time,
        'end_time' => $schedule->schedule_times[0]->end_time,
        'meeting_method' => $schedule->schedule_times[0]->meeting_method,
        'class_id' => $schedule->class_id,
        'subject_id' => $schedule->subject_id,
        'teacher_id' => $schedule->teacher_id,
        'room_id' => $schedule->room_id,
      ];

      $scheduleCollide = Schedule::where('id', '!=', $id)->where('class_id', $formRequest->class_id)
        ->where('period_id', $activePeriod->id)
        ->whereHas('schedule_times', function ($query) use ($formRequest) {
          $query->where('day', $formRequest->day)
            ->where(function ($q) use ($formRequest) {
              $q->where(function ($q2) use ($formRequest) {
                $q2->where('start_time', '<', $formRequest->end_time)
                  ->where('end_time', '>', $formRequest->start_time);
              });
            });
        })
        ->exists();

      if ($scheduleCollide) {
        return $this->sendError('Jadwal sudah ada atau bentrok dengan jadwal lain.', [], 400);
      }

      $validated = $formRequest->validated();
      $validated['period_id'] = $activePeriod->id;

      $scheduleExist = Schedule::where('subject_id', $validated['subject_id'])
        ->where('teacher_id', $validated['teacher_id'])
        ->where('class_id', $validated['class_id'])
        ->where('period_id', $activePeriod->id)
        ->first();

      if ($scheduleExist) {
        $schedule = $scheduleExist;
      }


      DB::beginTransaction();

      $schedule->update([
        'class_id' => $validated['class_id'],
        'subject_id' => $validated['subject_id'],
        'teacher_id' => $validated['teacher_id'],
        'room_id' => $validated['room_id'],
        'period_id' => $validated['period_id'],
      ]);

      $schedule_time = $schedule->schedule_times()
        ->where('id', $schedule_time_id)
        ->firstOrFail();

      $schedule_time->update([
        'day' => $validated['day'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'meeting_method' => $validated['meeting_method'],
      ]);

      $meetingAffectingChanges = $this->hasMeetingAffectingChanges($oldScheduleData, $validated, $schedule_time);
      if ($meetingAffectingChanges) {
        Queue::push(new UpdateScheduleMeetings($schedule, $schedule_time));
      }

      Log::info($schedule->class->students);
      foreach ($schedule->class->students as $student) {
        if ($student->schedules) {
          $mergedIds = array_unique(array_merge([$schedule->id], optional($student->schedules)->schedule_ids ?? []));
          $student->schedules()->update([
            'schedule_ids' => array_values($mergedIds)
          ]);
        } else {
          $student->schedules()->create([
            'student_id' => $student->id,
            'schedule_ids' => [$schedule->id]
          ]);
        }
      }

      DB::commit();

      return $this->sendResponse('Jadwal berhasil diedit.', $schedule);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error("Error updating schedule: " . $e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  /**
   * Cek apakah ada perubahan yang mempengaruhi meeting
   */
  private function hasMeetingAffectingChanges($oldData, $newData, $newDataScheduleTime)
  {
    // Pastikan semua data berupa array
    $newData = is_string($newData) ? json_decode($newData, true) : (array) $newData;
    $newDataScheduleTime = is_string($newDataScheduleTime) ? json_decode($newDataScheduleTime, true) : (array) $newDataScheduleTime;
    $oldData = is_string($oldData) ? json_decode($oldData, true) : (array) $oldData;

    $meetingAffectingFields = ['day', 'start_time', 'end_time', 'meeting_method'];

    foreach ($meetingAffectingFields as $field) {
      if (isset($oldData[$field])) {

        // Ambil nilainya
        $oldVal = $oldData[$field];
        $newVal = $newData[$field] ?? null;
        $newScheduleVal = $newDataScheduleTime[$field] ?? null;

        // --- NORMALISASI WAKTU (Object Carbon ke String 'H:i') ---
        // Jika field adalah start_time / end_time dan datanya berupa Object Tanggal
        if (in_array($field, ['start_time', 'end_time'])) {
          if ($oldVal instanceof \DateTimeInterface) {
            $oldVal = $oldVal->format('H:i');
          }
          if ($newVal instanceof \DateTimeInterface) {
            $newVal = $newVal->format('H:i');
          }
          if ($newScheduleVal instanceof \DateTimeInterface) {
            $newScheduleVal = $newScheduleVal->format('H:i');
          }
        }

        // --- CEK PERUBAHAN ---
        // Bandingkan nilainya. Jika newVal ada (tidak null) dan berbeda dengan oldVal
        $isChangedInNewData = $newVal !== null && $newVal !== $oldVal;
        $isChangedInSchedule = $newScheduleVal !== null && $newScheduleVal !== $oldVal;

        if ($isChangedInNewData || $isChangedInSchedule) {
          // Kamu bisa menambahkan Log::info("Berubah di field: $field", ['old' => $oldVal, 'new' => $newVal]) disini untuk debugging
          return true;
        }
      }
    }

    return false;
  }

  public function destroy(Request $request, $schedule_time_id)
  {
    try {
      DB::beginTransaction();

      $this->authorize('delete', Schedule::class);

      $scheduleTime = $scheduleTime = ScheduleTime::find($schedule_time_id);

      $schedule = $scheduleTime->schedule;
      if ($schedule->schedule_times()->count() > 1) {
        $scheduleTime->delete();
      } else {
        $schedule->delete();
      }

      foreach ($schedule->class->students as $student) {
        if ($student->schedules) {
          $updatedIds = array_filter(optional($student->schedules)->schedule_ids ?? [], function ($id) use ($schedule) {
            return $id !== $schedule->id;
          });
          $student->schedules()->update([
            'schedule_ids' => array_values($updatedIds)
          ]);
        }
      }

      DB::commit();

      return $this->sendResponse('Jadwal berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function bulkDestroy(Request $request)
  {
    try {
      DB::beginTransaction();

      $this->authorize('delete', Schedule::class);

      $scheduleTimeIds = $request->input('ids', []);

      if (empty($scheduleTimeIds)) {
        return $this->sendError('Tidak ada jadwal yang dipilih.', [], 422);
      }

      if (!empty($scheduleTimeIds)) {
        $deletedScheduleTime = 0;
        foreach ($scheduleTimeIds as $scheduleTimeId) {
          $scheduleTime = ScheduleTime::find($scheduleTimeId);
          if ($scheduleTime) {
            $schedule = $scheduleTime->schedule;

            foreach ($schedule->class->students as $student) {
              if ($student->schedules) {
                $updatedIds = array_filter(optional($student->schedules)->schedule_ids ?? [], function ($id) use ($schedule) {
                  return $id !== $schedule->id;
                });
                $student->schedules()->update([
                  'schedule_ids' => array_values($updatedIds)
                ]);
              }
            }

            if ($schedule->schedule_times()->count() > 1) {
              $scheduleTime->delete();
              $deletedScheduleTime++;
            } else {
              $schedule->delete();
            }
          }
        }
      }

      DB::commit();

      return $this->sendResponse('Jadwal berhasil dihapus.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }
}
