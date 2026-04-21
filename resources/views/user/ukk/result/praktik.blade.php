@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Hasil UKK')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Ujian Kompetensi Keahlian</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">
              <a href="{{ route('user.ukk.show', ['id' => $ukk->id]) }}">
                Ujian Kompetensi Keahlian
              </a>
            </li>
            <li class="breadcrumb-item active">
              Hasil
            </li>
          </ol>
        </div>
      </div>
    </div>
    @include('user.ukk.menu')
    <div class="container-fluid e-category p-0">
      <div class="row g-0 mb-4">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body px-3 py-4">
              <h3 class="mb-3">Hasil</h3>
              <div class="row justify-content-between gap-2">
                <div class="col-auto row g-2 align-items-center flex-grow-1">
                  <div class="d-flex align-items-center w-100 gap-2 col-12" style="max-width:400px">
                    <i data-feather="search"></i>
                    <input type="type" class="form-control w-100" placeholder="Cari nama siswa" id="globalSearch" />
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center col-12 justify-content-between col-md-auto">
                  {{-- Future Export and Reset buttons --}}
                </div>
              </div>
              <div class="e-category">
                <div class="row">
                  <div class="col-12 px-0">
                    <div class="card rounded-responsive shadow-none">
                      <div class="card-body px-0 pt-0">
                        <div class="list-product list-category text-center py-5">
                          <img style="width: 120px; height: 120px" src="{{ asset('assets/images/data-empty.png') }}" />
                          <p class="fw-semibold mb-0 text-center">Fitur Hasil Praktik Segera Hadir</p>
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
