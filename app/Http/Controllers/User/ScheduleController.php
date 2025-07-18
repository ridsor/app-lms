<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Period;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Schedule\ScheduleRequest;
use App\Models\Curriculum;
use App\Models\Major;
use App\Models\Meeting;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Queue;
use App\Jobs\CreateScheduleMeetings;
use App\Jobs\UpdateScheduleMeetings;

class ScheduleController extends Controller
{
  public function index(Request $request)
  {
    $activePeriod = Period::where('status', true)->first();

    $this->authorize('viewPossession', Schedule::class);

    $schedules = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'subject' => fn($query) => $query->select('id', 'name', 'code'),
      'teacher' => fn($query) => $query->select('id', 'name', 'user_id')->with('user:id,image'),
      'meetings' => fn($query) => $query->select('id', 'schedule_id', 'date')->where('date', '>=', now()->format('Y-m-d')),
    ])->orderBy('day')
      ->orderBy('start_time')
      ->where('period_id', $activePeriod->id ?? 0)
      ->filterByPermission($request->user());

    $schedules = $schedules->get()->groupBy(function ($item) {
      return $item->grouping_schedule;
    })->map(function ($group) {
      $first = $group->first();
      $days_time = $group->map(function ($x) {
        $meeting = $x->meetings->first();
        if ($meeting) {
          $meeting_await = $meeting->getRawOriginal('date') . ' ' . $x->start_time;
        }
        return [
          'meeting_await' => $meeting_await,
          'day' => $x->day,
          'start_time' => $x->start_time,
          'end_time' => $x->end_time,
        ];
      })->values();

      $now = now();

      $meeting_await = null;
      $closest_difference = null;
      foreach ($days_time as $item) {
        if ($item['meeting_await']) {
          $temp = Carbon::parse($item['meeting_await']);
          $difference = abs($temp->diffInSeconds($now, false));
          if (is_null($closest_difference) || $difference >= $closest_difference) {
            $closest_difference = $difference;
            $meeting_await = $temp;
          }
        }
      }

      $first->meeting_await = $meeting_await ? $meeting_await : null;
      $first->days_time = $days_time;

      return $first;
    })->values();

    $schedules = $schedules->sortBy(function ($item) {
      return $item->meeting_await ? $item->meeting_await->timestamp : PHP_INT_MAX;
    })->values();


    return view('user.schedule.index', compact('schedules'));
  }

  public function classList(Request $request)
  {
    if (!$request->user()->can('schedule.*')) {
      return abort(403);
    }

    if ($request->ajax()) {
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
      $data->filterSchedule($request->all())->withCount('schedules');

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
              <span class="badge badge-light-primary">' . ($row->schedules_count ?? 0) . '</span>
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
        ->select([
          'schedules.id',
          'subjects.name as subject_name',
          'teachers.name as teacher_name',
          'rooms.name as room_name',
          'schedules.day',
          'schedules.start_time',
          'schedules.end_time',
        ])
        ->filter($request->all())
        ->where('class_id', $classId)
        ->where('period_id', $activePeriod->id ?? 0)
        ->orderBy('day', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

      return Datatables::of($data)
        ->addColumn('id', function ($row) {
          $html = '
              <div class="checkbox-checked">
                  <div class="form-check d-flex justify-content-center align-items-center">
                  <input class="form-check-input select-row" type="checkbox"
                          style="width: 12px; height: 12px;" value="' . $row->id . '" name="selected_ids[]" id="select-row-' . $row->id . '">
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
                            <a class="square-white view" data-id="' . $row->id . '" style="cursor: pointer;"><svg>
                                <use href="' . asset('assets/svg/icon-sprite.svg#fill-view') . '">
                                </use>
                            </svg>
                            </a>
                            <a class="square-white edit"  data-id="' . $row->id . '" style="cursor: pointer;">
                                <svg><use href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '"></use></svg>
                            </a>
                            <a class="square-white trash"  data-id="' . $row->id . '" style="cursor: pointer;">
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

  public function showBySchedule($grouping_schedule)
  {
    $schedules = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id')->withCount('students'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'class.students:class_id,user_id,name,nisn',
      'class.students.user:id,image',
      'subject' => fn($query) => $query->select('id', 'name', 'code'),
      'teacher' => fn($query) => $query->select('id', 'name', 'user_id'),
      'teacher.user:id,image',
      'room' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id', 'academic_year', 'semester')
    ])->where('grouping_schedule', $grouping_schedule)->get();


    $schedules = $schedules->groupBy(function ($item) {
      return $item->grouping_schedule;
    })->map(function ($group) {
      $first = $group->first();
      $days_time = $group->map(function ($x) {
        return [
          'day' => $x->day,
          'start_time' => $x->start_time,
          'end_time' => $x->end_time,
        ];
      })->values();

      $first->days_time = $days_time;

      return $first;
    })->values();

    $schedule = $schedules->first();
    $this->authorize('view', $schedule);

    return view('user.schedule.show', compact('schedule'));
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

  public function edit($id)
  {
    $this->authorize('update', Schedule::class);

    $schedule = Schedule::with([
      'subject.curriculum' => fn($query) => $query->select('id', 'name'),
      'subject.curriculum.subjects' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'subject' => fn($query) => $query->select('id', 'name', 'curriculum_id'),
      'room' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'class.major:id,name'
    ])->findOrFail($id);

    return $this->sendResponse('Data jadwal ditemukan', $schedule);
  }

  public function store(ScheduleRequest $formRequest)
  {
    try {
      $this->authorize('create', Schedule::class);

      $activePeriod = Period::where('status', true)->first();
      if (!$activePeriod) {
        return $this->sendError('Tidak ada periode aktif.', [], 400);
      }

      $scheduleExist = Schedule::where('class_id', $formRequest->input('class_id'))
        ->where('day', $formRequest->input('day'))
        ->where('period_id', $activePeriod->id)
        ->whereBetween('start_time', [$formRequest->input('start_time'), $formRequest->input('end_time')])
        ->exists();

      if ($scheduleExist) {
        return $this->sendError('Jadwal sudah ada atau bentrok dengan jadwal lain.', [], 400);
      }

      $validated = $formRequest->validated();
      $validated['period_id'] = $activePeriod->id;

      $grouping_schedule = Schedule::where('class_id', $formRequest->class_id)
        ->where('subject_id', $formRequest->subject_id)
        ->where('teacher_id', $formRequest->teacher_id)
        ->where('period_id', $activePeriod->id ?? 0)
        ->first();

      if ($grouping_schedule) {
        $validated['grouping_schedule'] = $grouping_schedule->grouping_schedule;
      } else {
        $validated['grouping_schedule'] = uniqid();
      }

      $schedule = Schedule::create($validated);

      // Menggunakan Queue untuk memproses pembuatan meeting secara asynchronous
      Queue::push(new CreateScheduleMeetings($schedule));

      return $this->sendResponse('Jadwal berhasil ditambahkan.', $schedule, 201);
    } catch (\Exception $e) {
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function update(ScheduleRequest $formRequest, $id)
  {
    try {
      $this->authorize('update', Schedule::class);

      $activePeriod = Period::where('status', true)->first();
      if (!$activePeriod) {
        return $this->sendError('Tidak ada periode aktif.', [], 400);
      }

      $schedule = Schedule::findOrFail($id);

      // Simpan data lama untuk perbandingan
      $oldScheduleData = [
        'day' => $schedule->day,
        'start_time' => $schedule->start_time,
        'end_time' => $schedule->end_time,
        'meeting_method' => $schedule->meeting_method,
        'class_id' => $schedule->class_id,
        'subject_id' => $schedule->subject_id,
        'teacher_id' => $schedule->teacher_id,
        'room_id' => $schedule->room_id,
        'grouping_schedule' => $schedule->grouping_schedule,
      ];

      // Cek konflik jadwal (exclude jadwal yang sedang diupdate)
      $scheduleExist = Schedule::where('id', '!=', $id)
        ->where('class_id', $formRequest->input('class_id'))
        ->where('day', $formRequest->input('day'))
        ->where('period_id', $activePeriod->id)
        ->whereBetween('start_time', [$formRequest->input('start_time'), $formRequest->input('end_time')])
        ->exists();

      if ($scheduleExist) {
        return $this->sendError('Jadwal sudah ada atau bentrok dengan jadwal lain.', [], 400);
      }

      $validated = $formRequest->validated();
      $validated['period_id'] = $activePeriod->id;

      if ($schedule->subject_id != $formRequest->subject_id || $schedule->teacher_id != $formRequest->teacher_id || $schedule->class_id != $formRequest->class_id) {
        $grouping_schedule = Schedule::where('class_id', $formRequest->class_id)
          ->where('subject_id', $formRequest->subject_id)
          ->where('teacher_id', $formRequest->teacher_id)
          ->where('period_id', $activePeriod->id ?? 0)
          ->first();

        if ($grouping_schedule) {
          $validated['grouping_schedule'] = $grouping_schedule->grouping_schedule;
        } else {
          $validated['grouping_schedule'] = uniqid();
        }
      }

      // Update jadwal
      $schedule->update($validated);

      // Cek apakah ada perubahan yang mempengaruhi meeting
      $meetingAffectingChanges = $this->hasMeetingAffectingChanges($oldScheduleData, $validated);
      Log::info($meetingAffectingChanges);
      if ($meetingAffectingChanges) {
        // Queue job untuk update meetings
        Queue::push(new UpdateScheduleMeetings($schedule, $oldScheduleData));
      }
      return $this->sendResponse('Jadwal berhasil diedit.', $schedule);
    } catch (\Exception $e) {
      Log::error("Error updating schedule: " . $e->getMessage());
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  /**
   * Cek apakah ada perubahan yang mempengaruhi meeting
   */
  private function hasMeetingAffectingChanges($oldData, $newData)
  {
    // Perubahan yang mempengaruhi meeting
    $meetingAffectingFields = ['day', 'start_time', 'end_time', 'meeting_method', 'grouping_schedule'];

    foreach ($meetingAffectingFields as $field) {
      if (isset($oldData[$field]) && isset($newData[$field]) && $oldData[$field] !== $newData[$field]) {
        return true;
      }
    }

    return false;
  }

  public function destroy(Request $request, $id)
  {
    try {
      $this->authorize('delete', Schedule::class);

      $schedule = Schedule::findOrFail($id);
      $schedule->delete();
      return $this->sendResponse('Jadwal berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function bulkDestroy(Request $request)
  {
    try {
      if (!$request->user()->can('schedule.*')) {
        return abort(403);
      }

      $ids = $request->input('ids');
      $schedules = Schedule::whereIn('id', $ids)->get();
      foreach ($schedules as $schedule) {
        $schedule->delete();
      }
      return $this->sendResponse('Jadwal berhasil dihapus.');
    } catch (\Exception $e) {
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }
}
