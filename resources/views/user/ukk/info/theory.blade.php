@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'UKK')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <style>
    .view_file_path .Archive,
    .view_file_path .Link {
      background: #f5f6f9 !important;
    }

    .dark-only .view_file_path .Archive,
    .dark-only .view_file_path .Link {
      background: #1d1e26 !important;
    }

    .content-item {
      transition: all .3s;
    }

    .content-item:hover,
    .content-item:focus {
      background: rgba(0, 0, 0, .1);
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Uji Kompetensi Keahlian (Teori)</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">
              <a href="{{ route('user.ukk.index') }}">
                Uji Kompetensi Keahlian
              </a>
            </li>
          </ol>
        </div>
      </div>
    </div>
    <div class="container-fluid e-category p-0">
      <div class="row g-2 mb-3">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="row ">
                    <div class="col-12">
                      <label class="form-label">Jurusan</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $ukk->major }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Jenis UKK</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $ukk->type }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Periode</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $ukk->period->academic_year }} -
                          {{ $ukk->period->semester === 'even' ? 'Genap' : 'Ganjil' }}
                        </span>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="row g-2">
                    <div class="col-12">
                      <label class="form-label">Durasi</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $ukk?->duration ?: '-' }} Menit
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Waktu Mulai</label>
                      <div class="c-o-light f-w-600">
                        <div class="d-flex align-items-center">
                          <span class="icon d-inline-flex justify-content-center align-items-center">
                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                          </span>
                          <span class="mb-0 ms-2"
                            id="date">{{ $ukk?->start_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Waktu Selesai</label>
                      <div class="c-o-light f-w-600">
                        <div class="d-flex align-items-center">
                          <span class="icon d-inline-flex justify-content-center align-items-center">
                            <i data-feather="calendar" style="width:18px; height: 18px"></i>
                          </span>
                          <span class="mb-0 ms-2"
                            id="date">{{ $ukk?->end_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
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
      <div class="row g-2 mb-4">
        <div class="col-12 col-lg-7 col-xl-8 p-0 order-2 order-lg-1">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body">
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
                  <div class="col-12 mt-3 view_file_path">
                    <div class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1 text-center">
                      <div style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                        <i class="fa fa-file text-primary fs-2"></i>
                      </div>
                      <div class="fw-medium text-break" style="font-size: .8rem">
                        {{ $ukk?->file_name . ' (' . number_format($ukk?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                      </div>
                      <div class="d-flex gap-2">
                        <a href="{{ URL::temporarySignedRoute('user.ukk.file.get', now()->addMinutes(60), ['id' => $ukk->id]) }}"
                          target="_blank" style="width: 38px; height: 38px;"
                          class="btn d-flex align-items-center bg-20-primary border justify-content-center text-primary p-2">
                          <i data-feather="eye" style="width: 20px; height: 20px"></i>
                        </a>
                        <a href="{{ route('user.ukk.file.download', $ukk->id) }}" style="width: 38px; height: 38px;"
                          class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                          <i data-feather="download" style="width: 20px; height: 20px"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                  @if ($ukk->file_path)
                    <div class="col-12 mt-3 view_file_path">
                      @if (Helper::isPreviewable($ukk->file_name))
                        <div class="mt-4 border rounded-2 overflow-hidden bg-light"
                          style="min-height: 400px; max-height: 800px;">
                          @php
                            $fileUrl = URL::temporarySignedRoute(
                                'user.ukk.file.get', // nama route
                                now()->addMinutes(60), // masa berlaku
                                ['id' => $ukk->id],
                            );
                            $fileType = Helper::getFileType($ukk->file_name);
                          @endphp

                          @if ($fileType == 'image')
                            <div class="d-flex justify-content-center align-items-center p-3 h-100">
                              <img src="{{ $fileUrl }}" alt="{{ $ukk->file_name }}"
                                style="max-width: 100%; height: auto; object-fit: contain;">
                            </div>
                          @elseif (Helper::isGooglePreviewable($ukk->file_name))
                            <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true"
                              width="100%" height="600px" frameborder="0"></iframe>
                          @else
                            <iframe src="{{ $fileUrl }}" width="100%" height="600px" frameborder="0"></iframe>
                          @endif
                        </div>
                      @else
                        <div
                          class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1 text-center">
                          <div
                            style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                            <i class="fa fa-file text-primary fs-2"></i>
                          </div>
                          <div class="fw-medium text-break" style="font-size: .8rem">
                            {{ $ukk?->file_name . ' (' . number_format($ukk?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                          </div>
                          <a href="{{ route('user.ukk.file.download', $ukk->id) }}" style="width: 38px; height: 38px;"
                            class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                            <i data-feather="download" style="width: 20px; height: 20px"></i>
                          </a>
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 order-1 order-lg-2">
          @php
            $now = now();
            $is_ukk_available =
                $ukk->start_time <= $now &&
                $ukk->end_time >= $now &&
                $ukk_result?->status != 'completed' &&
                $ukk?->multipleQuestions->count() + $ukk?->essayQuestions->count() > 0;
          @endphp
          <div class="p-3">
            <div class="mb-2">
              {!! Helper::getExamStatusLabel($ukk_result?->status) !!}
            </div>

            @if ($ukk_result?->status == 'completed')
              <div class="mb-3">
                <a href="{{ route('user.ukk.teori.workmanship.result', $ukk->id) }}"
                  class="btn btn-primary w-100">Lihat
                  Hasil</a>
              </div>
            @endif

            <div class="d-flex mb-3 justify-content-between align-items-center">
              <p class="mb-0 fw-semibold fs-6">Nilai</p>
              <input class="form-control text-center" type="number" style="width: 70px" disabled
                value="{{ $ukk_result?->formatted_score }}" name="score" step="0.1" />
            </div>

            <div class="mb-3">
              <label class="form-label">Waktu Pengerjaan</label>
              <div class="c-o-light f-w-600">
                <div class="d-flex align-items-center">
                  <span class="icon d-inline-flex justify-content-center align-items-center">
                    <i data-feather="calendar" style="width:18px; height: 18px"></i>
                  </span>
                  @if ($ukk_result?->status == 'completed')
                    <div class="d-flex flex-column">
                      <span class="mb-0 ms-2" id="date">Mulai
                        {{ $ukk_result?->created_at->translatedFormat('j M Y H:i') . ' WIT' ?: '-' }}</span>
                      <span class="mb-0 ms-2" id="date">Selesai
                        {{ $ukk_result?->updated_at->translatedFormat('j M Y H:i') . ' WIT' ?: '-' }}</span>
                    </div>
                  @else
                    <span class="mb-0 ms-2" id="date">-</span>
                  @endif
                </div>
              </div>
            </div>

            @if ($is_ukk_available)
              <div class="mb-3">
                <div class="text-center">
                  <span class="fw-bold">Waktu tersisa:</span>
                  <span id="countdown" class="badge bg-danger fs-6">00:00:00</span>
                </div>
              </div>
            @endif

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('user.ukk.index') }}" class="btn btn-outline-secondary" type="button"
                aria-label="Close">
                Kembali
              </a>
              @role('student')
                {{-- <button data-href="{{ route('user.ukk.workmanship', $ukk->id) }}" type="button" id="start-exam-btn" --}}
                <button type="button" id="start-ukk-btn" data-id="{{ $ukk->id }}" class="btn btn-primary"
                  {{ $is_ukk_available ? '' : 'disabled' }}>Mulai</button>
              @endrole
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script>
    const duration = {{ $ukk_result?->remaining_seconds ?? $ukk->remaining_seconds }};
  </script>
  <script src="{{ asset('assets/js/ukk-info.js') }}"></script>
@endsection
