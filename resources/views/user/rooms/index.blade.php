@extends('layouts.user.app')

@section('title', 'Manajemen Ruangan')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/room-crud.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Ruangan</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Ruangan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xxl-5 col-lg-6">
                <div class="card height-equal">
                    <div class="card-header">
                        <h4>Daftar Ruangan</h4>
                    </div>
                    <div class="card-body e-category">
                        <div class="job-filter">
                            <div class="row g-1">
                                <div class="col-md-8">
                                    <input class="form-control" id="search" name="search" type="text"
                                        placeholder="Cari ruangan..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="sort" name="sort">
                                        <option selected="">Urutkan</option>
                                        <option value="name_asc" @if (request('sort') == 'name_asc') selected @endif>A-Z
                                        </option>
                                        <option value="name_desc" @if (request('sort') == 'name_desc') selected @endif>Z-A
                                        </option>
                                        <option value="created_desc" @if (request('sort') == 'created_desc') selected @endif>
                                            Terbaru
                                        </option>
                                        <option value="created_asc" @if (request('sort') == 'created_asc') selected @endif>
                                            Terlama
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="roomsList" class="mt-3">
                            @include('user.rooms.list', ['rooms' => $rooms])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-7 col-lg-6">
                <div class="card height-equal">
                    <div class="card-header">
                        <h4 id="formTitle">Tambah Ruangan</h4>
                    </div>
                    <div class="card-body custom-input">
                        <form class="row g-3 needs-validation" novalidate="novalidate" id="roomForm">
                            @csrf
                            @include('user.rooms.fields')
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
    {{-- <script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script> --}}
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/room-crud.js') }}"></script>
@endsection
