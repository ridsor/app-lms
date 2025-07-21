@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Kehadiran')

@section('styles')
  <style>
    #attendance-table th {
      white-space: nowrap !important;
      overflow: visible !important;
      text-overflow: unset !important;
      max-width: none !important;
    }

    #attendance-table td {
      white-space: nowrap !important;
      overflow: visible !important;
      text-overflow: unset !important;
      max-width: none !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid e-category p-0">
    <div class="row">
      <div class="col-12">
        <div class="card my-4 rounded-responsive">
          <div class="card-body px-0">
            <div class="row g-2 px-4 mb-4">
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Mata Pelajaran</label>
                <p class="c-o-light f-w-600">
                  <span id="subject">
                    {{ $meeting->schedule->subject->name }}
                  </span>
                </p>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Kelas</label>
                <p class="c-o-light f-w-600">
                  <span id="class">
                    {{ $meeting->schedule->class->major ? $meeting->schedule->class->major->name . ' - ' : '' }}{{ $meeting->schedule->class->name }}
                    - {{ $meeting->schedule->class->level }}
                  </span>
                </p>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Pertemuan ke</label>
                <p class="c-o-light f-w-600">
                  <span id="meeting">
                    {{ $meeting->meeting_at }}
                  </span>
                </p>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Metode</label>
                <p class="c-o-light f-w-600">
                  <span id="meething_method">
                    {{ Helper::getMeetingMethodLabel($meeting->meeting_method) }}
                  </span>
                </p>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Waktu</label>
                <div class="c-o-light f-w-600">
                  <div class="d-flex gap-2 align-items-center">
                    <div class="d-flex align-items-center">
                      <i class="fa-solid fa-calendar"></i>
                      <span class="mb-0 ms-2" id="date">{{ Helper::getDayName($meeting->schedule_time->day) }}</span>
                    </div>
                    <div>&middot;</div>
                    <span>
                      <span id="start_time">{{ $meeting->schedule_time->start_time->translatedFormat('H:i') }}</span> -
                      <span id="end_time">{{ $meeting->schedule_time->end_time->translatedFormat('H:i') }}</span> WIT
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Status</label>
                <p class="c-o-light f-w-600">
                  <span id="status" class="badge badge-light-primary">
                    {{ $meeting->status }}
                  </span>
                </p>
              </div>
              <div class="col-12 mt-4">
                <div class="d-flex gap-3 mb-2 justify-content-between align-items-center">
                  <h6>Kehadiran Pertemuan</h6>
                </div>
                <div class="row g-2">
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label">Kelas dimulai</label>
                    <p class="c-o-light f-w-600">
                      <span id="started_at">
                        {{ $meeting->started_at ? $meeting->date . ' ' . $meeting->formatted_started_at . ' WIT' : '-' }}
                      </span>
                    </p>
                  </div>
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label">Jumlah Hadir</label>
                    <p class="c-o-light f-w-600">
                      <span id="total_attendance">{{ $meeting->attendances_count }}</span> dari <span
                        id="total_user">{{ $meeting->schedule->class->students_count }}</span> Peserta
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-12 mt-4">
                <h6 class="mb-3">Pengajar</h6>
                <div class="d-flex align-items-center gap-3">
                  <div class="profile-media">
                    <img class="rounded-circle"
                      style="
                          width: 50px;
                          height: 50px;
                          object-fit: cover;"
                      id="teacher-image"
                      src="{{ $meeting->schedule->teacher->user->image ? asset('storage/' . $meeting->schedule->teacher->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                      alt="user">
                  </div>
                  <div class="d-flex">
                    <p class="mb-0 c-o-light fw-medium" id="teacher">{{ $meeting->schedule->teacher->name }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div>
              <h6 class="px-4 mb-3">Peserta</h6>
              <div class="row justify-content-between align-items-center mb-4 px-4 g-2">
                @can('attendance.edit')
                <div class="col-auto col-lg-6">
                  <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-circle-info txt-primary"></i>
                    <span class="txt-primary">
                      Pengajar melakukan presensi kehadiran peserta secara manual pada pertemuan ini
                    </span>
                  </div>
                </div>
                <div class="col-auto">
                  <button class="btn btn-primary" id="change_attendance">
                    Ubah Kehadiran
                  </button>
                  <button class="btn btn-outline-primary" id="cancel_change_attendance" style="display:none">
                    Batal
                  </button>
                  <button class="btn btn-primary" id="save_attendance" style="display:none"
                    data-meeting-id="{{ $meeting->id }}">
                    Simpan Kehadiran
                  </button>
                </div>
              </div>
              @endcan
              <div class="list-product list-category">
                <div class="recent-table table-responsive custom-scrollbar">
                  <table class="table table-bordered" id="attendance-table">
                    <thead>
                      <tr>
                        <th rowspan="2"> <span class="c-o-light f-w-600">No</span></th>
                        <th rowspan="2"> <span class="c-o-light f-w-600">Nama</span></th>
                        <th rowspan="2"> <span class="c-o-light f-w-600">Kehadiran</span></th>
                        <th rowspan="2" class="update-column"> <span class="c-o-light f-w-600">Update</span></th>
                        <th class="status-column" style="display: none"> <span class="c-o-light f-w-600">Status</span>
                        </th>
                      </tr>
                      <tr>
                        <th class="status-column" style="display: none">
                          <div class="d-flex gap-3 align-items-center checkbox-checked">
                            @foreach ($attendanceValue as $key => $value)
                              <div class="form-check">
                                <label class="form-check-label fs-6 mb-0">
                                  <input class="form-check-input border-secondary border status-all-{{ $value }}"
                                    name="{{ 'status-all' }}" value="{{ $value }}"
                                    type="radio">{{ $value }}</label>
                              </div>
                            @endforeach
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($attendances as $key => $attendance)
                        <tr>
                          <td>
                            <p class="f-light mb-0">{{ $key + 1 }}</p>
                          </td>
                          <td>
                            <p class="f-light mb-0">
                              {{ $attendance['student']->name }}</p>
                            <p class="f-light mb-0">{{ $attendance['student']->nisn }}</p>
                          </td>
                          <td>
                            {!! Helper::getAttendanceLabel($attendance['status']) !!}
                          </td>
                          <td class="update-value">
                            <p class="f-light mb-0">
                              {{ $attendance['editby'] ? $attendance['editby']->username . ' | ' . $attendance['updated_at']->translatedFormat('l, d F Y - H:i:s') . ' WIT' : ' - ' }}
                            </p>
                          </td>
                          <td class="status-input" style="padding: 12px 20px; display: none"
                            data-user-id="{{ $attendance['student']->user_id }}">
                            <div class="d-flex gap-3 align-items-center checkbox-checked">
                              @foreach ($attendanceValue as $value)
                                <div class="form-check">
                                  <label class="form-check-label fs-6 mb-0">
                                    <input class="form-check-input border-3 status-value" name="{{ 'status' . $key }}"
                                      value="{{ $value }}" type="radio"
                                      @if ($attendance['status'] == $value) checked @endif>{{ $value }}</label>
                                </div>
                              @endforeach
                            </div>
                          </td>
                        </tr>
                      @endforeach
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
  <script>
    const statuses = @json($attendanceValue);
  </script>
  <script src="{{ asset('assets/js/edit-attendance.js') }}"></script>
@endsection
