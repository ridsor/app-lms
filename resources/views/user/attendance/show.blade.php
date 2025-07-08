@extends('layouts.user.app')

@section('title', 'Rekap Kehadiran Jadwal')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Rekap Kehadiran Jadwal</h3>
          <div class="mb-2">
            <strong>Kelas:</strong> {{ $schedule->class->name }} - {{ $schedule->class->level }}<br>
            @if ($schedule->class->major)
              <strong>Jurusan:</strong> {{ $schedule->class->major->name }}<br>
            @endif
            <strong>Mata Pelajaran:</strong> {{ $schedule->subject->name }}<br>
            <strong>Guru:</strong> {{ $schedule->teacher->name }}<br>
            <strong>Hari:</strong> {{ $schedule->day }}<br>
            <strong>Jam:</strong> {{ $schedule->start_time }} - {{ $schedule->end_time }}
          </div>
        </div>
        <div class="col-sm-6 text-end mt-2 mt-sm-0">
          <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
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
                  <label class="form-label" for="student-filter">Nama Siswa</label>
                  <input type="text" id="student-filter" class="form-control" placeholder="Cari nama siswa">
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="status-filter">Status Kehadiran</label>
                  <select id="status-filter" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="H">Hadir</option>
                    <option value="I">Izin</option>
                    <option value="S">Sakit</option>
                    <option value="A">Alpha</option>
                  </select>
                </div>
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
                </div>
              </div>
            </div>
            <div class="card-body pt-0 px-0">
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="attendance-show-table">
                    <thead>
                      <tr>
                        <th>Nama Siswa</th>
                        <th>Status Kehadiran</th>
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

  <!-- Modal Edit Status -->
  <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAttendanceModalLabel">Edit Status Kehadiran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAttendanceForm">
          <div class="modal-body">
            <input type="hidden" name="attendance_id" id="editAttendanceId">
            <div class="mb-3">
              <label for="editAttendanceStatus" class="form-label">Status</label>
              <select class="form-select" name="status" id="editAttendanceStatus" required>
                <option value="H">Hadir</option>
                <option value="I">Izin</option>
                <option value="S">Sakit</option>
                <option value="A">Alpha</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/attendance-show-crud.js') }}"></script>
@endsection
