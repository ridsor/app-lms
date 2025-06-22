@extends('layouts.user.app')

@section('title', 'Manajemen Periode')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/period-crud.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Periode</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Periode</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body e-category">
                        <div class="row g-3 custom-input">
                            <div class="col-md-6 col-lg"> <label class="form-label" for="datetime-local">Dari: </label>
                                <div class="input-group flatpicker-calender">
                                    <input class="datepicker-here form-control digits" type="text" data-language="id"
                                        value="{{ request('start_date') }}" id="filterStartDate" placeholder="dd/mm/yyyy"
                                        type="date">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg"> <label class="form-label" for="datetime-local3">Sampai: </label>
                                <div class="input-group flatpicker-calender">
                                    <input class="datepicker-here form-control digits" type="text" data-language="id"
                                        value="{{ request('end_date') }}" id="filterEndDate" placeholder="dd/mm/yyyy"
                                        type="date">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg"><label class="form-label">Semester</label><select
                                    class="form-select" aria-label="Pilih semester" id="filterSemester">
                                    <option value="" selected="">Semua</option>
                                    <option value="even" @if (request('semester') == 'even') selected @endif>Genap</option>
                                    <option value="odd" @if (request('semester') == 'odd') selected @endif>Ganjil</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg">
                                <div class="d-flex justify-content-end flex-column h-100 w-100">
                                    <select class="form-select" id="sort" name="sort">
                                        <option value="" selected="">Urutkan</option>
                                        <option value="created_desc" @if (request('sort') == 'created_desc') selected @endif>
                                            Terbaru
                                        </option>
                                        <option value="created_asc" @if (request('sort') == 'created_asc') selected @endif>
                                            Terlama
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex justify-content-end flex-column h-100 w-100">
                                    <button class="btn btn-primary f-w-500" id="filterBtn">Terapkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 col-lg-6">
                <div class="card height-equal">
                    <div class="card-header">
                        <h4>Daftar Periode</h4>
                    </div>
                    <div class="card-body">
                        <div id="periodsList">
                            @include('user.periods.list', ['periods' => $periods])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-7 col-lg-6">
                <div class="card height-equal">
                    <div class="card-header">
                        <h4 id="formTitle">Tambah Periode</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 custom-input theme-form" novalidate="novalidate" id="periodForm">
                            @csrf
                            @include('user.periods.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script>
    <script src="{{ asset('assets/js/bookmark/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/period-crud.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
@endsection
