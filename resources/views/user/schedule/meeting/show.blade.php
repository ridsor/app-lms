@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Jadwal ' . $meeting->schedule->subject->code)

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <style>
        .view_file_path .Archive,
        .view_file_path .Link {
            background: #f5f6f9 !important;
        }

        .dark-only .view_file_path .Archive,
        .dark-only .view_file_path .Link {
            background: #1d1e26 !important;
        }

        .action_meeting_text {
            opacity: 0 !important;
            transition: all 0.3s;
            pointer-events: none
        }

        .item:focus .action_meeting_text,
        .item:hover .action_meeting_text {
            outline: none;
            pointer-events: auto;
            opacity: 1 !important;
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid p-0">
        <div class="page-title">
            <div class="row mb-4 p-2 p-sm-0">
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
                        <li class="breadcrumb-item active">
                            <a
                                href="{{ route('user.schedule.showBySchedule', ['code' => $meeting->schedule->subject->code]) }}">
                                {{ $meeting->schedule->subject->code }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Pertemuan {{ $meeting->meeting_at }}</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card mb-0 rounded-2 p-3 p-md-4"
                        style="background: linear-gradient(103.75deg,#33B1EE -13.9%,var(--theme-default) 79.68%)">
                        <h3 class="text-white">{{ $schedule->subject->code }} &middot;
                            {{ strtoupper($schedule->subject->name) }}</h3>
                        <p class="text-white fw-medium">
                            {{ $schedule->class->name }}{{ $schedule->class->level }}{{ $schedule->class->major ? ' ' . $schedule->class->major->name : '' }}
                            - {{ Helper::getMeetingMethodLabel($meeting->meeting_method) }}</p>
                        <div class="d-flex flex-column">
                            @foreach ($schedule->schedule_times as $schedule_time)
                                <div class="d-flex gap-2 text-white">
                                    <div class="d-flex align-items-center">
                                        <span class="icon d-inline-flex justify-content-center align-items-center">
                                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                        </span>
                                        <span class="mb-0 ms-2">{{ Helper::getDayName($schedule_time->day) }}</span>
                                    </div>
                                    <div>&middot;</div>
                                    <span>
                                        {{ $schedule_time->start_time->translatedFormat('H:i') }} -
                                        {{ $schedule_time->end_time->translatedFormat('H:i') }} WIT
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 g-md-0">
            <div class="col-12 col-lg-3 position-static d-flex flex-column">
                <div class="p-3">
                    <div class="fs-6">Daftar Pertemuan</div>
                </div>
                <div class="h-100 flex-grow-1 w-100">
                    <div class="position-relative overflow-auto custom-scrollbar meeting-sidebar" id="my-sticky">
                        @include('user.schedule.meeting.sidebar')
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-9">
                <div class="card my-0 h-100 rounded-responsive shadow-none">
                    <div class="card-body px-2 py-4 px-sm-3">
                        <div class="d-flex flex-column h-100 gap-3">
                            <div class="w-100">
                                <div
                                    class="mb-3 d-flex flex-column flex-sm-row justify-content-between gap-3 flex-wrap w-100">
                                    <div class="text-nowrap">
                                        <div class="d-flex align-items-center gap-3">
                                            <h3>Pertemuan {{ $meeting->meeting_at }}</h3>
                                            <p class="mb-0 c-o-light fw-medium fs-6">
                                                {!! Helper::getMeetingTypeLabel($meeting->type) !!}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('user.attendance.edit', ['schedule_id' => $schedule->id, 'meeting_id' => $meeting->id]) }}"
                                            class="d-flex gap-2 align-items-center">
                                            <div class="rounded-circle ratio ratio-1x1 badge badge-light-warning"
                                                style="width: 40px; height: 40px;">
                                                <i data-feather="bar-chart-2" class="p-2 w-100 h-100">
                                                </i>
                                            </div>
                                            <div>
                                                <p class="fw-semibold" style="margin-bottom:4px">
                                                    {{ $attendancePercentage }}%
                                                </p>
                                                <div class="progress" style="width: 100px; height:4px;">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: {{ $attendancePercentage }}%"
                                                        aria-valuenow="{{ $attendancePercentage }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="d-flex align-items-center">
                                        <span class="icon d-inline-flex justify-content-center align-items-center">
                                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                        </span>
                                        <span class="mb-0 ms-2">{{ $meeting->formatted_date }}</span>
                                    </div>
                                    <div>&middot;</div>
                                    <span>
                                        {{ $meeting->schedule_time->start_time->translatedFormat('H:i') }} -
                                        {{ $meeting->schedule_time->end_time->translatedFormat('H:i') }} WIT
                                    </span>
                                </div>
                            </div>
                            @role('teacher')
                                <div class="d-flex align-items-center gap-3 w-100">
                                    <i class="fa-solid fa-circle-info txt-primary"></i>
                                    <span class="txt-primary">
                                        Pengajar dapat mengisi realisasi setelah klik Mulai Belajar dan 2 jam setelah waktu
                                        pertemuan
                                    </span>
                                </div>
                                <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap  w-100">
                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                        data-bs-title="Mulai belajar dapat dilakukan saat waktu pertemuan">
                                        <button class="btn btn-primary" id="start_learning"
                                            data-title="{{ 'Pertemuan ' . $meeting->meeting_at }}"
                                            data-meeting-id="{{ $meeting->id }}" type="button"
                                            {{ $isStartedAt ? '' : 'disabled' }}>
                                            Mulai Belajar
                                        </button>
                                    </span>
                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                        data-bs-title="Realisasi dapat diisi setelah Mulai Belajar">
                                        <button class="btn btn-success" id="btn_fill_realization" data-bs-toggle="modal"
                                            {{ $isRealization ? '' : 'disabled' }} data-bs-target="#fillRealizationModal">
                                            Isi Realisasi
                                        </button>
                                    </span>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editMeetingModal">
                                        Ubah Pertemuan
                                    </button>
                                </div>
                            @endrole
                            <div class="d-grid flex-grow-1 w-100 gap-3">
                                <div class="d-flex flex-column gap-3 w-100 flex-grow-1">
                                    @if (count($contents) > 0)
                                        @foreach ($contents as $content)
                                            @switch($content->data_type)
                                                @case('meeting_text')
                                                    @include(
                                                        'user.schedule.meeting.meeting-text-content-tem',
                                                        [
                                                            'content' => $content,
                                                        ]
                                                    )
                                                @break

                                                @case('material')
                                                    @include(
                                                        'user.schedule.meeting.material-content-item',
                                                        [
                                                            'content' => $content,
                                                        ]
                                                    )
                                                @break

                                                @case('task')
                                                    @include('user.schedule.meeting.task-content-item', [
                                                        'content' => $content,
                                                    ])
                                                @break

                                                @default
                                            @endswitch
                                        @endforeach
                                    @else
                                        {{-- empty data --}}
                                        <div class="d-flex justify-content-center mb-3 w-100">
                                            <div class="px-4 py-5 d-grid" style="justify-items: center">
                                                <img style="width: 120px; height: 120px"
                                                    src="{{ asset('assets/images/data-empty.png') }}" />
                                                <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                                                <p class="mb-0 text-center">Tidak ada konten yang tersedia pada pertemuan
                                                    ini.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @role('teacher')
                                    <div style="align-self: end">
                                        <div class="d-flex justify-content-end">
                                            <button class="btn d-flex align-items-center border justify-content-center gap-2"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#addContent"
                                                aria-expanded="false" aria-controls="addContent">
                                                <i class="fa-solid fa-plus"></i>
                                                Tambah Konten
                                            </button>
                                        </div>
                                        <div class="collapse" id="addContent">
                                            <div class="p-20">
                                                <div
                                                    class="d-flex gap-4 justify-content-center flex-wrap flex-column flex-sm-row">
                                                    <div
                                                        class="item d-flex flex-sm-column align-items-center row-gap-2 column-gap-3 flex-row">
                                                        <button class="btn border border-secondary-sublte"
                                                            data-bs-toggle="modal" data-bs-target="#addMeetingTextModal"
                                                            style="aspect-ratio: 1/1; width: fit-content; border-style:dashed!important">
                                                            <div class="p-1">
                                                                <img style="width:30px; height:30px" class="theme-aware-icon"
                                                                    src="{{ asset('assets/icons/text.png') }}" />
                                                            </div>
                                                        </button>
                                                        <p class="mb-0 text-center">Text</p>
                                                    </div>
                                                    <div
                                                        class="item d-flex flex-sm-column align-items-center row-gap-2 column-gap-3 flex-row">
                                                        <button class="btn border border-secondary-sublte"
                                                            data-bs-toggle="modal" data-bs-target="#addMaterialModal"
                                                            style="aspect-ratio: 1/1; width: fit-content; border-style:dashed!important">
                                                            <div class="p-1">
                                                                <img style="width:30px; height:30px" class="theme-aware-icon"
                                                                    src="{{ asset('assets/icons/agenda.png') }}" />
                                                            </div>
                                                        </button>
                                                        <p class="mb-0 text-center">Materi</p>
                                                    </div>
                                                    <div
                                                        class="item d-flex flex-sm-column align-items-center row-gap-2 column-gap-3 flex-row">
                                                        <button class="btn border border-secondary-sublte"
                                                            data-bs-toggle="modal" data-bs-target="#addTaskModal"
                                                            style="aspect-ratio: 1/1; width: fit-content; border-style:dashed!important">
                                                            <div class="p-1">
                                                                <img style="width:30px; height:30px" class="theme-aware-icon"
                                                                    src="{{ asset('assets/icons/task.png') }}" />
                                                            </div>
                                                        </button>
                                                        <p class="mb-0 text-center">Tugas</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                            </div>
                        </div>
                    </div>
                </div>
                @include('user.schedule.meeting.modal')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const statuses = @json($attendanceValue);
        const defaultDescription = `{!! addslashes($meeting->description) !!}`;
        const defaultSubSubjectMatter = `{!! addslashes($meeting->teaching_journal?->sub_subject_matter) !!}`;
        const defaultAdditionalNote = `{!! addslashes($meeting->teaching_journal?->additional_note) !!}`;
    </script>
    <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <script src={{ asset('assets/js/schedule-meeting.js') }}></script>
    <script src={{ asset('assets/js/schedule-meeting-material.js') }}></script>
    <script src={{ asset('assets/js/schedule-meeting-task.js') }}></script>
    <script src={{ asset('assets/js/schedule-meeting-text.js') }}></script>
    <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
@endsection
