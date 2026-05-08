@extends('layouts.user.app')

@section('title', 'Jadwal Kelas')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
  <style>
    #schedule-by-class-table td p,
    #schedule-by-class-table td,
    #schedule-by-class-table th p,
    #schedule-by-class-table th {
      white-space: nowrap !important;
      overflow: visible !important;
      text-overflow: unset !important;
      max-width: none !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Jadwal Kelas {{ $class->name }} - {{ $class->level }}
            {{ $class->major ? '(' . $class->major->name . ')' : '' }}</h3>
        </div>
        <div class="col-sm-6 text-end mt-2 mt-sm-0">
          <a href="{{ route('user.schedule.classlist') }}" class="btn btn-secondary btn-sm"><i
              class="fa fa-arrow-left"></i>
            Kembali ke Daftar Kelas</a>
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
                  <select class="selectpicker search-picker filter" data-live-search="true" id="teacher-filter">
                    <option value="">Pilih Guru</option>
                    @foreach ($teachers as $teacher)
                      <option value="{{ $teacher->name }}">{{ $teacher->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 col-xl">
                  <label class="form-label" for="room-filter">Ruangan</label>
                  <select class="selectpicker search-picker filter" data-live-search="true" id="room-filter">
                    <option value="">Pilih Ruangan</option>
                    @foreach ($rooms as $room)
                      <option value="{{ $room->name }}">{{ $room->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 col-xl">
                  <label class="form-label" for="day-filter">Hari</label>
                  <select class="form-select" id="day-filter">
                    <option value="">Pilih Hari</option>
                    @foreach ($days as $day)
                      <option value="{{ $day['label'] }}">{{ $day['label'] }}</option>
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
              <button class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal" data-bs-target="#addScheduleModal"><i
                  class="fa fa-plus pe-2"></i>Tambah Jadwal</button>
              <div class="row g-3 justify-content-end align-items-center" id="schedule-by-class-action-buttons">
                <div class="col-auto">
                  <span>
                    <span class="me-1" id="selected-count">0</span> dipilih
                  </span>
                </div>
                <div class="col-auto">
                  <a id="delete-selected"
                    class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-2 py-2"
                    style="cursor: pointer;">
                    <i class="fa-solid fa-trash-can txt-danger"></i>
                  </a>
                </div>
              </div>
              @include('user.schedule.modal')
            </div>
            <div class="card-body px-0 pt-0">
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="schedule-by-class-table">
                    <thead>
                      <tr>
                        <th>
                          <div class="checkbox-checked">
                            <div class="form-check d-flex justify-content-center align-items-center">
                              <input class="form-check-input" id="select-all" type="checkbox"
                                style="width: 12px; height: 12px;" value>
                            </div>
                          </div>
                        </th>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Aksi</th>
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
  <script src="{{ asset('assets/js/flat-pickr/custom-flatpickr.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script>
    const classId = @json(request()->route('classId'));
    const curriculums = @json($activeCurriculum);
  </script>
  <script src="{{ asset('assets/js/schedule-by-class-crud.js') }}"></script>
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
@endsection
