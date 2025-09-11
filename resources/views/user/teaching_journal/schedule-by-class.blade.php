@extends('layouts.user.app')

@section('title', 'Jurnal Mengajar')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Kelas {{ $class->name }} - {{ $class->level }}
                        @if ($class->major)
                            ({{ $class->major->name }})
                        @endif
                    </h3>
                </div>
                <div class="col-sm-6 text-end mt-2 mt-sm-0">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i>
                        Kembali ke
                        Daftar Kelas</a>
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
                                    <label class="form-label" for="teacher-filter">Guru</label>
                                    <select class="selectpicker search-picker filter" data-live-search="true"
                                        id="teacher-filter">
                                        <option value="">Pilih Guru</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->name }}">{{ $teacher->name }}</option>
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
                            <div class="py-2"></div>
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="list-product list-category">
                                <div class="recent-table table-responsive custom-scrollbar">
                                    <table class="table table-bordered" id="journal-schedule-by-class-table">
                                        <thead>
                                            <tr>
                                                <th><span class="c-o-light f-w-600">No</span></th>
                                                <th><span class="c-o-light f-w-600">Mata Pelajaran</span></th>
                                                <th><span class="c-o-light f-w-600">Pengajar</span></th>
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
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/teaching-journal-schedule-by-class-crud.js') }}"></script>
    <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
@endsection
