@extends('layouts.user.app')

@section('title', 'Manajemen Kelas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Kelas</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Kelas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div><!-- Container-fluid starts-->
    <div class="container-fluid e-category">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md"><label class="form-label">Tingkat</label><select class="form-select"
                                    id="level-filter" aria-label="Select parent category">
                                    <option value="" selected>Pilih Tingkat</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}">{{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($majors->count() > 0)
                                <div class="col-md"><label class="form-label">Jurusan</label><select class="form-select"
                                        id="major-filter" aria-label="Select parent category">
                                        <option value="" selected>Pilih Jurusan</option>
                                        @foreach ($majors as $major)
                                            <option value="{{ $major->name }}">{{ $major->name }}</option>
                                        @endforeach
                                    </select>   
                                </div>
                            @endif
                            <div class="col d-flex justify-content-start align-items-end"><a
                                    class="btn btn-primary f-w-500 w-100" id="filter-btn" href="#!">Submit</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500 mb-2" href="#!" data-bs-toggle="modal"
                                data-bs-target="#addClassModal"><i class="fa fa-plus pe-2"></i>Tambah
                            </a>
                            <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Kelas</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate="" id="addClassForm">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="className">Nama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="className" type="text"
                                                                placeholder="Masukan nama kelas" name="name">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classLevel">Tingkat<span
                                                                    class="txt-danger">*</span></label><input
                                                                class="form-control" id="classLevel" type="text"
                                                                placeholder="Masukan tingkat kelas" name="level">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classMajor">Jurusan</label>
                                                                <select class="form-select" id="classMajor" aria-label="Select parent category" name="major_id">
                                                                    <option value="" selected>Pilih Jurusan</option>
                                                                    @foreach ($majors as $major)
                                                                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classCapacity">Kapasitas<span
                                                                class="txt-danger">*</span></label><input
                                                                class="form-control" id="classCapacity" type="number"
                                                                placeholder="Masukan kapasitas kelas" name="capacity">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="addClassSubmitBtn">Tambah +</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        {{-- Add Class Form --}}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Edit Kelas</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        {{-- Edit Class Form --}}
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate=""
                                                        id="editClassForm">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="className">Nama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="className" type="text"
                                                                placeholder="Masukan nama kelas" name="name">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classLevel">Tingkat<span
                                                                    class="txt-danger">*</span></label><input
                                                                class="form-control" id="classLevel" type="text"
                                                                placeholder="Masukan tingkat kelas" name="level">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classMajor">Jurusan</label>
                                                                <select class="form-select" id="classMajor" aria-label="Select parent category" name="major_id">
                                                                    <option value="" selected>Pilih Jurusan</option>
                                                                    @foreach ($majors as $major)
                                                                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="classCapacity">Kapasitas<span
                                                                class="txt-danger">*</span></label><input
                                                                class="form-control" id="classCapacity" type="number"
                                                                placeholder="Masukan kapasitas kelas" name="capacity">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="editClassSubmitBtn">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 justify-content-end">
                                <div class="col-auto" style="display: none;">
                                    <button class="btn btn-danger mb-2" id="delete-selected" disabled>Hapus
                                        Terpilih</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0">
                        <div class="list-product list-category">
                            <div class="recent-table table-responsive custom-scrollbar">
                                <table class="table" id="class-table">
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
                                            <th> <span class="c-o-light f-w-600">Nama</span></th>
                                            <th> <span class="c-o-light f-w-600">Tingkat</span></th>
                                            <th> <span class="c-o-light f-w-600">Jurusan</span></th>
                                            <th> <span class="c-o-light f-w-600">Kapasitas</span></th>
                                            <th> <span class="c-o-light f-w-600">Waktu</span></th>
                                            <th> <span class="c-o-light f-w-600">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Container-fluid Ends-->
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/select.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/class-crud.js') }}"></script>
@endsection
