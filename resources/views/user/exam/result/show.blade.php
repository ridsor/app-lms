@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Hasil Ujian')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
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

    #exam-result-table_wrapper .dt-search {
      display: none !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Ujian</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">
              <a href="{{ route('user.exam.show', ['id' => $exam->id]) }}">
                Ujian
              </a>
            </li>
            <li class="breadcrumb-item active">
              Hasil
            </li>
          </ol>
        </div>
      </div>
    </div>
    @include('user.exam.menu')
    <div class="container-fluid e-category p-0">
      <div class="row g-0 mb-4">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body px-3 py-4">
              <h3 class="mb-3">Hasil Ujian</h3>
              <div class="row justify-content-between gap-2">
                <div class="col-auto row g-2 align-items-center flex-grow-1">
                  @if ($exam->not_yet_rated)
                    <div class="d-flex flep-wrap align-items-center col-auto">
                      <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum
                        dinilai <span class="badge ms-1 badge-danger">{{ $exam->not_yet_rated }}</span>
                      </span>
                    </div>
                  @endif
                  <div class="d-flex align-items-center w-100 gap-2 col-12" style="max-width:400px">
                    <i data-feather="search"></i>
                    <input type="type" class="form-control w-100" placeholder="Cari nama mahasiswa"
                      id="globalSearch" />
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center col-12 justify-content-between col-md-auto">
                  <form action="{{ route('user.exam.result.export', ['id' => $exam->id]) }}" method="GET"
                    class="d-inline" id="export-excel">
                    @csrf
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                      <i class="fa-solid fa-file"></i>
                      <span>
                        Export Excel
                      </span>
                    </button>
                  </form>
                  <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2" id="reset-all"
                    data-id="{{ $exam->id }}">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>
                      Reset Semua
                    </span>
                  </button>
                  <a class="btn btn-outline-info d-flex align-items-center gap-2 {{ $exam->results_count > 0 ? '' : 'pe-none' }}"
                    {{ $exam->results->first()?->id ? 'href=' . route('user.exam.evaluation', ['exam_id' => $exam->id]) : '' }}>
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
                            <table class="table table-bordered" id="exam-result-table">
                              <thead>
                                <tr>
                                  <th>No</th>
                                  <th>Nama</th>
                                  <th>Pengerjaan</th>
                                  <th>Nilai</th>
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
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script>
    const task_id = @json($exam->id)
  </script>
  <script src={{ asset('assets/js/exam-result.js') }}></script>
@endsection
