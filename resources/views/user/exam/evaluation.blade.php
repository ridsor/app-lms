@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Ujian')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/glightbox.min.css') }}">
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
    <div class="container-fluid e-category p-0">
      <div class="row g-2 mb-3">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="row ">
                    <div class="col-12">
                      <label class="form-label">Judul</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $exam->title }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Mata Pelajaran</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ strtoupper($exam->schedule->subject->name) }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Kode Matpel</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $exam->schedule->subject->code }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Kelas</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $exam->schedule->class->name }}{{ $exam->schedule->class->level }}
                        </span>
                      </p>
                    </div>
                    @if ($exam->schedule->class->major)
                      <div class="col-12">
                        <label class="form-label">Jurusan</label>
                        <p class="c-o-light f-w-600">
                          <span>
                            {{ $exam->schedule->class->major->name }}
                          </span>
                        </p>
                      </div>
                    @endif
                    <div class="col-12">
                      <label class="form-label">Tipe Ujian</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ Helper::getExamTypeLabel($exam->type) }}
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
                          {{ $exam?->duration ?: '-' }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Jumlah Soal</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $exam?->multipleQuestions->count() + $exam?->essayQuestions->count() ?: '-' }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Sifat</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ Helper::getExamModeLabel($exam->exam_mode) }}
                        </span>
                      </p>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Acak Soal</label>
                      <p class="c-o-light f-w-600">
                        <span>
                          {{ $exam->is_shuffle_questions ? 'Ya' : 'Tidak' }}
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
                            id="date">{{ $exam?->start_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
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
                            id="date">{{ $exam?->end_time->translatedFormat('j M Y H:i') . ' WIT' ?? '-' }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div>
                    <label class="form-label">Deskripsi</label>
                  </div>
                  @if ($exam?->description)
                    <div class="ql-editor text-wrap h-auto p-0">
                      {!! $exam?->description !!}
                    </div>
                  @else
                    <span>-</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-3 px-3">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <a {{ $exam_results->onFirstPage() ? 'aria-disabled="true"' : '' }} role="button"
            {{ $exam_results->onFirstPage() ? '' : 'href=' . route('user.exam.evaluation', ['exam_id' => $exam->id, 'page' => $exam_results->currentPage() - 1]) }}
            class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ $exam_results->onFirstPage() ? 'disabled' : '' }}">
            <i data-feather="chevron-left" style="width:18px; height: 18px"></i>
          </a>
          <div class="d-flex flex-column align-items-center justify-content-center px-2">
            <p class="mb-0 fw-medium text-break">
              {{ $exam_result->student->name }}
            </p>
            <p class="f-light mb-0 text-break">{{ $exam_result->student->nisn }}</p>
          </div>
          <a {{ $exam_results->hasMorePages() ? 'href=' . route('user.exam.evaluation', ['exam_id' => $exam->id, 'page' => $exam_results->currentPage() + 1]) : '' }}
            role="button" {{ !$exam_results->hasMorePages() ? 'aria-disabled="true"' : '' }}
            class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ !$exam_results->hasMorePages() ? 'disabled' : '' }}">
            <i data-feather="chevron-right" style="width:18px; height: 18px"></i>
          </a>
        </div>
      </div>
      <div class="row g-3 align-items-center mb-3">
        <form action="" class="d-flex align-items-center col gap-2">
          <input type="search" class="form-control w-100" value="{{ request()->query('search') }}"
            placeholder="Cari soal" name="search" id="globalSearch" />
          <button class="btn btn-primary gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
            <i data-feather="search" style="width:18px; height:18px"></i>
          </button>
        </form>
      </div>
      <div class="row g-4 flex-column list-question px-md-3 mb-3">
        @if (count($questions) > 0)
          @foreach ($questions as $index => $question)
            @include('user.question.evaluation-item', [
                'question' => $question,
                'number' => $questions->firstItem() + $loop->index,
            ])
          @endforeach
          <div class="pagination-wrapper w-100">
            {{ $questions->withQueryString()->links('pagination::bootstrap-5') }}
          </div>
        @else
          {{-- empty data --}}
          <div class="d-flex justify-content-center mb-3 w-100">
            <div class="px-4 py-5 d-grid" style="justify-items: center">
              <img style="width: 120px; height: 120px" src="{{ asset('assets/images/data-empty.png') }}" />
              <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
              <p class="mb-0 text-center">Belum ada soal.</p>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
  <script src={{ asset('assets/js/glightbox.min.js') }}></script>
  <script src="{{ asset('assets/js/exam-evaluation.js') }}"></script>
@endsection
