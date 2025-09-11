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
            <div class="col-12">
                <div class="card rounded-responsive">
                    <div class="card-header card-no-border text-end">
                        <div class="py-3"></div>
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
                                        <div class="row g-2 text-start">
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
                                                            <span
                                                                class="icon d-inline-flex justify-content-center align-items-center">
                                                                <i data-feather="calendar"
                                                                    style="width:18px; height: 18px"></i>
                                                            </span>
                                                            <span class="mb-0 ms-2" id="date">-</span>
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
                    <div class="card-body pt-0 px-0">
                        <div class="list-product list-category">
                            <div class="recent-table table-responsive custom-scrollbar">
                                <table class="table table-bordered" id="attendance-schedule-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">Pertemuan</span></th>
                                            <th><span class="c-o-light f-w-600">Waktu</span></th>
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
        const schedule_id = @json($schedule->id);
    </script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/attendance-meeting-by-schedule.js') }}"></script>
@endsection
