@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'UKK')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
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
          </ol>
        </div>
      </div>
    </div>
    @include('user.ukk.menu')
    <div class="container-fluid e-category p-0">
      <div class="row g-0 mb-4">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                @role(['student', 'parent'])
                  @php
                    $is_done = $ukk->type === 'Teori'
                      ? $ukk->results->where('student_id', $studentId)->isNotEmpty()
                      : $ukk->practiceResults->where('student_id', $studentId)->isNotEmpty();
                  @endphp
                  @if (!$is_done)
                    <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">
                      Belum dikerjakan
                    </span>
                  @else
                    <span class="badge m-0 badge-light-success px-2 py-1 d-flex align-items-center">
                      Sudah dikerjakan
                    </span>
                  @endif
                @endrole
                @can(['ukk.edit', 'ukk.delete'])
                  <div class="d-flex justify-content-end flex-grow-1 align-items-center gap-2">
                    <button
                      class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                      style="width: 38px; height: 38px;" onclick="handleEditUkk(event, {{ $ukk->id }})">
                      <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                    </button>
                    <button class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                      style="width: 38px; height: 38px;" data-redirect="{{ route('user.ukk.index') }}"
                      onclick="handleDeleteUkk(event, {{ $ukk->id }})">
                      <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                    </button>
                  </div>
                @endrole
              </div>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="row">
                    <div class="col-12">
                      <label class="form-label">Judul</label>
                      <p class="c-o-light f-w-600">
                        <span>{{ $ukk->title }}</span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Operator</label>
                      <p class="c-o-light f-w-600">
                        <span>{{ $ukk->operator->name }}</span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Jurusan</label>
                      <p class="c-o-light f-w-600">
                        <span>{{ $ukk->major }}</span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Jenis UKK</label>
                      <p class="c-o-light f-w-600">
                        <span>{{ $ukk->type }}</span>
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
                        <span>{{ $ukk?->duration ?: '-' }} Menit</span>
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

                @if ($ukk->file_path)
                  <div class="col-12 mt-3 view_file_path">
                    <div class="Archive py-3 px-3 mx-2 rounded-2 d-flex align-items-center flex-column gap-1 text-center">
                      <div style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;">
                        <i class="fa fa-file text-primary fs-2"></i>
                      </div>
                      <div class="fw-medium text-break" style="font-size: .8rem">
                        {{ $ukk?->file_name . ' (' . number_format($ukk?->file_size / (1024 * 1024), 2) . 'mb)' ?? '-' }}
                      </div>
                      <div class="d-flex gap-2">
                        <a href="{{ route('user.ukk.file.get', $ukk->id) }}" target="_blank"
                          style="width: 38px; height: 38px;"
                          class="btn d-flex align-items-center bg-20-primary border justify-content-center text-primary p-2">
                          <i data-feather="eye" style="width: 20px; height: 20px"></i>
                        </a>
                        <a href="{{ route('user.ukk.file.download', $ukk->id) }}" style="width: 38px; height: 38px;"
                          class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                          <i data-feather="download" style="width: 20px; height: 20px"></i>
                        </a>
                      </div>
                    </div>

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
                    @endif
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('user.ukk.modal')
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('assets/js/ukk.js') }}"></script>
@endsection
