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
        <div class="page-title">
            <div class="row justify-content-between">
                <div class="col col-sm-6">
                    <h3>Rekap Kehadiran Kelas</h3>
                </div>
                <div class="col-auto text-end mt-2 mt-sm-0">
                    <a href="{{ url()->previous() }}" class="btn text-nowrap btn-secondary btn-sm"><i
                            class="fa fa-arrow-left"></i>
                        Kembali</a>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card rounded-responsive">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Mata Pelajaran</label>
                                <p class="c-o-light f-w-600">
                                    {{ $schedule->subject->name }}
                                </p>
                                <label class="form-label">Kelas</label>
                                <p class="c-o-light f-w-600">
                                    {{ $schedule->class?->major ? $schedule->class?->major?->name . ' - ' : '' }}
                                    {{ $schedule->class->name }} -
                                    {{ $schedule->class->level }}
                                </p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Pengajar</label>
                                <p class="c-o-light f-w-600">
                                    {{ $schedule->teacher->name }}
                                </p>
                                <label class="form-label">Periode</label>
                                <p class="c-o-light f-w-600">
                                    {{ $schedule->period->semester == 'odd' ? 'Ganjil' : 'Genap' }} TA
                                    {{ $schedule->period->academic_year }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 py-0">
                        <div class="modal fade" id="detailMeetingModal" tabindex="-1" aria-labelledby="addScheduleModal"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 800px">
                                <div class="modal-content category-popup">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pertemuan</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Mata Pelajaran</label>
                                                <p class="c-o-light f-w-600">
                                                    <span id="subject">
                                                        -
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Kelas</label>
                                                <p class="c-o-light f-w-600">
                                                    <span id="class">
                                                        -
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Pertemuan ke</label>
                                                <p class="c-o-light f-w-600">
                                                    <span id="meeting">
                                                        -
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Metode</label>
                                                <p class="c-o-light f-w-600">
                                                    <span id="meeting_method">
                                                        -
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Waktu</label>
                                                <div class="c-o-light f-w-600">
                                                    <div class="d-flex gap-2">
                                                        <div class="d-flex align-items-center">
                                                            <span><i data-feather="calendar"
                                                                    style="width:18px; height: 18px"></i></span>
                                                            <span class="mb-0 ms-2" id="day">-</span>
                                                        </div>
                                                        <div>&middot;</div>
                                                        <span>
                                                            <span id="start_time">00:00</span> - <span
                                                                id="end_time">00:00</span> WIT
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Status</label>
                                                <p class="c-o-light f-w-600">
                                                    <span id="status" class="badge badge-light-primary">
                                                        -
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div class="d-flex gap-3 mb-2 justify-content-between align-items-center">
                                                    <h6>Kehadiran Pertemuan</h6>
                                                    @can('attendance.edit')
                                                        <a class="btn btn-primary" id="change_attendance">
                                                            Ubah Kehadiran
                                                        </a>
                                                    @endcan
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Kelas dimulai</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span id="started_at">
                                                                -
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Jumlah Hadir</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span id="total_attendance">-</span> dari <span
                                                                id="total_user">-</span> Peserta
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
                                                            src="{{ asset('assets/svg/user-placeholder.svg') }}"
                                                            alt="user">
                                                    </div>
                                                    <div class="d-flex">
                                                        <p class="mb-0 c-o-light fw-medium" id="teacher">
                                                            -
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-product list-category">
                        <div class="recent-table table-responsive custom-scrollbar">
                            <table class="table table-bordered" id="attendance-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2"> <span class="c-o-light f-w-600">No</span></th>
                                        <th rowspan="2"> <span class="c-o-light f-w-600">Nama</span></th>
                                        @if (count($schedule->meetings->toArray()) > 0)
                                            <th class="text-center" colspan="{{ count($schedule->meetings->toArray()) }}">
                                                <span class="c-o-light f-w-600">Pertemuan</span>
                                            </th>
                                        @endif
                                        <th colspan="4" class="text-center"> <span
                                                class="c-o-light f-w-600">Jumlah</span></th>
                                    </tr>
                                    <tr>
                                        @foreach ($schedule->meetings as $key => $value)
                                            <th class="text-center" style="padding: 12px 8px; ">
                                                <button
                                                    class="btn badge {{ $value->started_at ? 'btn-outline-info' : 'btn-outline-secondary' }}"
                                                    onclick="handleDetailMeeting({{ $value->id }},{{ $value->schedule_time_id }})">
                                                    {{ $key + 1 }}
                                                </button>
                                            </th>
                                        @endforeach
                                        <th class="text-center"> <span class="c-o-light f-w-600">Hadir</span></th>
                                        <th class="text-center"> <span class="c-o-light f-w-600">Izin</span></th>
                                        <th class="text-center"> <span class="c-o-light f-w-600">Sakit</span></th>
                                        <th class="text-center"> <span class="c-o-light f-w-600">Absen</span></th>
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
                                                <p class="f-light mb-0">{{ $attendance['student']->nis }}</p>
                                            </td>
                                            @foreach ($attendance['attendances'] as $status)
                                                <td class="text-center">
                                                    {!! Helper::getAttendanceLabel($status) !!}
                                                </td>
                                            @endforeach
                                            <td class="text-center">
                                                {{ $attendance['total_attendance'] }}
                                            </td>
                                            <td class="text-center">
                                                {{ $attendance['total_permission'] }}
                                            </td>
                                            <td class="text-center">
                                                {{ $attendance['total_sick'] }}
                                            </td>
                                            <td class="text-center">
                                                {{ $attendance['total_absence'] }}
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/show-attendance-recap.js') }}"></script>
@endsection
