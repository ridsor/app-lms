@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Hasil UKK Praktik')

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

    #ukk-practice-result-table_wrapper .dt-search {
      display: none !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Uji Kompetensi Keahlian</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.ukk.index') }}">Uji Kompetensi Keahlian</a></li>
            <li class="breadcrumb-item active">Hasil</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="container-fluid e-category p-0">
      {{-- Tab Menu --}}
      <div class="row g-0 mb-4">
        <div class="col-12 p-0">
          <div class="card rounded-responsive">
            <div class="card-body">
              <ul class="d-flex gap-2 row-gap-3 flex-wrap">
                <li>
                  <a href="{{ route('user.ukk.show', $ukk->id) }}" class="py-2 px-2 text-secondary">Info UKK</a>
                </li>
                <li>
                  <a href="{{ route('user.ukk.result.praktik', $ukk->id) }}"
                    class="py-2 px-2 border-bottom border-primary">Hasil</a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        {{-- Content Table --}}
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body px-3 py-4">
              <h3 class="mb-3">Hasil</h3>

              <div class="row justify-content-between gap-2">
                <div class="col-auto row gap-2 align-items-center flex-grow-1">
                  <div class="d-flex flep-wrap align-items-center col-auto">
                    <span class="badge m-0 badge-light-primary px-2 py-1 d-flex align-items-center">
                      Selesai dikerjakan <span class="badge ms-1 badge-primary">{{ $ukk->completed_count }}</span>
                    </span>
                  </div>
                  <div class="d-flex align-items-center w-100 gap-2 col-12" style="max-width:400px">
                    <i data-feather="search"></i>
                    <input type="type" class="form-control w-100" placeholder="Cari nama siswa" id="globalSearch" />
                  </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 col-12 justify-content-between col-md-auto">
                  <a href="{{ route('user.ukk.result.praktik.export', ['id' => $ukk->id]) }}" class="btn btn-success d-flex align-items-center gap-2">
                      <i class="fa-solid fa-file"></i>
                      <span>
                        Export Excel
                      </span>
                  </a>

                  <a class="btn btn-outline-info d-flex align-items-center gap-2 {{ $ukk->practice_results_count > 0 ? '' : 'pe-none' }}"
                    href="{{ route('user.ukk.praktik.evaluation', ['id' => $ukk->id]) }}">
                    <span>Penilaian</span>
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
                            <table class="table table-bordered" id="ukk-practice-result-table">
                              <thead>
                                <tr>
                                  <th>No</th>
                                  <th>Nama</th>
                                  <th>Pengumpulan</th>
                                  <th>Nilai</th>
                                  <th>Kesimpulan</th>
                                  <th>Penilai</th>
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
  <script src="{{ asset('assets/js/ukk-practice-result.js') }}"></script>
@endsection
