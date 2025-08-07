@php
    use App\Helpers\Helper;
@endphp

@props(['content'])

<div class="item d-flex w-100 border rounded-1 flex-column">
    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
        data-bs-target="#task-content-{{ $content->id }}" aria-expanded="false"
        aria-controls="task-content-{{ $content->id }}">
        <div class="row g-0 align-items-center w-100">
            <div class="col-12 col-md-auto">
                <div class="d-flex flex-column align-items-center py-3 px-4">
                    <div class="mb-2">
                        <img style="width:30px; height:30px" class="theme-aware-icon"
                            src="{{ asset('assets/icons/task.png') }}" />
                    </div>
                    <p class="mb-0 text-center">Tugas</p>
                </div>
            </div>
            <div class="col flex-grow-1">
                <div class="py-0 pb-3 px-3 px-md-0 py-md-3 me-md-5">
                    <p class="fw-medium mb-1">{{ $content->title }}</p>
                    <p class="mb-2">
                        <span class="badge m-0 badge-light-primary px-2 py-1">{{ $content->status }}</span>
                    </p>
                    <div style="font-size: .8rem;" class="text-secondary">
                        @if ($content->is_late_submission_allowed_with_time)
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="icon"><i data-feather="clock"
                                        style="width:18px; height: 18px"></i></span>
                                <span class="mb-0">Batas pengumpulan tugas terlambat:
                                    {{ $content->late_submission_time->translatedFormat('d M Y H:i') }} WIT</span>
                            </div>
                        @endif
                        <div class="row g-0 g-sm-2">
                            <div class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                <div class="d-flex align-items-center">
                                    <span class="icon"><i data-feather="calendar"
                                            style="width:18px; height: 18px"></i></span>
                                    <span class="mb-0 ms-2">{{ $content->start_time->translatedFormat('d M Y') }}</span>
                                </div>
                                <div>&middot;</div>
                                <span>
                                    {{ $content->start_time->translatedFormat('H:i') }} WIT
                                </span>
                            </div>
                            <div class="col-12 col-md-auto d-flex justify-content-center align-items-center">
                                <span class="icon d-flex align-items-center justify-content-center">
                                    <i data-feather="minus" style="width:18px; height: 18px"></i>
                                </span>
                            </div>
                            <div class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                <div class="d-flex align-items-center">
                                    <span class="icon"><i data-feather="calendar"
                                            style="width:18px; height: 18px"></i></span>
                                    <span class="mb-0 ms-2">{{ $content->end_time->translatedFormat('d M Y') }}</span>
                                </div>
                                <div>&middot;</div>
                                <span>
                                    {{ $content->end_time->translatedFormat('H:i') }} WIT
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-absolute top-0 end-0 col-auto top-middle">
                <div class="p-3 d-flex align-items-center justify-content-center">
                    <i class="svg-color position-static" data-feather="chevron-down"></i>
                </div>
            </div>
        </div>
    </button>
    <div class="accordion-collapse collapse w-100" id="task-content-{{ $content->id }}">
        <div class="d-flex justify-content-end gap-2 p-3">
            @can('material.*')
                <button class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                    style="width: 38px; height: 38px;" onclick="handleEditTask(event, {{ $content->id }})">
                    <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                </button>
                <button class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                    style="width: 38px; height: 38px;" onclick="handleDeleteTask(event, {{ $content->id }})">
                    <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                </button>
            @endcan
        </div>
        <div class="d-flex px-3 mb-2 justify-content-between align-items-center">
            <p class="fw-medium mb-0">Tugas {{ Helper::getTaskTypeLabel($content->type) }}</p>
            @if ($content?->not_yet_rated)
                <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum dinilai <span
                        class="badge ms-1 badge-danger">{{ $content->not_yet_rated }}</span></span>
            @endif
        </div>
        <div class="description mb-2">
            @if ($content->description)
                <div class="ql-editor text-wrap h-auto">
                    {!! $content->description !!}
                </div>
            @endif
        </div>
        @if ($content->file_path)
            <div class="mb-3 view_file_path">
                <div class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1">
                    <div style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                        <i class="fa fa-file text-primary fs-2"></i>
                    </div>
                    <div class="fw-medium text-break" style="font-size: .8rem">
                        {{ $content?->file_name . ' (' . number_format($content?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                    </div>
                    <a href="{{ route('user.task.file.download', ['task_id' => $content->id]) }}"
                        style="width: 38px; height: 38px;"
                        class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                        <i data-feather="download" style="width: 20px; height: 20px"></i>
                    </a>
                </div>
            </div>
        @endif
        @role('student')
            <div class="mb-3 px-3 d-flex justify-content-end">
                <a href="{{ route('user.tasksubmission.show', ['task_id' => $content->id]) }}"
                    class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                    Lihat
                </a>
            </div>
        @endrole
        @role('teacher')
            <div class="mb-3 px-3 d-flex justify-content-end">
                <a href="{{ route('user.task.show', ['task_id' => $content->id]) }}"
                    class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                    Lihat
                </a>
            </div>
        @endrole
    </div>
</div>
