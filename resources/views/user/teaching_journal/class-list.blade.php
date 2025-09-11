@extends('layouts.user.app')

@section('title', 'Jurnal Mengajar')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Journal Mengajar</h3>
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
                                @if (isset($majors) && count($majors) > 0)
                                    <div class="col-md-4 col-xl">
                                        <label class="form-label" for="major-filter">Jurusan</label>
                                        <select class="form-select" id="major-filter" aria-label="Select major">
                                            <option value="" selected>Pilih Jurusan</option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->name }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-4 col-xl">
                                    <label class="form-label" for="class-filter">Kelas</label>
                                    <select class="form-select" id="class-filter" aria-label="Select class">
                                        <option value="" selected>Pilih Kelas</option>
                                        @foreach ($classNames as $class)
                                            <option value="{{ $class->name }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-xl">
                                    <label class="form-label" for="level-filter">Tingkat</label>
                                    <select class="form-select" id="level-filter" aria-label="Select level">
                                        <option value="" selected>Pilih Tingkat</option>
                                        @foreach ($classLevels as $classLevel)
                                            <option value="{{ $classLevel->level }}">{{ $classLevel->level }}</option>
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
                                    <table class="table table-bordered" id="teaching-journal-classlist-table">
                                        <thead>
                                            <tr>
                                                <th><span class="c-o-light f-w-600">No</span></th>
                                                @if ($hasMajor)
                                                    <th><span class="c-o-light f-w-600">Jurusan</span></th>
                                                @endif
                                                <th><span class="c-o-light f-w-600">Kelas</span></th>
                                                <th><span class="c-o-light f-w-600">Tingkat</span></th>
                                                <th><span class="c-o-light f-w-600"></span> </th>
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
    <script>
        const hasMajor = @json($hasMajor);
    </script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/teaching-journal-classlist-crud.js') }}"></script>
@endsection
