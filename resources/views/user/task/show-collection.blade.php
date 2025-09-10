@php
    use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Pengumpulan Tugas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <style>
        .view_file_path .Archive,
        .view_file_path .Link {
            background: #f5f6f9 !important;
        }

        .dark-only .view_file_path .Archive,
        .dark-only .view_file_path .Link {
            background: #1d1e26 !important;
        }

        #task-collection-table_wrapper .dt-search {
            display: none !important;
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid p-0">
        <div class="page-title">
            <div class="row p-2 p-sm-0">
                <div class="col-sm-6">
                    <h3>Tugas</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.schedule.index') }}">
                                {{ $task->meeting->schedule->subject->code }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Tugas
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="container-fluid e-category p-0">
            <div class="row g-0 mb-4">
                <div class="col-12 p-0">
                    <div class="card rounded-responsive">
                        <div class="card-body">
                            <ul class="d-flex gap-2 row-gap-3 flex-wrap">
                                <li>
                                    <a href="{{ !Request::routeIs('user.task.show') ? route('user.task.show', ['task_id' => $task->id]) : '' }}"
                                        class="py-2 px-2 {{ Request::routeIs('user.task.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Info
                                        Tugas</a>
                                </li>
                                <li>
                                    <a href="{{ !Request::routeIs('user.task.collection') ? route('user.task.collection', ['task_id' => $task->id]) : '' }}"
                                        class="py-2 px-2 {{ Request::routeIs('user.task.collection') ? 'border-bottom border-primary' : 'text-secondary' }}">Pengumpulan</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0">
                    <div class="card h-100 my-0 rounded-responsive">
                        <div class="card-body px-3 py-4">
                            <h3 class="mb-3">Pengumpulan Tugas</h3>
                            <div class="row justify-content-between gap-2">
                                <div class="col-auto row gap-2 align-items-center flex-grow-1">
                                    @if ($task->not_yet_rated)
                                        <div class="d-flex flep-wrap align-items-center col-auto">
                                            <span
                                                class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum
                                                dinilai <span
                                                    class="badge ms-1 badge-danger">{{ $task->not_yet_rated }}</span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center w-100 gap-2 col-12" style="max-width:400px">
                                        <i data-feather="search"></i>
                                        <input type="type" class="form-control w-100" placeholder="Cari nama mahasiswa"
                                            id="globalSearch" />
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 col-12 justify-content-between col-md-auto">
                                    <div class="d-flex gap-2 align-items-center h-100">
                                        <label for="displayedValue" class="mb-0">Nilai ditampilkan</label>
                                        <div class="form-check form-switch form-check-inline m-0"><input
                                                class="form-check-input check-size" id="displayedValue" type="checkbox"
                                                data-id="{{ $task->id }}" style="transform:translateY(2px)"
                                                role="switch" {{ $task->value_displayed ? 'checked' : '' }}></div>
                                    </div>
                                    <form action="{{ route('user.task.result.export', ['id' => $task->id]) }}"
                                        method="GET" class="d-inline" id="export-excel">
                                        @csrf
                                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-file"></i>
                                            <span>
                                                Export Excel
                                            </span>
                                        </button>
                                    </form>
                                    <a class="btn btn-outline-info d-flex align-items-center gap-2 {{ count($task->submissions) > 0 ?: 'pe-none' }}"
                                        {{ $task->submissions->first()?->id ? 'href=' . route('user.task.evaluation', ['task_id' => $task->id]) : '' }}>
                                        <span>
                                            Penilaian
                                        </span>
                                        <i data-feather="edit-2" style="width: 18px; height: 18px"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="e-category">
                                <div class="row">
                                    <div class="col-12 px-0">
                                        <div class="card rounded-responsive shadow-none">
                                            <div class="card-header card-no-border text-end">
                                                <div class="py-2"></div>
                                            </div>
                                            <div class="card-body px-0 pt-0">
                                                <div class="list-product list-category">
                                                    <div class="recent-table table-responsive custom-scrollbar">
                                                        <table class="table table-bordered" id="task-collection-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Nama</th>
                                                                    <th>Pengumpulan</th>
                                                                    <th>Nilai</th>
                                                                    <th>Penilaian</th>
                                                                    <th>Penilai</th>
                                                                    <th></th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script>
        const task_id = @json($task->id)
    </script>
    <script src={{ asset('assets/js/task-collection.js') }}></script>
@endsection
