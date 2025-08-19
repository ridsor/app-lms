@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Jadwal ' . $schedule->subject->code)

@section('main_content')
    <div class="container-fluid p-0">
        <div class="page-title">
            <div class="row p-2 p-sm-0">
                <div class="col-sm-6">
                    <h3>Jadwal</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('user.schedule.index') }}">
                                Jadwal
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ $schedule->subject->code }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="container-fluid e-category">
            <div class="row g-4">
                <div class="col-12 col-lg-3 p-0 position-static d-flex flex-column">
                    <div class="p-3">
                        <div class="fs-6">Daftar Pertemuan</div>
                    </div>
                    <div class="h-100 flex-grow-1 w-100">
                        <div class="position-relative overflow-auto custom-scrollbar meeting-sidebar" id="my-sticky">
                            @include('user.schedule.meeting.sidebar')
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-9 p-0">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body">
                            <div class="row g-2 mb-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Periode </label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $schedule->period->academic_year }}
                                            {{ Helper::getSemesterLabel($schedule->period->semester) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Kode Matpel</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $schedule->subject->code }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Mata Pelajaran</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ strtoupper($schedule->subject->name) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Kelas</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $schedule->class->name }}{{ $schedule->class->level }}
                                        </span>
                                    </p>
                                </div>
                                @if ($schedule->class->major)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label">Jurusan</label>
                                        <p class="c-o-light f-w-600">
                                            <span>
                                                {{ $schedule->class->major->name }}
                                            </span>
                                        </p>
                                    </div>
                                @endif
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Waktu</label>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($schedule->schedule_times as $schedule_time)
                                            <div
                                                class="d-flex gap-2 align-items-center c-o-light f-w-600 flex-wrap text-nowrap">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="icon d-inline-flex justify-content-center align-items-center">
                                                        <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                                    </span>
                                                    <span class="mb-0 ms-2"
                                                        id="date">{{ Helper::getDayName($schedule_time->day) }},
                                                    </span>
                                                </div>
                                                <div>
                                                    <span>
                                                        <span
                                                            id="start_time">{{ $schedule_time->start_time->translatedFormat('H:i') }}</span>
                                                        -
                                                        <span
                                                            id="end_time">{{ $schedule_time->end_time->translatedFormat('H:i') }}</span>
                                                        WIT
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <h6 class="mb-3">Pengajar</h6>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="profile-media">
                                            <img class="rounded-circle"
                                                style="
                              width: 50px;
                              height: 50px;
                              object-fit: cover;"
                                                id="teacher-image"
                                                src="{{ $schedule->teacher->user->image ? asset('storage/' . $schedule->teacher->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                                                alt="user">
                                        </div>
                                        <div class="d-flex">
                                            <p class="mb-0 c-o-light fw-medium" id="teacher">
                                                {{ $schedule->teacher->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-3 gap-4">
                                    <h6>Peserta</h6>
                                    <p class="mb-0 c-o-light fw-medium fs-6">
                                        <span
                                            class="badge badge-light-primary">{{ $schedule->class->students_count }}</span>
                                    </p>
                                </div>
                                <div class="row g-4">
                                    @foreach ($schedule->class->students as $student)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="profile-media">
                                                    <img class="rounded-circle"
                                                        style="
                                                            width: 50px;
                                                            height: 50px;
                                                            object-fit: cover;"
                                                        id="teacher-image"
                                                        src="{{ $student->user->image ? asset('storage/' . $student->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                                                        alt="user">
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <p class="mb-0 c-o-light fw-medium" id="teacher">{{ $student->name }}
                                                    </p>
                                                    <p class="mb-0 c-o-light" id="teacher">{{ $student->nis }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
@endsection
