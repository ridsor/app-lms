@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Tugas')

@section('styles')
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

        .content-item {
            transition: all .3s;
        }

        .content-item:hover,
        .content-item:focus {
            background: rgba(0, 0, 0, .1);
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid p-0">
        <div class="page-title">
            <div class="row p-2 p-sm-0">
                <div class="col-sm-6">
                    <h3>Tugas</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('user.schedule.index') }}">
                                {{ $task->meeting->schedule->subject->code }}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="container-fluid e-category p-0">
            <div class="row g-2 mb-3">
                <div class="col-12 p-0">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="row ">
                                        <div class="col-12">
                                            <label class="form-label">Mata Pelajaran</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ strtoupper($task->meeting->schedule->subject->name) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Kode Matpel</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $task->meeting->schedule->subject->code }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Pertemuan</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $task->meeting->meeting_at }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Kelas</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $task->meeting->schedule->class->name }}{{ $task->meeting->schedule->class->level }}
                                                </span>
                                            </p>
                                        </div>
                                        @if ($task->meeting->schedule->class->major)
                                            <div class="col-12">
                                                <label class="form-label">Jurusan</label>
                                                <p class="c-o-light f-w-600">
                                                    <span>
                                                        {{ $task->meeting->schedule->class->major->name }}
                                                    </span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label">Tipe Tugas</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ Helper::getTaskTypeLabel($task->type) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Waktu Mulai</label>
                                            <div class="c-o-light f-w-600">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="icon d-inline-flex justify-content-center align-items-center">
                                                        <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                                    </span>
                                                    <span class="mb-0 ms-2"
                                                        id="date">{{ $task?->start_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Waktu Selesai</label>
                                            <div class="c-o-light f-w-600">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="icon d-inline-flex justify-content-center align-items-center">
                                                        <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                                    </span>
                                                    <span class="mb-0 ms-2"
                                                        id="date">{{ $task?->end_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Pengiriman
                                                Terlambat</label>
                                            <div class="c-o-light f-w-600">
                                                <div class="form-check form-switch" style="pointer-events: none">
                                                    <input class="form-check-input" disabled
                                                        {{ $task->allow_late_submission ? 'checked' : '' }}
                                                        name="allow_late_submission" type="checkbox" role="switch">
                                                </div>
                                            </div>
                                        </div>
                                        @if ($task?->late_submission_time)
                                            <div class="col-12">
                                                <label class="form-label">Waktu Terlambat</label>
                                                <div class="c-o-light f-w-600">
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="icon d-inline-flex justify-content-center align-items-center">
                                                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                                        </span>
                                                        <span class="mb-0 ms-2"
                                                            id="date">{{ $task?->late_submission_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                                                    </div>
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
            <div class="mb-3 px-3">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <a {{ $submission->onFirstPage() ? 'aria-disabled="true"' : '' }} role="button"
                        {{ $submission->onFirstPage() ? '' : 'href=' . route('user.task.evaluation', ['task_id' => $task->id, 'page' => $submission->currentPage() - 1]) }}
                        class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ $submission->onFirstPage() ? 'disabled' : '' }}">
                        <i data-feather="chevron-left" style="width:18px; height: 18px"></i>
                    </a>
                    <div class="d-flex flex-column align-items-center justify-content-center px-2">
                        <p class="mb-0 fw-medium text-break">
                            {{ $task_submission->student->name }}
                        </p>
                        <p class="f-light mb-0 text-break">{{ $task_submission->student->nis }}</p>
                    </div>
                    <a {{ $submission->hasMorePages() ? 'href=' . route('user.task.evaluation', ['task_id' => $task->id, 'page' => $submission->currentPage() + 1]) : '' }}
                        role="button" {{ !$submission->hasMorePages() ? 'aria-disabled="true"' : '' }}
                        class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ !$submission->hasMorePages() ? 'disabled' : '' }}">
                        <i data-feather="chevron-right" style="width:18px; height: 18px"></i>
                    </a>
                </div>
            </div>
            <div class="row g-2 mb-4">
                <div class="col-12 col-lg-7 col-xl-8 p-0 order-2 order-lg-1">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="col-12">
                                        <label class="form-label">Judul</label>
                                        <p class="c-o-light f-w-600">
                                            <span>
                                                {{ $task->title }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div>
                                        <label class="form-label">Deskripsi</label>
                                    </div>
                                    @if ($task?->description)
                                        <div class="ql-editor text-wrap h-auto p-0">
                                            {!! $task?->description !!}
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                                @if ($task->file_path)
                                    <div class="col-12">
                                        <div class="mt-3 view_file_path">
                                            <div
                                                class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1">
                                                <div
                                                    style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                                                    <i class="fa fa-file text-primary fs-2"></i>
                                                </div>
                                                <div class="fw-medium text-break" style="font-size: .8rem">
                                                    {{ $task?->file_name . ' (' . number_format($task?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                                                </div>
                                                <a href="{{ route('user.task.file.download', ['task_id' => $task->id]) }}"
                                                    style="width: 38px; height: 38px;"
                                                    class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                                                    <i data-feather="download" style="width: 20px; height: 20px"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5 col-xl-4 order-1 order-lg-2">
                    <form method="POST" id="form_evaluation" data-tasksubmission-id="{{ $task_submission->id }}"
                        data-id={{ $task->id }}>
                        <div class="p-3">
                            <div class="d-flex mb-3 justify-content-between align-items-center">
                                <p class="mb-0 fw-semibold fs-6">Nilai</p>
                                <input class="form-control text-center" type="number" style="width: 70px"
                                    value="{{ $task_submission->formatted_score }}" name="score" step="0.1" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Waktu Pengumpulan</label>
                                <div class="c-o-light f-w-600">
                                    <div class="d-flex align-items-center">
                                        <span class="icon d-inline-flex justify-content-center align-items-center">
                                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                        </span>
                                        <span class="mb-0 ms-2"
                                            id="date">{{ optional($task?->late_submission_time)->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konten</label>
                                <div class="d-flex flex-column gap-2">
                                    @foreach ($task_submission->contents as $content)
                                        @switch($content['type'])
                                            @case('file')
                                                <a href="{{ route('user.task.evaluation.file.download', ['task_submission_id' => $task_submission->id, 'id' => $content['id']]) }}"
                                                    class="w-100 d-block">
                                                    <div
                                                        class="content-item d-flex align-items-center gap-1 justify-content-between file border w-100 rounded-2 p-3 file-preview-item">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;margin-right:5px;">
                                                                <i class="{{ Helper::getContentIcon($content['name']) }}"
                                                                    style="color:#1976d2;font-size:18px;"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-break text-start"
                                                                    style="font-size:0.8rem; color:var(--bs-body-color)">
                                                                    {{ $content['name'] }}{{ ' (' . number_format($content['size'] / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @break

                                            @case('link')
                                                <button onclick="handleCopyText('{{ $content['url'] }}')"
                                                    class="btn p-0 border-0 bg-transparent w-100" type="button">
                                                    <div
                                                        class="content-item d-flex align-items-center gap-1 justify-content-between file border w-100 rounded-2 p-3 file-preview-item">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;margin-right:5px;">
                                                                <i class="fa fa-link text-info"
                                                                    style="color:#1976d2;font-size:18px;"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-break text-start" style="font-size:0.8rem">
                                                                    {{ $content['url'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </button>
                                            @break

                                            @default
                                        @endswitch
                                    @endforeach
                                </div>
                            </div>
                            @if (count($task_submission->group_members) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Tag Anggota Kelompok</label>
                                    <ul class='d-flex flex-column gap-1'>
                                        @foreach ($task_submission->group_members as $item)
                                            <li class="d-flex align-items-center gap-1">
                                                <i data-feather="minus" style="width:15px; height: 15px"></i>
                                                <span class="text-break">
                                                    {{ $item }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">Feedback</label>
                                <textarea class="form-control" rows="3" name="feedback">{{ $task_submission->feedback }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('user.task.collection', ['task_id' => $task->id]) }}"
                                    class="btn btn-outline-secondary" type="button" aria-label="Close">
                                    Kembali
                                </a>
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/task-evaluation.js') }}"></script>
@endsection
