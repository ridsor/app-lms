@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Tugas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/tagify.css') }}">
    <style>
        .view_file_path .File {
            background: #f5f6f9 !important;
        }

        .dark-only .view_file_path .File {
            background: #1d1e26 !important;
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid px-0">
        <div class="page-title">
            <div class="row p-2 p-sm-0">
                <div class="col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('user.task.index') }}">Tugas</a></li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="e-category select2-drpdwn">
            <div class="row  g-0">
                <div class="col-12 col-lg-8">
                    <div class="card rounded-responsive">
                        <div class="card-body p-0">
                            <div class="row g-0 align-items-center w-100 border-bottom">
                                <div class="col-12 col-md-auto">
                                    <div class="d-flex flex-column align-items-center py-3 px-4">
                                        <div>
                                            <img style="width:30px; height:30px" class="theme-aware-icon"
                                                src="{{ asset('assets/icons/task.png') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col flex-grow-1">
                                    <div class="py-0 pb-3 px-3 px-md-0 py-md-3 me-md-5">
                                        <p class="fw-medium mb-0 fs-4">{{ $task->title }}</p>
                                        <p class="mb-2">
                                            <span class="badge m-0 badge-light-primary px-2 py-1">{{ $task->status }}</span>
                                        </p>
                                        <div style="font-size: .8rem;" class="text-secondary">
                                            @if ($task->is_late_submission_allowed_with_time)
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="icon"><i data-feather="clock"
                                                            style="width:18px; height: 18px"></i></span>
                                                    <span class="mb-0">Batas pengumpulan tugas terlambat:
                                                        {{ $task->late_submission_time->translatedFormat('d M Y H:i') }}
                                                        WIT</span>
                                                </div>
                                            @endif
                                            <div class="row g-0 g-sm-2">
                                                <div
                                                    class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                                    <div class="d-flex align-items-center">
                                                        <span class="icon"><i data-feather="calendar"
                                                                style="width:18px; height: 18px"></i></span>
                                                        <span
                                                            class="mb-0 ms-2">{{ $task->start_time->translatedFormat('d M Y') }}</span>
                                                    </div>
                                                    <div>&middot;</div>
                                                    <span>
                                                        {{ $task->start_time->translatedFormat('H:i') }} WIT
                                                    </span>
                                                </div>
                                                <div
                                                    class="col-12 col-md-auto d-flex justify-content-center align-items-center">
                                                    <span class="icon d-flex align-items-center justify-content-center">
                                                        <i data-feather="minus" style="width:18px; height: 18px"></i>
                                                    </span>
                                                </div>
                                                <div
                                                    class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                                    <div class="d-flex align-items-center">
                                                        <span class="icon"><i data-feather="calendar"
                                                                style="width:18px; height: 18px"></i></span>
                                                        <span
                                                            class="mb-0 ms-2">{{ $task->end_time->translatedFormat('d M Y') }}</span>
                                                    </div>
                                                    <div>&middot;</div>
                                                    <span>
                                                        {{ $task->end_time->translatedFormat('H:i') }} WIT
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-3">
                                <div class="d-flex px-3 mb-2 justify-content-between align-items-center">
                                    <p class="fw-medium mb-0">Tugas {{ Helper::getTaskTypeLabel($task->type) }}</p>
                                    @if ($task->not_yet_rated)
                                        <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum
                                            dinilai
                                            <span class="badge ms-1 badge-danger">{{ $task->not_yet_rated }}</span></span>
                                    @endif
                                </div>
                                <div class="description mb-2">
                                    @if ($task->description)
                                        <div class="ql-editor text-wrap h-auto">
                                            {!! $task->description !!}
                                        </div>
                                    @endif
                                </div>
                                @if ($task->file_path)
                                    <div class="mb-3 view_file_path">
                                        <div
                                            class="File py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1">
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
                    </div>
                </div>
                <div class="col-12 col-lg-4 position-static d-flex flex-column">
                    <div class="h-100 flex-grow-1 w-100">
                        <div class="position-relative overflow-y-auto overflow-x-hidden" id="my-sticky">
                            <div class="p-3">
                                @if ($task->value_displayed && $task_submission->formatted_score)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <p class="fs-5 mb-0">Nilai</p>
                                        <p class="fs-6 badge m-0 badge-light-primary px-2 py-1">
                                            {{ $task_submission->formatted_score }}</p>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <p class="fs-5 mb-0">Tugas</p>
                                    <span
                                        class="text-{{ $task_submission?->submitted_at ? 'secondary' : 'danger' }}">{{ $task_submission?->submitted_at ? 'Diserahkan' : 'Belum Diserahkan' }}</span>
                                </div>
                                @if ($task->type == 'group')
                                    <div class="mb-2">
                                        <p class="fs-7 mb-1">Tag Anggota Kelompok</p>
                                        <form>
                                            <textarea id="group_members" class="w-100" placeholder="Masukan anggota kelompokmu"
                                                @cannot('task_submission.edit') disabled @endcannot>{{ $task_submission?->group_members }}</textarea>
                                        </form>
                                    </div>
                                @endif
                                <div class="d-flex flex-column gap-1 mb-2" id="task-submission-preview">
                                    <div class="d-flex justify-content-center align-items-center p-2 w-100">
                                        <i class="fa-solid fa-arrows-rotate fa-spin"></i>
                                    </div>
                                </div>
                                @can('task_submission.edit')
                                    <div class="mb-3">
                                        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#addContent" aria-expanded="false" aria-controls="addContent">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <i data-feather="plus" style="width: 20px; height:20px"></i>
                                                <span>Tambah</span>
                                            </div>
                                        </button>
                                        <div class="collapse" id="addContent">
                                            <div class="d-flex flex-column w-100">
                                                <label
                                                    class="mb-0 btn btn-light rounded-0 border-0 text-inherit w-100 text-center p-2 tasksubmission-content-item">
                                                    <p class="mb-0">File</p>
                                                    <input type="file" hidden multiple
                                                        accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                                        id="task-submission-content-file">
                                                </label>
                                                <button data-bs-toggle="modal" data-bs-target="#linkModal"
                                                    class="btn btn-light rounded-0 border-0 text-inherit w-100 text-center p-2 tasksubmission-content-item">
                                                    <p class="mb-0">Link</p>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100 mb-3" id="submit-task" disabled
                                        data-id="{{ $task->id }}">
                                        Serahkan
                                    </button>
                                @endcan
                                @if ($task->allow_late_submission)
                                    <p class="fst-italic text-center mb-3" style="font-size: .8rem">
                                        Tugas tidak dapat
                                        diserahkan
                                        setelah batas waktu
                                    </p>
                                @endif
                                @if ($task_submission?->feedback)
                                    <hr />
                                    <div class="mb-3">
                                        <label class="form-label mb-0">Feedback</label>
                                        <div class="feedback mb-2">
                                            <div class="ql-editor text-wrap h-auto w-100 px-0">
                                                {!! $task_submission->feedback !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="linkModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content category-popup">
                        <div class="modal-body">
                            <div class="text-start">
                                <p class="fs-6 mb-0">Tambahkan Link</p>
                                <form class="row g-3" method="POST" id="task-submission-content-link">
                                    <div class="col-12">
                                        <label class="form-label" for="addLink">Link<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="addLink" type="text" placeholder=""
                                            autocomplete="off" name="link">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-bs-dismiss="modal" aria-label="Close">
                                                Batal
                                            </button>
                                            <button class="btn btn-primary" type="submit"
                                                id="submit">Tambahkan</button>
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
@endsection
@section('scripts')
    <script>
        const members = @json($members);
        let taskSubmissioncontents = @json($task_submission?->contents) ?? [];
        const deleteContent = [];
    </script>
    <script src="{{ asset('assets/js/select2/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    <script src="{{ asset('assets/js/content-upload.js') }}"></script>
    <script src={{ asset('assets/js/task-submission.js') }}></script>
@endsection
