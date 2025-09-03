@extends('layouts.user.app')

@section('title', 'Bank Soal')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Bank Soal
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">
                            <span>
                                Bank Soal
                            </span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="e-category">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="card rounded-responsive">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Filter</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                <div class="col-md-4 col-xl">
                                    <label class="form-label" for="subject-filter">Mata Pelajaran</label>
                                    <select class="selectpicker search-picker filter" data-live-search="true"
                                        id="subject-filter">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->name }}">{{ $subject->name }} -
                                                {{ $subject->curriculum->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto d-flex justify-content-start align-items-end">
                                    <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 px-0">
                    <div class="card rounded-responsive">
                        <div class="card-header card-no-border text-end">
                            <div class="card-header-right-icon">
                                <button class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#addQuestionBankModal"><i class="fa fa-plus pe-2"></i>Tambah
                                </button>
                                <div class="row g-3 justify-content-end align-items-center"
                                    id="question-bank-action-buttons">
                                    <div class="col-auto">
                                        <span>
                                            <span class="me-1" id="selected-count">0</span> dipilih
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <a id="delete-selected"
                                            class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-2 py-2"
                                            style="cursor: pointer;">
                                            <i class="fa-solid fa-trash-can txt-danger"></i>
                                        </a>
                                    </div>
                                </div>
                                @include('user.question-bank.modal')
                            </div>
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="list-product list-category">
                                <div class="recent-table table-responsive custom-scrollbar">
                                    <table class="table table-bordered" id="question-bank-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="checkbox-checked">
                                                        <div
                                                            class="form-check d-flex justify-content-center align-items-center">
                                                            <input class="form-check-input" id="select-all" type="checkbox"
                                                                style="width: 12px; height: 12px;" value>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th><span class="c-o-light f-w-600">Judul</span></th>
                                                <th><span class="c-o-light f-w-600">Mata Pelajaran</span></th>
                                                <th><span class="c-o-light f-w-600">Soal</span></th>
                                                <th><span class="c-o-light f-w-600">Waktu</span></th>
                                                <th><span class="c-o-light f-w-600"></span></th>
                                            </tr>
                                        </thead>
                                    </table>
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
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/question-bank.js') }}"></script>
@endsection
