@extends('layouts.user.app')

@section('title', 'Hasil UKK Teori')

@section('styles')
  <style>
    .wrapper-result {
      background: #eee;
    }

    .dark-only .wrapper-result {
      background: #1d1e26 !important;
    }

    .progress-bar-circle {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
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
            <li class="breadcrumb-item">
              <a href="{{ route('user.ukk.index') }}">
                Uji Kompetensi Keahlian
              </a>
            </li>
            <li class="breadcrumb-item active">Hasil</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid e-category p-0">
    <div class="card h-100 my-0 rounded-responsive mb-3">
      <div class="card-body p-3">
        <h1 class="text-center mb-3">Selesai Mengerjakan UKK Teori</h1>
        <div class="wrapper-result p-3 mb-3">
          <div class="text-center mb-3">
            <p class="mb-0 fs-5 text-success">Terima kasih, Anda telah menyelesaikan Uji Kompetensi Keahlian Teori.</p>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-12">
            <div class="col-12">
              <label class="form-label">Judul</label>
              <p class="c-o-light f-w-600">
                <span>
                  {{ $ukk->title }}
                </span>
              </p>
            </div>
          </div>
          <div class="col-12">
            <div>
              <label class="form-label">Instruksi</label>
            </div>
            @if ($ukk?->instructions)
              <div class="ql-editor text-wrap h-auto p-0">
                {!! $ukk?->instructions !!}
              </div>
            @else
              <span>-</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
