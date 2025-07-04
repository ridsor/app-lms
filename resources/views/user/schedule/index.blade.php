@extends('layouts.user.app')

@section('title', 'Jadwal')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Daftar Kelas & Jurusan</h3>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header card-no-border">
              <div class="header-top">
                <h5>Filter</h5>
              </div>
            </div>
            <div class="card-body pt-0">
              <div class="row g-3">
                @can('student.*')
                  @if ($majors->count() > 0)
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
                @endcan
                @can('student.view.homeroomteacher')
                  @if ($majors->count() > 0)
                    <div class="col-md-3 col-xl">
                      <label class="form-label" for="major-filter">Jurusan</label>
                      <select class="form-select" disabled id="major-filter" aria-label="Select major">
                        <option value="{{ $homeroomTeacherClass->major->name }}" selected>
                          {{ $homeroomTeacherClass->major->name }}</option>
                      </select>
                    </div>
                  @endif
                @endcan
                @can('student.*')
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="class-filter">Kelas</label>
                    <select class="form-select" id="class-filter" aria-label="Select class">
                      <option value="" selected>Pilih Kelas</option>
                      @foreach ($classNames as $class)
                        <option value="{{ $class->name }}">{{ $class->name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="class-filter">Kelas</label>
                    <select class="form-select" disabled id="class-filter" aria-label="Select class">
                      <option value="{{ $homeroomTeacherClass->name }}" selected>{{ $homeroomTeacherClass->name }}</option>
                    </select>
                  </div>
                @endcan
                @can('student.*')
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="level-filter">Tingkat</label>
                    <select class="form-select" id="level-filter" aria-label="Select level">
                      <option value="" selected>Pilih Tingkat</option>
                      @foreach ($classLevels as $classLevel)
                        <option value="{{ $classLevel->level }}">{{ $classLevel->level }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="level-filter">Tingkat</label>
                    <select class="form-select" disabled id="level-filter" aria-label="Select level">
                      <option value="{{ $homeroomTeacherClass->level }}" selected>{{ $homeroomTeacherClass->level }}
                      </option>
                    </select>
                  </div>
                @endcan
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header card-no-border text-end">
              <div class="py-2"></div>
            </div>
            <div class="card-body pt-0 px-0">
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="schedule-table">
                    <thead>
                      <tr>
                        @if ($majors->count() > 0)
                        <th><span class="c-o-light f-w-600">Jurusan</span></th>
                        @endif
                        <th><span class="c-o-light f-w-600">Kelas</span></th>
                        <th><span class="c-o-light f-w-600">Tingkat</span></th>
                        <th><span class="c-o-light f-w-600">Jumlah Jadwal</span></th>
                        <th><span class="c-o-light f-w-600">Aksi</span> </th>
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
  <script>
    const hasMajor = @json($majors->count() > 0);
  </script>
  <script src="{{ asset('assets/js/schedule-crud.js') }}"></script>
@endsection
