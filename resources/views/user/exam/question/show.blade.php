@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Ujian')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/glightbox.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <style>
        .answer-option {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .answer-option input[type="text"] {
            flex: 1;
            margin-left: 10px;
            margin-right: 10px;
        }

        .remove-option {
            cursor: pointer;
            color: #777;
            font-size: 20px;
        }

        .remove-option:hover {
            color: red;
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.exam.show', ['id' => $exam->id]) }}">
                                Ujian
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Soal
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        @include('user.exam.menu')
        <div class="container-fluid e-category">
            <div class="row g-0 mb-3">
                <div class="col-12">
                    <h1 class="mb-2">Soal</h1>
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="border rounded-2 p-3 card mb-0">
                                <p class="mb-2">Jumlah Soal</p>
                                <span class="fs-3">{{ $exam->questions_count }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded-2 p-3 card mb-0">
                                <p class="mb-2">Jumlah Poin</p>
                                <span class="fs-3">{{ $exam->questions_sum_question_points ?? 0 }}</span>
                            </div>
                        </div>
                        @can('exam.create')
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#copyQuestionModal"
                                        class="btn btn-primary gap-2 px-3 btn-sm d-flex justify-content-center align-items-center">
                                        <i class="fa-solid fa-copy"></i>
                                        <span>Salin Bank Soal</span>
                                    </button>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="row g-3 align-items-center mb-3">
                <form action="" class="d-flex align-items-center col gap-2">
                    <input type="search" class="form-control w-100" value="{{ request()->query('search') }}"
                        placeholder="Cari soal" name="search" id="globalSearch" />
                    <button class="btn btn-primary gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
                        <i data-feather="search" style="width:18px; height:18px"></i>
                    </button>
                </form>
                @can('exam.create')
                    <div class="col-12 col-md-auto d-flex justify-content-end">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addQuestionkModal"
                            class="btn btn-primary gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
                            <i data-feather="plus" style="width:18px; height:18px"></i>
                            <span>Tambah Soal</span>
                        </button>
                    </div>
                @endcan
            </div>
            <div class="row g-4 flex-column list-question px-md-3 mb-3">
                @if (count($questions) > 0)
                    @foreach ($questions as $index => $question)
                        @include('user.question.item', [
                            'question' => $question,
                            'number' => ($questions->currentPage() - 1) * $questions->perPage() + $index + 1,
                        ])
                    @endforeach
                    <div class="pagination-wrapper w-100">
                        {{ $questions->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    {{-- empty data --}}
                    <div class="d-flex justify-content-center mb-3 w-100">
                        <div class="px-4 py-5 d-grid" style="justify-items: center">
                            <img style="width: 120px; height: 120px" src="{{ asset('assets/images/data-empty.png') }}" />
                            <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                            <p class="mb-0 text-center">Belum ada soal.</p>
                        </div>
                    </div>
                @endif
            </div>
            @include('user.exam.question.modal')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
    <script src={{ asset('assets/js/glightbox.min.js') }}></script>
    <script src={{ asset('assets/js/exam-question.js') }}></script>
@endsection
