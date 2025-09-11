@extends('layouts.user.app')

@section('title', 'Kehadiran')

@section('main_content')
    <div class="container-fluid e-category p-0">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Kehadiran</h3>
                </div>
            </div>
        </div>
        <div class="row">
            @role('teacher')
                <div class="col-12">
                    <div class="card rounded-responsive">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Filter</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                @if (isset($majors) && count($majors) > 0)
                                    <div class="col-md-3 col-xl">
                                        <label class="form-label" for="major-filter">Jurusan</label>
                                        <select class="form-select" id="major-filter" aria-label="Select major">
                                            <option value="" selected>Pilih Jurusan</option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->name }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-3 col-xl">
                                    <label class="form-label" for="class-filter">Kelas</label>
                                    <select class="form-select" id="class-filter" aria-label="Select class">
                                        <option value="" selected>Pilih Kelas</option>
                                        @foreach ($classNames as $class)
                                            <option value="{{ $class->name }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-xl">
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
            @endrole
            <div class="col-12">
                <div class="card rounded-responsive">
                    <div class="card-header card-no-border text-end">
                        <div class="py-3"></div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product list-category">
                            <div class="recent-table table-responsive custom-scrollbar">
                                <table class="table table-bordered" id="attendance-schedule-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">No</span></th>
                                            <th><span class="c-o-light f-w-600">Mata Pelajaran</span></th>
                                            @role(['student', 'parent'])
                                                <th><span class="c-o-light f-w-600">Guru</span></th>
                                            @endrole
                                            @role('teacher')
                                                <th><span class="c-o-light f-w-600">Kelas</span></th>
                                            @endrole
                                            <th><span class="c-o-light f-w-600">Rekap</span> </th>
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
@endsection

@section('scripts')
    <script>
        const role = @json(auth()->user()->getRoleNames()->first());
    </script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/attendance-crud.js') }}"></script>
@endsection
