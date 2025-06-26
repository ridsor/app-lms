@extends('layouts.user.app')

@section('title', 'Manajemen Ruangan')

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
    </div><!-- Container-fluid starts-->
    <div class="container-fluid e-category">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500 mb-2" href="#" data-bs-toggle="modal"
                                data-bs-target="#addRoomModal"><i class="fa fa-plus pe-2"></i>Tambah
                            </a>
                            <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Ruangan</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate="" id="addRoomForm">
                                                        <div class="col-md-12">
                                                            <label class="form-label" for="roomName">Nama Ruangan<span
                                                                    class="txt-danger">*</span></label>
                                                            <input class="form-control" id="roomName" type="text"
                                                                placeholder="Masukan nama ruangan" name="name">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="addRoomSubmitBtn">Tambah +</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Edit Ruangan</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate="" id="editRoomForm">
                                                        <div class="col-md-12">
                                                            <label class="form-label" for="roomName">Nama Ruangan<span
                                                                    class="txt-danger">*</span></label>
                                                            <input class="form-control" id="roomName" type="text"
                                                                placeholder="Masukan nama ruangan" name="name">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="editRoomSubmitBtn">Simpan</button>
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
                                <table class="table" id="room-table">
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
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/room-crud.js') }}"></script>
@endsection
