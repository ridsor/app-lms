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
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Uji Kompetensi Keahlian (Praktik)</h3>
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

    <div class="row">
      {{-- Informasi UKK --}}
      <div class="col-xl-8 col-lg-7">
        <div class="card rounded-responsive">
          <div class="card-body">
            <h5>{{ $ukk->title }}</h5>
            <div class="mt-3">
              <label class="form-label fw-bold">Instruksi:</label>
              <div class="ql-editor p-0">{!! $ukk->instructions !!}</div>
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

      {{-- Form Pengumpulan --}}
      <div class="col-xl-4 col-lg-5">
        <div class="card rounded-responsive">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center justify-content-between gap-2">
              <h5>Status Pengumpulan</h5>
              <span class="text-{{ $practice_result?->submitted_at ? 'secondary' : 'danger' }}">
                {{ $practice_result?->submitted_at ? 'Diserahkan' : 'Belum Diserahkan' }}
              </span>
            </div>
          </div>
          <div class="card-body">
            @if ($practice_result && $practice_result->graded_at)
              <div class="card border mb-3">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between">
                    <span class="fw-bold">Nilai:</span>
                    <span class="badge badge-primary fs-6">{{ $practice_result->formatted_score }}</span>
                  </div>
                  @if ($practice_result->feedback)
                    <div class="mt-2">
                      <small class="text-muted d-block">Feedback:</small>
                      <p class="mb-0">{{ $practice_result->feedback }}</p>
                    </div>
                  @endif
                </div>
              </div>
            @endif

            <div class="d-flex flex-column gap-1 mb-2" id="ukk-submission-preview">
              <div class="d-flex justify-content-center align-items-center p-2 w-100">
                <i class="fa-solid fa-arrows-rotate fa-spin"></i>
              </div>
            </div>

            @role('student')
              @php
                $now = now();
                $can_submit = $now >= $ukk->start_time && $now <= $ukk->end_time;
              @endphp

              @if ($can_submit)
                <div class="mb-3">
                  <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse"
                    data-bs-target="#addContent" aria-expanded="false" aria-controls="addContent">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <i data-feather="plus" style="width: 20px; height:20px"></i>
                      <span>Tambah</span>
                    </div>
                  </button>
                  <div class="collapse" id="addContent">
                    <div class="d-flex flex-column w-100">
                      <label
                        class="mb-0 btn btn-light rounded-0 border-0 text-inherit w-100 text-center p-2 tasksubmission-content-item">
                        <p class="mb-0">File</p>
                        <input type="file" hidden multiple
                          accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mp3,.kml,.gpx,.geojson"
                          id="ukk-submission-content-file">
                      </label>
                      <button data-bs-toggle="modal" data-bs-target="#linkModal"
                        class="btn btn-light rounded-0 border-0 text-inherit w-100 text-center p-2 tasksubmission-content-item">
                        <p class="mb-0">Link</p>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Deskripsi / Catatan (Opsional)</label>
                  <textarea id="practice-description" class="form-control" rows="3">{{ $practice_result->contents['description'] ?? '' }}</textarea>
                </div>

                <button class="btn btn-primary w-100 mb-3" id="submit-practice" disabled data-id="{{ $ukk->id }}">
                  Simpan
                </button>
              @else
                <div class="alert alert-danger mb-0">
                  <i class="fa fa-exclamation-triangle me-2"></i>
                  @if ($now < $ukk->start_time)
                    Pengumpulan belum dibuka.
                  @else
                    Waktu pengumpulan sudah berakhir.
                  @endif
                </div>
              @endif
            @endrole
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Link --}}
  <div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="linkModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content category-popup">
        <div class="modal-body">
          <div class="text-start">
            <p class="fs-6 mb-0">Tambahkan Link</p>
            <form class="row g-3" method="POST" id="ukk-submission-content-link">
              <div class="col-12">
                <label class="form-label" for="addLink">Link<span class="txt-danger">*</span></label>
                <input class="form-control" id="addLink" type="url" placeholder="https://" autocomplete="off"
                  required name="link">
              </div>
              <div class="col-md-12">
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                  <button class="btn btn-primary" type="submit">Tambahkan</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    let ukkSubmissionContents = @json($practice_result->contents ?? ['description' => '', 'files' => [], 'links' => []]);
    if (!ukkSubmissionContents.files) ukkSubmissionContents.files = [];
    if (!ukkSubmissionContents.links) ukkSubmissionContents.links = [];
    const deleteContent = [];
    const ukkId = "{{ $ukk->id }}";
  </script>
  <script src="{{ asset('assets/js/content-upload.js') }}"></script>
  <script src="{{ asset('assets/js/ukk-practice-submission.js') }}"></script>
@endsection
