<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UKKController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);

        $exams = Exam::with([
            'schedule:id,subject_id,class_id',
            'schedule.meetings:id,schedule_id',
            'schedule.subject:id,name,code',
            'schedule.class:id,name,level,major_id',
            'schedule.class.major:id,name',
            'results' => function ($q) use ($request) {
                if ($request->user()->hasRole('student')) {
                    $q->select(["id", "exam_id"])->where('student_id', $request->user()->student->id);
                } elseif ($request->user()->hasRole('parent')) {
                    $q->select(["id", "exam_id"])->where('student_id', $request->user()->parent->id);
                }
            }
        ])->selectRaw('exams.*,(
                SELECT COUNT(*) FROM exam_answers 
                WHERE exam_answers.score IS NULL
            ) as not_yet_rated')
            ->filter($request->all())
            ->filterByPermission($request->user())
            ->paginate(10);

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
        $examTypes = [
            ['value' => 'Midterm', 'label' => 'UTS'],
            ['value' => 'Final', 'label' => 'UAS'],
        ];
        $schedules = Schedule::select('id', 'subject_id', 'class_id')->with(['subject:id,code,name', 'class:id,name,level,major_id', 'class.major:id,name'])->where('period_id', $activePeriod->id ?? 0)->get();

        return view('user.exam.index', compact('exams', 'schedules', 'classes', 'classLevels', 'classNames', 'majors', 'hasMajors', 'subjects', 'periods', 'examTypes', 'activePeriod'));
    }
}
