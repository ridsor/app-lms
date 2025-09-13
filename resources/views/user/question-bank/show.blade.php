@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Tugas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/glightbox.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
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
                    <h3>Bank Soal</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.question-bank.index') }}">
                                Bank Soal
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="container-fluid e-category pb-4">
            <div class="row g-0 mb-4">
                <div class="col-12 p-0">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Judul</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $question_bank->title }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Deskripsi</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $question_bank->description }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label">Jumlah Soal</label>
                                    <p class="c-o-light f-w-600">
                                        <span>
                                            {{ $question_bank->questions_count }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
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
                <div class="col-12 col-md-auto d-flex justify-content-end">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#addQuestionkModal"
                        class="btn btn-primary gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
                        <i data-feather="plus" style="width:18px; height:18px"></i>
                        <span>Tambah Soal</span>
                    </button>
                </div>
            </div>
            <div class="row g-4 flex-column list-question px-md-3">
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
            @include('user.question-bank.modal-show')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
    <script src={{ asset('assets/js/glightbox.min.js') }}></script>
    <script src={{ asset('assets/js/question-bank-show.js') }}></script>
@endsection
