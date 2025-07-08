<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Curriculum;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Subject;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Models\Teacher;

class AttendanceController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $data = Schedule::with(['class.major', 'subject', 'teacher'])
        ->orderBy('class_id')
        ->orderBy('subject_id');
      // Filter (kelas, jurusan, tingkat, dsb)
      if ($request->filled('major')) {
        $data->whereHas('class.major', function ($q) use ($request) {
          $q->where('name', $request->major);
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
      return DataTables::of($data)
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.show', $row->id);
          $exportUrl = route('user.attendance.export', $row->id);
          return '<a href="' . $url . '" class="btn btn-info btn-sm">Lihat</a> '
            . '<a href="' . $exportUrl . '" class="btn btn-success btn-sm">Export</a>';
        })
        ->make(true);
    }
    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = \App\Models\SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = \App\Models\SchoolClass::select('name')->distinct()->orderBy('name')->get();
    return view('user.attendance.index', compact('majors', 'classLevels', 'classNames'));
  }

  public function show(Request $request, $id)
  {
    $schedule = Schedule::with(['class.students', 'attendances.statuses', 'subject', 'teacher', 'class.major', 'meetings.attendances.statuses', 'meetings.subject', 'meetings.teacher'])
      ->findOrFail($id);

    if ($request->ajax()) {
      $meetings = $schedule->meetings;
      // Filter meeting
      if ($request->filled('meeting')) {
        $meetings = $meetings->filter(function ($m) use ($request) {
          return $m->name == $request->meeting || $m->id == $request->meeting;
        });
      }
      // Filter tanggal
      if ($request->filled('date')) {
        $meetings = $meetings->filter(function ($m) use ($request) {
          return $m->date == $request->date;
        });
      }
      // Kelompokkan meetings berdasarkan subject+teacher
      $grouped = $meetings->groupBy(function ($m) {
        $subject = $m->subject->name ?? $m->schedule->subject->name ?? '-';
        $teacher = $m->teacher->name ?? $m->schedule->teacher->name ?? '-';
        return $subject . '||' . $teacher;
      });
      $data = [];
      foreach ($grouped as $key => $meetingsGroup) {
        [$subject, $teacher] = explode('||', $key);
        $meetingRows = [];
        foreach ($meetingsGroup as $meeting) {
          $attendanceList = '';
          foreach ($meeting->attendances as $attendance) {
            $student = $attendance->student;
            $status = optional($attendance->statuses->last())->status;
            $label = $status == 'H' ? 'Hadir' : ($status == 'I' ? 'Izin' : ($status == 'S' ? 'Sakit' : ($status == 'A' ? 'Alpha' : '')));
            $badge = $status == 'H' ? 'success' : ($status == 'I' ? 'info' : ($status == 'S' ? 'warning' : ($status == 'A' ? 'danger' : 'secondary')));
            $attendanceList .= '<div>' . ($student->name ?? '-') . ' <span class="badge bg-' . $badge . '">' . ($label ?: '-') . '</span></div>';
          }
          $meetingRows[] = [
            'meeting_name' => $meeting->name ?? ('Pertemuan ' . $meeting->id),
            'date' => $meeting->date ?? '-',
            'attendances' => $attendanceList ?: '<span class="text-muted">Belum ada data</span>',
          ];
        }
        $data[] = [
          'subject_name' => $subject,
          'teacher_name' => $teacher,
          'meetings' => $meetingRows,
        ];
      }
      return DataTables::of($data)
        ->addColumn('meetings_table', function ($row) {
          $html = '<table class="table table-bordered table-sm mb-0">';
          $html .= '<thead><tr><th>Pertemuan</th><th>Tanggal</th><th>Daftar Kehadiran</th></tr></thead><tbody>';
          foreach ($row['meetings'] as $m) {
            $html .= '<tr>';
            $html .= '<td>' . $m['meeting_name'] . '</td>';
            $html .= '<td>' . $m['date'] . '</td>';
            $html .= '<td>' . $m['attendances'] . '</td>';
            $html .= '</tr>';
          }
          $html .= '</tbody></table>';
          return $html;
        })
        ->rawColumns(['meetings_table'])
        ->make(true);
    }
    return view('user.attendance.by-schedule', compact('schedule'));
  }

  public function edit(Request $request, $id)
  {
    // Modal edit status attendance (hanya vice-principal/teacher terkait)
    $attendance = Attendance::findOrFail($id);
    if (!Gate::allows('edit-attendance', $attendance)) {
      abort(403);
    }
    return response()->json(['success' => true, 'data' => $attendance]);
  }

  public function update(Request $request, $id)
  {
    // Update status attendance (hanya vice-principal/teacher terkait)
    $attendance = Attendance::findOrFail($id);
    if (!Gate::allows('edit-attendance', $attendance)) {
      abort(403);
    }
    $request->validate([
      'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
    ]);
    $attendance->status = $request->status;
    $attendance->save();
    return response()->json(['success' => true, 'message' => 'Status kehadiran berhasil diupdate']);
  }

  public function export(Request $request, $id)
  {
    // Export Excel rekap kehadiran per jadwal
    // ... implementasi export ...
    return Excel::download(new \App\Exports\AttendanceExport($id), 'rekap-kehadiran.xlsx');
  }

  public function listKelas(Request $request)
  {
    if ($request->ajax()) {
      $data = SchoolClass::query();
      $hasMajors = Major::count() > 0;
      if ($hasMajors) {
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
      return DataTables::of($data)
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.schedulebyclass', $row->id);
          return '<a href="' . $url . '" class="btn btn-info btn-sm">Lihat Jadwal</a>';
        })
        ->rawColumns(['Aksi'])
        ->make(true);
    }
    $majors = Major::select('id', 'name')->orderBy('name')->get();
    $classLevels = SchoolClass::select('level')->distinct()->orderBy('level')->get();
    $classNames = SchoolClass::select('name')->distinct()->orderBy('name')->get();
    return view('user.attendance.class-list', compact('majors', 'classLevels', 'classNames'));
  }

  public function jadwalByKelas(Request $request, $classId)
  {
    $class = SchoolClass::with('major')->findOrFail($classId);

    if ($request->ajax()) {
      // Perbaiki ambiguitas kolom 'id' dengan menambahkan prefix tabel pada MIN(id)
      $data = Schedule::selectRaw('schedules.subject_id, schedules.teacher_id, MIN(schedules.id) as schedule_id')
        ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
        ->join('teachers', 'schedules.teacher_id', '=', 'teachers.id')
        ->addSelect('subjects.name as subject_name', 'teachers.name as teacher_name')
        ->groupBy('schedules.subject_id', 'schedules.teacher_id')
        ->where('schedules.class_id', $classId)
        ->get();

      if ($request->filled('search')) {
        $search = $request->search['value'];
        $data->where(function ($q) use ($search) {
          $q->whereFullText('subjects.name', $search);
        });
      }

      if ($request->filled('guru')) {
        $data->whereHas('teacher', function ($q) use ($request) {
          $q->where('name', $request->guru);
        });
      }
      if ($request->filled('subject')) {
        $data->whereHas('subject', function ($q) use ($request) {
          $q->where('name', $request->subject);
        });
      }
      if ($request->filled('hari')) {
        $data->where('day', $request->hari);
      }
      return DataTables::of($data)
        ->addColumn('Mata Pelajaran', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . $row->subject_name . '</p>
          </div>
          ';
          return $html;
        })->addColumn('Guru Pengajar', function ($row) {
          $html = '
          <div class="product-names">
          <p>' . $row->teacher_name . '</p>
          </div>
          ';
          return $html;
        })
        ->addColumn('Aksi', function ($row) {
          $url = route('user.attendance.show', $row->schedule_id);
          return '<a href="' . $url . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Lihat Rekap</a>';
        })
        ->rawColumns(['Mata Pelajaran', 'Guru Pengajar', 'Aksi'])->make(true);
    }

    $teachers = Teacher::select('id', 'name')->get();
    $schedules = Schedule::with(['subject', 'teacher'])
      ->where('class_id', $classId)
      ->orderBy('subject_id')
      ->get();
    return view('user.attendance.schedule-by-class', compact('class', 'schedules', 'teachers'));
  }
}
