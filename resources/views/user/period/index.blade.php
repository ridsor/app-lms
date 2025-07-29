@extends('layouts.user.app')

@section('title', 'Periode')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
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
    </div><!-- Container-fluid starts-->
    <div class="e-category">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-responsive">
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md"><label class="form-label">Semester</label><select class="form-select"
                                    id="semester-filter" aria-label="Pilih semester">
                                    <option value="" selected>Pilih Semester</option>
                                    <option value="odd">Ganjil</option>
                                    <option value="even">Genap</option>
                                </select>
                            </div>
                            <div class="col-md">
                                <label class="form-label">Dari</label>
                                <input class="form-control flatpickr-input start-date" id="start-date-filter" type="date"
                                    autocomplete="off" placeholder="DD-MM-YYYY">
                            </div>
                            <div class="col-md"><label class="form-label">Sampai</label>
                                <input class="form-control flatpickr-input end-date" id="end-date-filter"
                                    placeholder="DD-MM-YYYY" type="date" autocomplete="off">
                            </div>
                            <div class="col  d-flex justify-content-start align-items-end"><a
                                    class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card rounded-responsive">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500 mb-2" href="#" data-bs-toggle="modal"
                                data-bs-target="#addPeriodModal"><i class="fa fa-plus pe-2"></i>Tambah
                            </a>
                            <div class="modal fade" id="addPeriodModal" tabindex="-1" aria-labelledby="addPeriodModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Periode</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate id="addPeriodForm">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="POST">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="semester">Semester<span
                                                                    class="txt-danger">*</span></label>
                                                            <select class="form-select" id="addSemester" name="semester"
                                                                required>
                                                                <option value="" selected>Pilih Semester</option>
                                                                <option value="odd">Ganjil</option>
                                                                <option value="even">Genap</option>
                                                            </select>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="academicYear">Tahun
                                                                Akademik<span class="txt-danger">*</span></label>
                                                            <input class="form-control" id="addAcademicYear"
                                                                type="text" placeholder="Contoh: 2024/2025"
                                                                name="academic_year">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex flex-column flatpicker-input">
                                                                <label class="form-label" for="addStartDate">Tanggal
                                                                    Mulai<span class="txt-danger">*</span></label>
                                                                <input class="form-control flatpickr start-date"
                                                                    id="addStartDate" name="start_date"
                                                                    placeholder="DD-MM-YYYY" type="date"
                                                                    autocomplete="off">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex flex-column flatpicker-input">
                                                                <label class="form-label" for="addEndDate">Tanggal
                                                                    Selesai<span class="txt-danger">*</span></label>
                                                                <input class="form-control flatpickr end-date"
                                                                    id="addEndDate" name="end_date"
                                                                    placeholder="DD-MM-YYYY" type="date"
                                                                    autocomplete="off">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="addPeriodSubmitBtn">Tambah +</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editPeriodModal" tabindex="-1"
                                aria-labelledby="editPeriodModal" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Edit Periode</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate id="editPeriodForm">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="PUT">
                                                        <input type="hidden" id="editPeriodId" name="period_id">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="semester">Semester<span
                                                                    class="txt-danger">*</span></label>
                                                            <select class="form-select" id="editSemester" name="semester"
                                                                required>
                                                                <option value="" selected>Pilih Semester</option>
                                                                <option value="odd">Ganjil</option>
                                                                <option value="even">Genap</option>
                                                            </select>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="academicYear">Tahun
                                                                Akademik<span class="txt-danger">*</span></label>
                                                            <input class="form-control" id="editAcademicYear"
                                                                type="text" placeholder="Contoh: 2024/2025"
                                                                name="academic_year">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex flex-column flatpicker-input">
                                                                <label class="form-label" for="editStartDate">
                                                                    Tanggal
                                                                    Mulai<span class="txt-danger">*</span>
                                                                </label>
                                                                <input class="form-control flatpickr start-date"
                                                                    id="editStartDate" name="start_date"
                                                                    placeholder="DD-MM-YYYY" type="date"
                                                                    autocomplete="off">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex flex-column flatpicker-input">
                                                                <label class="form-label" for="editEndDate">
                                                                    Tanggal
                                                                    Selesai<span class="txt-danger">*</span>
                                                                </label>
                                                                <input class="form-control flatpickr start-date"
                                                                    id="editEndDate" name="end_date"
                                                                    placeholder="DD-MM-YYYY" type="date"
                                                                    autocomplete="off">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="editPeriodSubmitBtn">Simpan</button>
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
                                    <a id="delete-selected"
                                        class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-3 py-2"
                                        style="cursor: pointer;">
                                        <span class="me-1 text-dark" id="delete-selected-count">0</span>
                                        <i class="fa-solid fa-trash-can txt-danger"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="list-product list-category">
                            <div class="recent-table table-responsive custom-scrollbar">
                                <table class="table" id="period-table">
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
                                            <th> <span class="c-o-light f-w-600">Semester</span></th>
                                            <th> <span class="c-o-light f-w-600">Tahun Akademik</span></th>
                                            <th> <span class="c-o-light f-w-600">Periode</span></th>
                                            <th> <span class="c-o-light f-w-600">Status</span></th>
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
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/select.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/period-crud.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
@endsection
