@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Ujian')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
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
                    <h3>Ujian</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('user.exam.index') }}">
                                Ujian
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
                                                    {{ strtoupper($exam->schedule->subject->name) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Kode Matpel</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $exam->schedule->subject->code }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Kelas</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ $exam->schedule->class->name }}{{ $exam->schedule->class->level }}
                                                </span>
                                            </p>
                                        </div>
                                        @if ($exam->schedule->class->major)
                                            <div class="col-12">
                                                <label class="form-label">Jurusan</label>
                                                <p class="c-o-light f-w-600">
                                                    <span>
                                                        {{ $exam->schedule->class->major->name }}
                                                    </span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label">Tipe Ujian</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ Helper::getExamTypeLabel($exam->type) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Sifat</label>
                                            <p class="c-o-light f-w-600">
                                                <span>
                                                    {{ Helper::getExamModeLabel($exam->exam_mode) }}
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
                                                        id="date">{{ $exam?->start_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
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
                                                        id="date">{{ $exam?->end_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
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
                                                {{ $exam->title }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div>
                                        <label class="form-label">Deskripsi</label>
                                    </div>
                                    @if ($exam?->description)
                                        <div class="ql-editor text-wrap h-auto p-0">
                                            {!! $exam?->description !!}
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5 col-xl-4 order-1 order-lg-2">
                    @php
                        $now = now();
                        $is_exam_available =
                            $exam->start_time <= $now &&
                            $exam->end_time >= $now &&
                            $exam_result?->status != 'completed' &&
                            $exam->questions_count > 0;
                    @endphp
                    <div class="p-3">
                        <div class="mb-2">
                            {!! Helper::getExamStatusLabel($exam_result?->status) !!}
                        </div>

                        @if ($exam_result?->status == 'completed')
                            <div class="mb-3">
                                <a href="{{ route('user.exam.workmanship.result', $exam->id) }}"
                                    class="btn btn-primary w-100">Lihat Hasil</a>
                            </div>
                        @endif

                        <div class="d-flex mb-3 justify-content-between align-items-center">
                            <p class="mb-0 fw-semibold fs-6">Nilai</p>
                            <input class="form-control text-center" type="number" style="width: 70px" disabled
                                value="{{ $exam_result?->formatted_score }}" name="score" step="0.1" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu Pengerjaan</label>
                            <div class="c-o-light f-w-600">
                                <div class="d-flex align-items-center">
                                    <span class="icon d-inline-flex justify-content-center align-items-center">
                                        <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                    </span>
                                    @if ($exam_result?->start_time && $exam_result?->end_time)
                                        <div class="d-flex flex-column">
                                            <span class="mb-0 ms-2" id="date">Mulai
                                                {{ $exam_result?->start_time->translatedFormat('j M Y H:i') . ' WIT' ?: '-' }}</span>
                                            <span class="mb-0 ms-2" id="date">Selesai
                                                {{ $exam_result?->end_time->translatedFormat('j M Y H:i') . ' WIT' ?: '-' }}</span>
                                        </div>
                                    @else
                                        <span class="mb-0 ms-2" id="date">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('user.exam.index') }}" class="btn btn-outline-secondary" type="button"
                                aria-label="Close">
                                Kembali
                            </a>

                            <button data-href="{{ route('user.exam.workmanship', $exam->id) }}" type="button"
                                id="start-exam-btn" data-id="{{ $exam->id }}" class="btn btn-primary"
                                {{ $is_exam_available ?: 'disabled' }}>Mulai</button>
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
    <script src="{{ asset('assets/js/exam-info.js') }}"></script>
@endsection
