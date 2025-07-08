@extends('layouts.user.app')

@section('title', 'Rekap Kehadiran Meeting')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Rekap Kehadiran Meeting</h3>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header card-no-border text-end">
              <div class="row g-2 align-items-end">
                <div class="col-md-4">
                  <label class="form-label" for="meeting-filter">Pertemuan</label>
                  <select class="form-select" id="meeting-filter">
                    <option value="">Pilih Pertemuan</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="date-filter">Tanggal</label>
                  <input type="date" class="form-control" id="date-filter">
                </div>
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
                </div>
              </div>
            </div>
            <div class="card-body pt-0 px-0">
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="attendance-meeting-table">
                    <thead>
                      <tr>
                        <th>Pertemuan</th>
                        <th>Tanggal</th>
                        <th>Daftar Kehadiran</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
  <script src="{{ asset('assets/js/attendance-crud.js') }}"></script>
@endsection
