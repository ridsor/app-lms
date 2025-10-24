<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeachingJournalRequest;
use App\Models\Major;
use App\Models\Meeting;
use App\Models\Period;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TeachingJournalController extends Controller
{
    public function store(TeachingJournalRequest $request, $meeting_id)
    {
        try {
            $meeting = Meeting::findOrFail($meeting_id);
            $this->authorize('update', $meeting);

            $today = Carbon::now();
            $isToday = $today->isSameDay(Carbon::parse($meeting->date));

            $isDuringSchedule = $isToday && $meeting->schedule_time->start_time <= now()
                && now() <= $meeting->schedule_time->end_time->addHours(2);

            if (!$isDuringSchedule) {
                return $this->sendError('Jurnal hanya dapat diisi selama waktu pertemuan hingga 2 jam setelahnya.', [], 400);
            }

            $validated = $request->validated();
            $validated['meeting_id'] = $meeting_id;

            $teaching_journal = $meeting->teaching_journal;
            if ($teaching_journal) {
                $meeting->teaching_journal->update($validated);
            } else {
                $teaching_journal = $meeting->teaching_journal()->create($validated);
            }

            activity()
                ->useLog('Jurnal Mengajar')
                ->performedOn($teaching_journal)
                ->causedBy($request->user())
                ->log('Pengguna mengisi jurnal mengajar');

            return $this->sendResponse('Jurnal berhasil disimpan.', $meeting->teaching_journal, 201);
        } catch (\Exception $e) {
            return $this->sendError(
                'Silakan coba lagi.',
                [],
                500
            );
        }
    }

    public function classList(Request $request)
    {
        if (!$request->user()->hasRole('vice-principal')) abort(403);

        $hasMajor = Major::count() > 0;
        $activePeriod = Period::where('status', true)->first();
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
                'schedules' => function ($q) use ($activePeriod, $request) {
                    $q->select(['id', 'class_id'])->withCount([
                        'meetings as present_count' => function ($query) {
                            $query->where('type', '!=', 'Holiday')->has('teaching_journal');
                        },
                        'meetings as meeting_count' => function ($query) {
                            $query->where('type', '!=', 'Holiday');
                        }
                    ]);
                    if ($request->filled('periode')) {
                        $q->where('schedules.period_id', $request->periode);
                    } else {
                        $q->where('schedules.period_id', $activePeriod->id ?? 0);
                    }
                },
            ]);

            // Execute the query and process results
            $data = $data->get()->map(function ($class) {
                $totalSchedule = 0;
                $totalPercentage = 0.0;

                foreach ($class->schedules as $schedule) {
                    $totalSchedule++;

                    if ($schedule->meeting_count > 0) {
                        $totalPercentage += round(($schedule->present_count / $schedule->meeting_count * 100), 2);
                    }
                }

                $class->journal_percentage = $totalSchedule > 0
                    ? round($totalPercentage / $totalSchedule, 2)
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
                    $url = route('user.journal.schedulebyclass', $row->id);
                    return '
                <div>
                <a href="' . $url . '" class="badge badge-light-primary fs-6">' . $row->journal_percentage . '%</a>
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

        return view('user.teaching_journal.class-list', compact('majors', 'classLevels', 'classNames', 'hasMajor', 'activePeriod', 'periods'));
    }

    public function showJournal($meeting_id)
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
                'schedule_time',
                'teaching_journal'
            ])->withCount('attendances')->find($meeting_id);

        $this->authorize('view', $meeting);

        $meetings = $meeting->schedule->meetings()->get();
        $index = $meetings->search(function ($item) use ($meeting_id) {
            return $item->id == $meeting_id;
        });
        $meeting->meeting_at = $index + 1;

        return $this->sendResponse('Berhasil mengambil data', $meeting);
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
                    'class_id',
                    'subjects.name as subject_name',
                    'teachers.name as teacher_name'
                ])
                ->where('schedules.class_id', $classId);

            // filter
            if ($request->filled('periode')) {
                $data->where('schedules.period_id', $request->periode);
            } else {
                $data->where('schedules.period_id', $activePeriod->id ?? 0);
            }
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

            $data = $data->withCount([
                'meetings as present_count' => function ($query) {
                    $query->where('type', '!=', 'Holiday')->has('teaching_journal');
                },
                'meetings as meeting_count' => function ($query) {
                    $query->where('type', '!=', 'Holiday');
                }
            ]);

            $data = $data->get()->map(function ($schedule) {
                if ($schedule->meeting_count) {
                    $schedule->journal_percentage =  round(($schedule->present_count / $schedule->meeting_count * 100), 2);
                } else {
                    $schedule->journal_percentage = 0.0;
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
                ->addColumn('', function ($row) {
                    $url = route('user.journal.meetingBySchedule', [
                        'schedule_id' => $row->id,
                    ]);
                    return '
                        <div>
                        <a href="' . $url . '" class="badge badge-light-primary fs-6">' . $row->journal_percentage . '%</a>
                        </div>
                    ';
                })
                ->rawColumns(['Mata Pelajaran', 'Pengajar', ''])->make(true);
        }

        $periods = Period::select('id', 'academic_year', 'semester')->orderBy('start_date', 'desc')->get();
        $teachers = Teacher::select('id', 'name')->get();
        $subjects = Subject::select('id', 'name')->get();

        return view('user.teaching_journal.schedule-by-class', compact('class', 'teachers', 'subjects', 'activePeriod', 'periods'));
    }

    public function meetingBySchedule(Request $request, $schedule_id)
    {
        $schedule = Schedule::with([
            'subject:id,code,name',
            'class:id,name,major_id',
            'class.major:id,name',
            'room:id,name'
        ])->findOrFail($schedule_id);
        $this->authorize('view', $schedule);

        if ($request->ajax()) {
            $data = Meeting::select('meetings.id as id', 'meetings.schedule_id as schedule_id', 'date', 'started_at', 'schedule_time_id', 'schedule_times.start_time as start_time', 'schedule_times.end_time as end_time')
                ->join('schedule_times', 'schedule_times.id', '=', 'meetings.schedule_time_id')
                ->with([
                    'schedule_time:id,start_time,end_time',
                    'teaching_journal:id,meeting_id',
                ])
                ->where('meetings.schedule_id', $schedule_id)->orderBy('meetings.date', 'asc')->orderBy('schedule_times.start_time', 'asc');

            $data = $data->get();

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
                ->addColumn('', function ($row) {
                    return '
                            <div class="">
                            <button onclick="handleDetailMeeting(' . $row->id . ')" ' . (is_null($row->teaching_journal) ? 'disabled' : '') . ' class="border-0 badge ' . (is_null($row->teaching_journal) ? 'badge-light-danger' : 'badge-light-primary') . '">Lihat</button>
                            </div>
                    ';
                });

            return $dataTable
                ->rawColumns(['Waktu', ''])
                ->make(true);
        }

        return view('user.teaching_journal.meeting-by-schedule', compact('schedule'));
    }

    public function export(Request $request, $id)
    {
        if (!$request->user()->hasRole('teacher')) return abort(403);

        $schedule = Schedule::with([
            'subject:id,code,name',
            'class:id,name,level,major_id',
            'class.major:id,name',
            'teacher:id,name,nip',
            'period:id,academic_year,semester',
            'meetings' => function ($q) {
                $q->orderBy('date', 'asc');
            },
            'meetings.teaching_journal',
            'meetings.materials:id,meeting_id,title,description',
            'meetings.tasks:id,meeting_id,title,description'
        ])->find($id);
        $this->authorize('view', $schedule);

        $pdf = Pdf::loadView('pdf.journal', $schedule->toArray())->setPaper('A4', 'portrait');

        $filename = "Jurnal Mengajar - "
            . ($schedule->period->semester == 'odd' ? 'Ganjil' : 'Genap')
            . " TA "
            . str_replace(['/', '\\'], '-', $schedule->period->academic_year)
            . " "
            . $schedule->subject->code
            . ".pdf";


        return $pdf->download($filename);
    }
    public function exporttes(Request $request, $id)
    {
        if (!$request->user()->hasRole('teacher')) return abort(403);

        $schedule = Schedule::with([
            'subject:id,code,name',
            'class:id,name,level,major_id',
            'class.major:id,name',
            'teacher:id,name,nip',
            'period:id,academic_year,semester',
            'meetings' => function ($q) {
                $q->orderBy('date', 'asc');
            },
            'meetings.teaching_journal',
            'meetings.materials:id,meeting_id,title,description',
            'meetings.tasks:id,meeting_id,title,description'
        ])->find($id);
        $this->authorize('view', $schedule);


        return view('pdf.journal', $schedule->toArray());
    }
}
