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
