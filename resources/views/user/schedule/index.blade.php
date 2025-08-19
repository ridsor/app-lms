@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Jadwal')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Jadwal</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Jadwal</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="common-offcanvas">
            <div class="row g-0 align-items-center justify-content-end mb-4">
                <div class="col-auto">
                    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#filter" aria-controls="filter"
                        class="btn btn-outline-success gap-1 btn-sm d-flex justify-content-center align-items-center">
                        <i data-feather="filter" style="width:18px; height:18px"></i>
                        <span>Filter</span>
                    </button>
                    <div class="offcanvas offcanvas-end" id="filter" tabindex="-1" aria-labelledby="filterLabel">
                        <div class="offcanvas-header pb-0">
                            <h5 class="offcanvas-title" id="filterLabel">Filter</h5><button class="btn-close" type="button"
                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body custom-input custom-scrollbar">
                            <form action="" method="GET">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="period-filter">Periode</label>
                                        <select class="form-select" id="period-filter" name="periode"
                                            aria-label="Select period">
                                            <option value="" selected>Pilih Periode</option>
                                            @foreach ($periods as $period)
                                                @if (empty(request()->input('periode') && $activePeriod->id == $period->id))
                                                    <option value="{{ $period->id }}" selected>
                                                        {{ $period->academic_year }}
                                                        {{ Helper::getSemesterLabel($period->semester) }}
                                                    </option>
                                                @elseif($period->id == request()->input('periode'))
                                                    <option value="{{ $period->id }}" selected>
                                                        {{ $period->academic_year }}
                                                        {{ Helper::getSemesterLabel($period->semester) }}
                                                    </option>
                                                @else
                                                    <option value="{{ $period->id }}">
                                                        {{ $period->academic_year }}
                                                        {{ Helper::getSemesterLabel($period->semester) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @role('teacher')
                                        @if ($majors->count() > 0)
                                            <div class="col-12">
                                                <label class="form-label" for="major-filter">Jurusan</label>
                                                <select class="form-select" id="major-filter" name="jurusan"
                                                    aria-label="Select major">
                                                    <option value="" selected>Pilih Jurusan</option>
                                                    @foreach ($majors as $major)
                                                        @if ($major->name == request()->input('jurusan'))
                                                            <option value="{{ $major->name }}" selected>
                                                                {{ $major->name }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $major->name }}">
                                                                {{ $major->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endrole
                                    <div class="col-12">
                                        <label class="form-label" for="subject-filter">Mata Pelajaran</label>
                                        <select class="selectpicker search-picker filter" name="mata-pelajaran"
                                            data-live-search="true" id="subject-filter">
                                            <option value="">Pilih Mata Pelajaran</option>
                                            @foreach ($subjects as $subject)
                                                @if ($subject->name == request()->input('mata-pelajaran'))
                                                    <option value="{{ $subject->name }}" selected>
                                                        {{ $subject->name }}
                                                        -
                                                        {{ $subject->curriculum->name }}</option>
                                                @else
                                                    <option value="{{ $subject->name }}">{{ $subject->name }}
                                                        -
                                                        {{ $subject->curriculum->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @role('teacher')
                                        <div class="col-12">
                                            <label class="form-label" for="class-filter">Kelas</label>
                                            <select class="form-select" id="class-filter" aria-label="Select class"
                                                name="kelas">
                                                <option value="" selected>Pilih Kelas</option>
                                                @foreach ($classNames as $class)
                                                    @if ($class->name == request()->input('kelas'))
                                                        <option value="{{ $class->name }}" selected>
                                                            {{ $class->name }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $class->name }}">{{ $class->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endrole
                                    @role('teacher')
                                        <div class="col-12">
                                            <label class="form-label" for="level-filter">Tingkat</label>
                                            <select class="form-select" id="level-filter" aria-label="Select level"
                                                name="tingkat">
                                                <option value="" selected>Pilih Tingkat</option>
                                                @foreach ($classLevels as $classLevel)
                                                    @if ($classLevel->level == request()->input('tingkat'))
                                                        <option value="{{ $classLevel->level }}" selected>
                                                            {{ $classLevel->level }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $classLevel->level }}">
                                                            {{ $classLevel->level }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endrole
                                    <div class="col-12">
                                        <label class="form-label" for="class-filter">Hari</label>
                                        <select class="form-select" id="class-filter" aria-label="Select class"
                                            name="hari">
                                            <option value="" selected>Pilih Hari</option>
                                            @foreach ($days as $day)
                                                @if ($day == request()->input('hari'))
                                                    <option value="{{ $day }}" selected>
                                                        {{ $day }}
                                                    </option>
                                                @else
                                                    <option value="{{ $day }}">{{ $day }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end align-items-center gap-2">
                                        <a class="btn btn-outline-primary f-w-500"
                                            href="?{{ http_build_query(['value' => request()->query('value')]) }}"
                                            id="filter-reset-btn">Reset</a>

                                        <button class="btn btn-primary f-w-500" id="filter-btn">Terapkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 px-0">
                <div class="rounded-responsive">
                    <div class="projects-wrapper">
                        <div class="tab-content" id="top-tabContent">
                            <div class="tab-pane fade show active" id="top-home" role="tabpanel"
                                aria-labelledby="top-home-tab">
                                @if (count($schedules) > 0)
                                <div class="row g-4">
                                    @foreach ($schedules as $schedule)
                                        <div class="col-xxl-4 col-md-6 col-lg-6 box-col-6">
                                            <a
                                                href="{{ route('user.schedule.showBySchedule', ['code' => $schedule->subject->code]) }}">
                                                <div class="progress-project-box">
                                                    <div class="list-box title-line-primary">
                                                        {{-- <div class="header-top"><span
                                                            class="badge badge-light-primary">{{ $schedule->meeting_await ? \Illuminate\Support\Carbon::parse($schedule->meeting_await)->diffForHumans(null, false, false, 2) : '' }}</span>
                                                            </div> --}}
                                                        <div class="project-body">
                                                            <div class="common-f-start gap-3">
                                                                <div>
                                                                    <h6 class="mb-2">
                                                                        <span class="text-capitalize">
                                                                            {{ $schedule->subject->code }} -
                                                                            {{ strtoupper($schedule->subject->name) }}
                                                                        </span>
                                                                    </h6>
                                                                    <div class="d-flex gap-2 flex-wrap">
                                                                        <div>
                                                                            <span class="fw-medium text-nowrap w-fit">
                                                                                {{ $schedule->class->name }}{{ $schedule->class->level }}{{ $schedule->class->major ? ' ' . $schedule->class->major->name : '' }}
                                                                            </span>
                                                                        </div>
                                                                        <div style="max-width: fit-content">&middot;</div>
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
                                    @else
                                    {{-- empty data --}}
                                    <div class="d-flex justify-content-center mb-3 w-100">
                                        <div class="px-4 py-5 d-grid" style="justify-items: center">
                                            <img style="width: 120px; height: 120px"
                                                src="{{ asset('assets/images/data-empty.png') }}" />
                                            <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                                            <p class="mb-0 text-center">Belum ada jadwal yang tersedia.</p>
                                        </div>
                                    </div>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
@endsection
