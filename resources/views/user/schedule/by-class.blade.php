@extends('layouts.user.app')

@section('title', 'Jadwal Kelas')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
  <style>
    #schedule-by-class-table td:nth-child(3) p,
    #schedule-by-class-table td:nth-child(3),
    #schedule-by-class-table th:nth-child(3) p,
    #schedule-by-class-table th:nth-child(3),
    #schedule-by-class-table td:nth-child(4) p,
    #schedule-by-class-table td:nth-child(4),
    #schedule-by-class-table th:nth-child(4) p,
    #schedule-by-class-table th:nth-child(4) {
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
          <a href="{{ route('user.schedule.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i>
            Kembali ke Daftar Kelas</a>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header card-no-border text-end">
              <button class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal" data-bs-target="#addScheduleModal"><i
                  class="fa fa-plus pe-2"></i>Tambah Jadwal</button>
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
  </script>
  <script src="{{ asset('assets/js/schedule-by-class-crud.js') }}"></script>
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
@endsection
