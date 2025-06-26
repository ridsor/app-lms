@extends('layouts.user.app')

@section('title', 'Manajemen Jurusan')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Jurusan</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Jurusan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div><!-- Container-fluid starts-->
    <div class="container-fluid e-category">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500 mb-2" href="#" data-bs-toggle="modal"
                                data-bs-target="#addMajorModal"><i class="fa fa-plus pe-2"></i>Tambah
                            </a>
                            <div class="modal fade" id="addMajorModal" tabindex="-1" aria-labelledby="addMajorModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Jurusan</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate="" id="addMajorForm">
                                                        <div class="col-md-12">
                                                            <label class="form-label" for="majorName">Nama Jurusan<span
                                                                    class="txt-danger">*</span></label>
                                                            <input class="form-control" id="majorName" type="text"
                                                                placeholder="Masukan nama jurusan" name="name">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="addMajorSubmitBtn">Tambah +</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editMajorModal" tabindex="-1" aria-labelledby="editMajorModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Edit Jurusan</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate=""
                                                        id="editMajorForm">
                                                        <div class="col-md-12">
                                                            <label class="form-label" for="majorName">Nama Jurusan<span
                                                                    class="txt-danger">*</span></label>
                                                            <input class="form-control" id="majorName" type="text"
                                                                placeholder="Masukan nama jurusan" name="name">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="editMajorSubmitBtn">Simpan</button>
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
                                <table class="table" id="major-table">
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
                                            <th> <span class="c-o-light f-w-600">Waktu</span></th>
                                            <th> <span class="c-o-light f-w-600">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
    <script src="{{ asset('assets/js/major-crud.js') }}"></script>
@endsection
