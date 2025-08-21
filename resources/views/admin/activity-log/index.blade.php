@extends('layouts.admin.app')

@section('title', 'Catatan Aktifitas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Catatan Aktifitas</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Catatan Aktifitas</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="e-category">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="card rounded-responsive">
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md">
                                    <label class="form-label">Pengguna</label>
                                    <input class="form-control" type="text" id="user-filter" />
                                </div>
                                <div class="col-md">
                                    <label class="form-label">Dari</label>
                                    <input class="form-control flatpickr-input start-date" id="start-date-filter"
                                        type="date" autocomplete="off" placeholder="DD-MM-YYYY">
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
                <div class="col-12 px-0">
                    <div class="card rounded-responsive">
                        <div class="card-header card-no-border text-end">
                            <div class="py-2">
                            </div>
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="list-product list-category">
                                <div class="recent-table table-responsive custom-scrollbar">
                                    <table class="table table-bordered" id="activity-log-table">
                                        <thead>
                                            <tr>
                                                <th><span class="c-o-light f-w-600">No</span></th>
                                                <th><span class="c-o-light f-w-600">Nama Aktifitas</span></th>
                                                <th><span class="c-o-light f-w-600">Informasi</span></th>
                                                <th><span class="c-o-light f-w-600">Pengguna</span> </th>
                                                <th><span class="c-o-light f-w-600">Subjek</span> </th>
                                                <th><span class="c-o-light f-w-600">Waktu</span> </th>
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
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/activity-log.js') }}"></script>
@endsection
