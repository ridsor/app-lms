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

class ScheduleController extends Controller
{
  public function index(Request $request)
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
          return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat Jadwal</a>';
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
    return view('user.schedule.index', [
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
    if (!$activePeriod) {
      abort(404, 'Tidak ada periode aktif.');
    }
    Log::info('here');

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
        ->where('period_id', $activePeriod->id)
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
      $meetingMethods = [['value' => 'Offline', 'label' => 'Luring'], ['value' => 'Online', 'label' => 'Daring'], ['value' => 'Hybrid', 'label' => 'Hybrid']];
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

  public function show(Request $request, $id)
  {
    if (!$request->user()->can('schedule.*')) {
      return abort(403);
    }

    $schedule = Schedule::with([
      'class' => fn($query) => $query->select('id', 'name', 'level', 'major_id'),
      'class.major' => fn($query) => $query->select('id', 'name'),
      'subject' => fn($query) => $query->select('id', 'name'),
      'teacher' => fn($query) => $query->select('id', 'name'),
      'room' => fn($query) => $query->select('id', 'name'),
      'period' => fn($query) => $query->select('id')
    ])->findOrFail($id);

    $data = [
      'id' => $schedule->id,
      'class' => $schedule->class,
      'subject' => $schedule->subject->name,
      'teacher' => $schedule->teacher->name,
      'room' => $schedule->room->name ?? '-',
      'day' => $schedule->day,
      'start_time' => $schedule->start_time,
      'end_time' => $schedule->end_time,
      'meeting_method' => $schedule->meeting_method,
      'period' => $schedule->period->id,
    ];
    return $this->sendResponse('Data jadwal ditemukan', $data);
  }

  public function edit(Request $request, $id)
  {
    if (!$request->user()->can('schedule.*')) {
      return abort(403);
    }

    $schedule = Schedule::findOrFail($id);
    return $this->sendResponse('Data jadwal ditemukan', $schedule);
  }

  public function store(ScheduleRequest $formRequest)
  {
    try {
      if (!$formRequest->user()->can('schedule.*')) {
        return abort(403);
      }

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
      $schedule = Schedule::create($validated);

      // meetings
      for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
        if ($tanggal->format('l') == $schedule->day) {
          // $isLibur = HariLibur::where('tanggal', $tanggal->format('Y-m-d'))->exists();
          $isWeekend = $tanggal->isWeekend(); // true jika Sabtu/Minggu

          if (!$isWeekend) {
            $schedule->meetings()->create([
              'date' => $tanggal->format('Y-m-d'),
              'meeting_method' => $schedule->meeting_method,
              'type' => 'Learning',
            ]);
          }
        }
      }

      return $this->sendResponse('Jadwal berhasil ditambahkan', $schedule, 201);
    } catch (\Exception $e) {
      Log::error($e);
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function update(ScheduleRequest $formRequest, $id)
  {
    try {
      if (!$formRequest->user()->can('schedule.*')) {
        return abort(403);
      }

      $activePeriod = Period::where('status', true)->first();
      if (!$activePeriod) {
        return $this->sendError('Tidak ada periode aktif.', [], 400);
      }

      $scheduleExist = Schedule::where('id', '!=', $id)
        ->where('class_id', $formRequest->input('class_id'))
        ->where('day', $formRequest->input('day'))
        ->where('period_id', $activePeriod->id)
        ->whereBetween('start_time', [$formRequest->input('start_time'), $formRequest->input('end_time')])
        ->exists();

      if ($scheduleExist) {
        return $this->sendError('Jadwal sudah ada atau bentrok dengan jadwal lain.', [], 400);
      }

      $schedule = Schedule::findOrFail($id);
      $validated = $formRequest->validated();
      $validated['period_id'] = $activePeriod->id;
      $schedule->update($validated);
      return $this->sendResponse('Jadwal berhasil diedit', $schedule);
    } catch (\Exception $e) {
      return $this->sendError('Silakan coba lagi.', [], 500);
    }
  }

  public function destroy(Request $request, $id)
  {
    try {
      if (!$request->user()->can('schedule.*')) {
        return abort(403);
      }
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
