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
            <div class="row g-0 mb-4">
                <div class="col-12 p-0">
                    <div class="card rounded-responsive">
                        <div class="card-body">
                            <ul class="d-flex gap-2 row-gap-3 flex-wrap">
                                <li>
                                    <a href="{{ !Request::routeIs('user.task.show') ? route('user.task.show', ['task_id' => $task->id]) : '' }}"
                                        class="py-2 px-2 {{ Request::routeIs('user.task.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Info
                                        Tugas</a>
                                </li>
                                <li>
                                    <a href="{{ !Request::routeIs('user.task.collection') ? route('user.task.collection', ['task_id' => $task->id]) : '' }}"
                                        class="py-2 px-2 {{ Request::routeIs('user.task.collection') ? 'border-bottom border-primary' : 'text-secondary' }}">Pengumpulan</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center  mb-3">
                                @if ($task->not_yet_rated)
                                    <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum
                                        dinilai <span class="badge ms-1 badge-danger">{{ $task->not_yet_rated }}</span>
                                    </span>
                                @endif
                                <div class="d-flex justify-content-end flex-grow-1 align-items-center gap-2">
                                    @can('material.*')
                                        <button
                                            class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                                            style="width: 38px; height: 38px;"
                                            onclick="handleEditTask(event, {{ $task->id }})">
                                            <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                                        </button>
                                        <button
                                            class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                                            style="width: 38px; height: 38px;"
                                            data-redirect="{{ '/jadwal/' . $task->meeting->schedule->subject->code . '/pertemuan/' . $task->meeting->meeting_at }}"
                                            onclick="handleDeleteTask(event, {{ $task->id }})">
                                            <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="row ">
                                        <div class="col-12">
                                            <label class="form-label">Judul</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $task->title }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Mata Pelajaran</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $task->meeting->schedule->subject->name }}
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
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row g-2">
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
                                        <div class="col-12">
                                            <label class="form-label">Tipe Tugas</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ Helper::getTaskTypeLabel($task->type) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-6">
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
                                        <div class="col-12 col-md-6">
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
                                                        <span class="icon"><i data-feather="calendar"
                                                                style="width:18px; height: 18px"></i></span>
                                                        <span class="mb-0 ms-2"
                                                            id="date">{{ $task?->late_submission_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if ($task->file_path)
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
                            @endif
                        </div>
                    </div>
                    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content category-popup">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Tugas</h5>
                                    <button class="btn-close" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0 custom-input">
                                    <div class="text-start">
                                        <div class="p-20">
                                            <form class="material-form needs-validation row g-3" method="POST"
                                                novalidate="" id="editTaskForm">
                                                <input type="hidden" name="deletedFile" value="0">
                                                <div class="col-12 col-md-6">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label" for="editMaterialTitle">Judul<span
                                                                    class="txt-danger">*</span></label>
                                                            <input class="form-control" id="editMaterialTitle"
                                                                type="text" placeholder="Tulis judul" name="title">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editTaskType">Jenis Tugas<span
                                                                    class="txt-danger">*</span></label>
                                                            <select class="form-select" id="editTaskType" name="type">
                                                                <option value="">Jenis Tugas</option>
                                                                @foreach ($taskType as $item)
                                                                    <option value="{{ $item['value'] }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex flex-column flatpicker-form">
                                                                <label class="form-label" for="startDate">Waktu Mulai<span
                                                                        class="txt-danger">*</span></label>
                                                                <input class="form-control flatpicker"
                                                                    id="editTaskStartTime" type="date"
                                                                    placeholder="Pilih waktu mulai" name="start_time"
                                                                    data-language="id">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex flex-column flatpicker-form">
                                                                <label class="form-label" for="endDate">Waktu
                                                                    Selesai<span class="txt-danger">*</span></label>
                                                                <input class="form-control flatpicker" autocomplete="off"
                                                                    id="editTaskEndTime" type="date"
                                                                    placeholder="Pilih waktu selesai" name="end_time"
                                                                    data-language="id">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label"
                                                                for="editAllowLateSubmission">Pengiriman
                                                                Terlambat</label>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input"
                                                                    id="editAllowLateSubmission"
                                                                    name="allow_late_submission" type="checkbox"
                                                                    role="switch">
                                                            </div>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-12 lateSubmission" style="display: none;">
                                                            <div class="d-flex flex-column flatpicker-form">
                                                                <label class="form-label" for="endDate">Batas Waktu
                                                                    Terlambat</label>
                                                                <input class="form-control flatpicker" autocomplete="off"
                                                                    id="editLateSubmissionTime" type="date"
                                                                    placeholder="Pilih batas waktu terlambat"
                                                                    name="late_submission_time" data-language="id">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <div class="taskFile">
                                                                <label class="form-label">File</label>
                                                                <div class="info text-danger mb-1"
                                                                    style="font-size: 12px;">
                                                                    Ukuran maksimal file 5mb
                                                                </div>
                                                                <div
                                                                    class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                                                                    <label for="editTaskFile"
                                                                        class="d-flex align-items-center mb-0 w-100"
                                                                        style="cursor:pointer;">
                                                                        <span
                                                                            style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                                            <i class="fa fa-upload text-primary fs-5"></i>
                                                                        </span>
                                                                        <span
                                                                            style="color:#b0b0b0; font-weight:500;">Unggah
                                                                            File</span>
                                                                    </label>
                                                                </div>
                                                                <input type="file" class="form-control file_path"
                                                                    id="editTaskFile"
                                                                    accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                                                    name="file_path" hidden>
                                                                <div id="file-preview" class="d-flex flex-column gap-1">
                                                                </div>
                                                            </div>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label"
                                                                for="editTaskDescription">Deskripsi<span
                                                                    class="txt-danger"></span></label>
                                                            <div class="toolbar-box">
                                                                <div id="editTaskToolbar">
                                                                    <button class="ql-bold">Bold</button>
                                                                    <button class="ql-italic">Italic</button>
                                                                    <button class="ql-underline">underline</button>
                                                                    <button class="ql-strike">Strike</button>
                                                                    <button class="ql-list" value="ordered">List</button>
                                                                    <button class="ql-list" value="bullet"></button>
                                                                    <button class="ql-indent" value="-1"></button>
                                                                    <button class="ql-indent" value="+1"></button>
                                                                    <button class="ql-link"></button>
                                                                </div>
                                                                <div id="editTaskDescriptionQuill"></div>
                                                                <input type="hidden" id="editTaskDescription"
                                                                    name="description" class="quill">
                                                            </div>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            Batal
                                                        </button>
                                                        <button class="btn btn-primary" type="submit"
                                                            id="submit">Simpan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
    <script src={{ asset('assets/js/schedule-meeting-task.js') }}></script>
@endsection
