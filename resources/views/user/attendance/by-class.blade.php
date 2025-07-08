@extends('layouts.user.app')

@section('title', 'Kelompok Jadwal Kelas')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Kelompok Jadwal Kelas</h3>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header card-no-border text-end">
              <div class="py-2"></div>
            </div>
            <div class="card-body pt-0 px-0">
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="attendance-schedule-group-table">
                    <thead>
                      <tr>
                        <th><span class="c-o-light f-w-600">Mata Pelajaran</span></th>
                        <th><span class="c-o-light f-w-600">Guru</span></th>
                        <th><span class="c-o-light f-w-600">Aksi</span></th>
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
    window.classId = @json($classId);
  </script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script src="{{ asset('assets/js/attendance-crud.js') }}"></script>
@endsection
