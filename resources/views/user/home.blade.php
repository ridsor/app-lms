@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Beranda')

@section('main_content')
    <div class="container-fluid dashboard-3 py-3">
        <div class="row g-2">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card o-hidden welcome-card">
                            <div class="card-body" style="min-height: 138px">
                                <h4 class="mb-3 mt-1 f-w-500 mb-0 f-22">Hi {{ request()->user()->name }} <span> <img
                                            src="{{ asset('assets/icons/hand.svg') }}" alt="hand vector"></span>
                                </h4>
                                <p>Selamat Datang</p>
                            </div><img class="welcome-img" src="{{ asset('assets/icons/widget.svg') }}" alt="search image">
                        </div>
                    </div>
                    @role(['teacher', 'student', 'parent'])
                        <div class="col-sm-6">
                            <div class="card course-box">
                                <div class="card-body">
                                    <div class="course-widget">
                                        <div class="course-icon">
                                            <img style="width:24px; height:24px; filter: invert(1);"
                                                src="{{ asset('assets/icons/task.png') }}" />
                                        </div>
                                        <div>
                                            <h4 class="mb-0"> <span class="counter"
                                                    data-target="100">{{ $countTasks }}</span>
                                            </h4><span class="f-light">Belum Dikerjakan</span><a
                                                href="{{ route('user.task.index') }}" class="btn btn-light f-light">Lihat
                                                Tugas<span class="ms-2"> <svg class="fill-icon f-light">
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#arrowright') }}">
                                                        </use>
                                                    </svg></span></a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="square-group">
                                    <li class="square-1 warning"></li>
                                    <li class="square-1 primary"></li>
                                    <li class="square-2 warning1"></li>
                                    <li class="square-3 danger"></li>
                                    <li class="square-4 light"></li>
                                    <li class="square-5 warning"></li>
                                    <li class="square-6 success"></li>
                                    <li class="square-7 success"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card course-box">
                                <div class="card-body">
                                    <div class="course-widget">
                                        <div class="course-icon warning">
                                            <img style="width:24px; height:24px; filter: invert(1);"
                                                src="{{ asset('assets/icons/exam.png') }}" />
                                        </div>
                                        <div>
                                            <h4 class="mb-0"> <span class="counter"
                                                    data-target="100">{{ $countExams }}</span>
                                            </h4><span class="f-light">Belum dikerjakan</span><a
                                                href="{{ route('user.exam.index') }}" class="btn btn-light f-light">Lihat
                                                Ujian<span class="ms-2"> <svg class="fill-icon f-light">
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#arrowright') }}">
                                                        </use>
                                                    </svg></span></a>
                                        </div>
                                    </div>
                                </div>
                                <ul class="square-group">
                                    <li class="square-1 warning"></li>
                                    <li class="square-1 primary"></li>
                                    <li class="square-2 warning1"></li>
                                    <li class="square-3 danger"></li>
                                    <li class="square-4 light"></li>
                                    <li class="square-5 warning"></li>
                                    <li class="square-6 success"></li>
                                    <li class="square-7 success"></li>
                                </ul>
                            </div>
                        </div>
                    @endrole
                    @role('vice-principal')
                        <div class="col-sm-4">
                            <div class="card widget-hover overflow-hidden">
                                <div class="card-header card-no-border pb-2">
                                    <h5>Guru</h5>
                                </div>
                                <div class="card-body pt-0 count-student">
                                    <div class="school-wrapper">
                                        <div class="school-header">
                                            <h4 class="txt-warning counter">{{ number_format($teacherCount) }}</h4>
                                        </div>
                                        <div class="school-body"> <img src="{{ asset('assets/images/teacher.svg') }}"
                                                alt="total teachers">
                                            <div class="right-line"><img src="{{ asset('assets/images/home-line.png') }}"
                                                    alt="line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card widget-hover overflow-hidden">
                                <div class="card-header card-no-border pb-2">
                                    <h5>Siswa</h5>
                                </div>
                                <div class="card-body pt-0 count-student">
                                    <div class="school-wrapper">
                                        <div class="school-header">
                                            <h4 class="txt-primary counter">{{ number_format($studentCount) }}</h4>
                                        </div>
                                        <div class="school-body"> <img src="{{ asset('assets/images/student.svg') }}"
                                                alt="total teachers">
                                            <div class="right-line"><img src="{{ asset('assets/images/home-line.png') }}"
                                                    alt="line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card widget-hover overflow-hidden">
                                <div class="card-header card-no-border pb-2">
                                    <h5>Orang Tua</h5>
                                </div>
                                <div class="card-body pt-0 count-student">
                                    <div class="school-wrapper">
                                        <div class="school-header">
                                            <h4 class="txt-success counter">{{ number_format($studentCount) }}</h4>
                                        </div>
                                        <div class="school-body"> <img src="{{ asset('assets/images/parent.svg') }}"
                                                alt="total teachers">
                                            <div class="right-line"><img src="{{ asset('assets/images/home-line.png') }}"
                                                    alt="line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 px-0">
                            <div class="card widget-hover rounded-responsive overflow-hidden">
                                <div class="card-body">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <div class="light-card attendance-card widget-hover">
                                                <div class="left-overview-content">
                                                    <div class="svg-box"><img
                                                            src="{{ asset('assets/images/home-attendance.png') }}"
                                                            alt="homework"></div>
                                                </div>
                                                <div class="right-overview-content">
                                                    <div>
                                                        <h6>Kehadiran Siswa</h6>
                                                        <span class="text-muted text-ellipsis">
                                                            Ketidakhadiran siswa berkurang bahkan yang terbaik...
                                                        </span>
                                                    </div>
                                                    <div class="d-flex marks-count">
                                                        <h5>{{ $attendannce_percentage }}%</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="light-card attendance-card widget-hover">
                                                <div class="left-overview-content">
                                                    <div class="svg-box"><img
                                                            src="{{ asset('assets/images/home-attendance.png') }}"
                                                            alt="homework"></div>
                                                </div>
                                                <div class="right-overview-content">
                                                    <div>
                                                        <h6>Jurnal Mengajar</h6>
                                                        <span class="text-muted text-ellipsis">
                                                            Ketidakhadiran guru berkurang bahkan yang terbaik...
                                                        </span>
                                                    </div>
                                                    <div class="d-flex marks-count">
                                                        <h5>{{ $journal_percentage }}%</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endrole
                </div>
            </div>
            @role(['teacher', 'student', 'parent'])
                <div class="col-ed-12 box-col-12">
                    <div class="header-top mb-3">
                        <h5 class="m-0">Jadwal Hari Ini</h5>
                        <div class="card-header-right-icon">
                            <a class="link-only" href="{{ route('user.schedule.index') }}">Lihat
                                Semua<i data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="projects-wrapper">
                        <div class="tab-content" id="top-tabContent">
                            <div class="tab-pane fade show active" id="top-home" role="tabpanel"
                                aria-labelledby="top-home-tab">
                                @if (count($schedules) > 0)
                                    <div class="row g-3">
                                        @foreach ($schedules as $schedule)
                                            <div class="col-xxl-4 col-md-6 col-lg-6 box-col-6">
                                                <a
                                                    href="{{ route('user.schedule.showBySchedule', ['id' => $schedule->id]) }}">
                                                    <div class="progress-project-box">
                                                        <div class="list-box title-line-primary">
                                                            {{-- <div class="header-top"><span
                                                            class="badge badge-light-primary">{{ $schedule->meeting_await ? \Illuminate\Support\Carbon::parse($schedule->meeting_await)->diffForHumans(null, false, false, 2) : '' }}</span>
                                                            </div> --}}
                                                            <div class="project-body">
                                                                <div class="common-f-start gap-3">
                                                                    <div>
                                                                        <h6 class="mb-2">
                                                                            <span class="text-capitalize"
                                                                                style="word-break: break-all;">
                                                                                {{ $schedule->subject->code }} -
                                                                                {{ strtoupper($schedule->subject->name) }}
                                                                            </span>
                                                                        </h6>
                                                                        <div class="d-flex gap-2 flex-wrap">
                                                                            <div>
                                                                                <span class="fw-medium text-nowrap w-fit" style="word-break: break-all;">
                                                                                    {{ $schedule->class->name }}{{ $schedule->class->level }}{{ $schedule->class->major ? ' ' . $schedule->class->major->name : '' }}
                                                                                </span>
                                                                            </div>
                                                                            <div style="max-width: fit-content">&middot;
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-nowrap">{{ $schedule->class->students_count }}
                                                                                    Siswa</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex flex-column">
                                                                            @foreach ($schedule->schedule_times as $schedule_time)
                                                                                {{-- {{ dd($schedule_time) }} --}}
                                                                                <div class="d-flex gap-2">
                                                                                    <div class="col d-flex align-items-center">
                                                                                        <i data-feather="calendar"
                                                                                            witdh="24"></i>
                                                                                        <span
                                                                                            class="mb-0 ms-2">{{ Helper::getDayName($schedule_time->day) }}</span>
                                                                                    </div>
                                                                                    <div class="col">&middot;</div>
                                                                                    <span>
                                                                                        {{ $schedule_time->start_time->translatedFormat('H:i') }}
                                                                                        -
                                                                                        {{ $schedule_time->end_time->translatedFormat('H:i') }}
                                                                                        WIT
                                                                                    </span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="project-bottom common-space">
                                                                    <div class="d-flex flex-column gap-1">
                                                                        <p class="mb-0">Pengajar</p>
                                                                        <p class="mb-0 fw-semibold">
                                                                            {{ $schedule->teacher->name }}</p>
                                                                    </div>
                                                                    <img class="rounded-circle common-circle"
                                                                        style="width: 50px; height: 50px; object-fit: cover"
                                                                        src="{{ optional($schedule->teacher->user)->image ? asset('storage/' . optional($schedule->teacher->user)->image) : asset('assets/svg/user-placeholder.svg') }}"
                                                                        alt="user">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- empty data --}}
                                    <div class="d-flex justify-content-center mb-3 w-100">
                                        <div class="px-4 py-5 d-grid" style="justify-items: center">
                                            <img style="width: 120px; height: 120px"
                                                src="{{ asset('assets/images/data-empty.png') }}" />
                                            <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                                            <p class="mb-0 text-center">Tidak ada jadwal untuk hari ini.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endrole
        </div>
    </div>
@endsection
