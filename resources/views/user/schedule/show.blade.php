@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Jadwal')

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Jadwal</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Jadwal</li>
            <li class="breadcrumb-item active">{{ $schedule->subject->code }}</li>
          </ol>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-body px-0">
              <div class="row g-2 px-4 mb-4">
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Periode </label>
                  <p class="c-o-light f-w-600">
                    <span>
                      {{ $schedule->period->academic_year }} {{ Helper::getSemesterLabel($schedule->period->semester) }}
                    </span>
                  </p>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Kode Matpel</label>
                  <p class="c-o-light f-w-600">
                    <span>
                      {{ $schedule->subject->code }}
                    </span>
                  </p>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Mata Pelajaran</label>
                  <p class="c-o-light f-w-600">
                    <span>
                      {{ $schedule->subject->name }}
                    </span>
                  </p>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Kelas</label>
                  <p class="c-o-light f-w-600">
                    <span>
                      {{ $schedule->class->name }}{{ $schedule->class->level }}
                    </span>
                  </p>
                </div>
                @if ($schedule->class->major)
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label">Jurusan</label>
                    <p class="c-o-light f-w-600">
                      <span>
                        {{ $schedule->class->major->name }}
                      </span>
                    </p>
                  </div>
                @endif
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Metode</label>
                  <p class="c-o-light f-w-600">
                    <span id="meething_method">
                      {{ Helper::getMeetingMethodLabel($schedule->meeting_method) }}
                    </span>
                  </p>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                  <label class="form-label">Waktu</label>
                  <div class="c-o-light f-w-600">
                    <div class="d-flex flex-column gap-1">
                      @foreach ($schedule->days_time as $value)
                      <div class="d-flex gap-2">
                        
                        <div class="d-flex align-items-center">
                          <i class="fa-solid fa-calendar"></i>
                          <span class="mb-0 ms-2" id="date">{{ $value['day'] }}, </span>
                        </div>
                        <span>
                          <span id="start_time">{{ $value['start_time'] }}</span> - <span
                            id="end_time">{{ $value['end_time'] }}</span> WIT
                        </span>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="col-12 mt-4">
                  <h6 class="mb-3">Guru Pengajar</h6>
                  <div class="d-flex align-items-center gap-3">
                    <div class="profile-media">
                      <img class="rounded-circle"
                        style="
                            width: 50px;
                            height: 50px;
                            object-fit: cover;"
                        id="teacher-image"
                        src="{{ $schedule->teacher->user->image ? asset('storage/' . $schedule->teacher->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                        alt="user">
                    </div>
                    <div class="d-flex">
                      <p class="mb-0 c-o-light fw-medium" id="teacher">{{ $schedule->teacher->name }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <div class="d-flex align-items-center mb-3">
                  <h6 class="px-4">Peserta</h6>
                  <p class="mb-0 c-o-light fw-medium fs-6">
                    <span class="badge badge-light-primary">{{ $schedule->class->students_count }}</span>
                  </p>
                </div>
                <div class="row px-4 g-4">
                  @foreach ($schedule->class->students as $student)
                    <div class="col-12 col-md-6 col-lg-4">
                      <div class="d-flex align-items-center gap-3">
                        <div class="profile-media">
                          <img class="rounded-circle"
                            style="
                              width: 50px;
                              height: 50px;
                              object-fit: cover;"
                            id="teacher-image"
                            src="{{ $student->user->image ? asset('storage/' . $student->user->image) : asset('assets/svg/user-placeholder.svg') }}"
                            alt="user">
                        </div>
                        <div class="d-flex flex-column">
                          <p class="mb-0 c-o-light fw-medium" id="teacher">{{ $student->name }}</p>
                          <p class="mb-0 c-o-light" id="teacher">{{ $student->nisn }}</p>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
                {{-- <div class="list-product list-category">
                  <div class="recent-table table-responsive custom-scrollbar">
                    <table class="table table-bordered" id="attendance-table">
                      <thead>
                        <tr>
                          <th rowspan="2"> <span class="c-o-light f-w-600">No</span></th>
                          <th rowspan="2"> <span class="c-o-light f-w-600">Nama</span></th>
                          <th rowspan="2"> <span class="c-o-light f-w-600">Kehadiran</span></th>
                          <th rowspan="2" class="update-column"> <span class="c-o-light f-w-600">Update</span></th>
                          <th class="status-column" style="display: none"> <span
                              class="c-o-light f-w-600">Status</span>
                          </th>
                        </tr>
                        <tr>
                          <th class="status-column" style="display: none">
                            <div class="d-flex gap-3 align-items-center checkbox-checked">
                              @foreach ($attendanceValue as $key => $value)
                                <div class="form-check">
                                  <label class="form-check-label fs-6 mb-0">
                                    <input class="form-check-input border-3 status-all-{{ $value }}"
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
                              <p class="f-light">{{ $key + 1 }}</p>
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
                                      <input class="form-check-input border-3 status-value"
                                        name="{{ 'status' . $key }}" value="{{ $value }}" type="radio"
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
                </div> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
