@extends('layouts.app')

@section('title', 'Pengerjaan Ujian')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/glightbox.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
  <div class="container-fluid my-4 p-0 px-md-2">

    <!-- Header: Timer + Progress -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
      <div>
        <h4>{{ $exam->title }}</h4>
      </div>

      @if (!is_null($exam->duration))
        <div class="text-center">
          <span class="fw-bold">Waktu tersisa:</span>
          <span id="countdown" class="badge bg-danger fs-6">00:00</span>
        </div>
      @endif
    </div>

    <!-- Progress bar -->
    <div class="mb-4">
      <div class="progress" style="height: 15px;">
        <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0"
          aria-valuemin="0" aria-valuemax="100">
        </div>
      </div>
    </div>

    <div class="row g-2 p-0">
      <div class="col-md-9 mb-3">
        <div id="question-area" data-id="">
          <div class="card mb-3 rounded-responsive">
            <div class="card-body">
              <div id="question-loading">
                <div class="p-3 d-flex justify-content-center">
                  <i class="fa-solid fa-arrows-rotate fa-spin fs-3"></i>
                </div>
              </div>
              <div class="question-content col order-3 order-md-2">
                <div class="d-flex flex-column">
                  <div class="mb-2">
                    <div class="ql-editor text-wrap h-auto p-0" id="question-text">
                    </div>
                  </div>
                  <div id="question-file" class="mb-2">

                  </div>
                  <div class="question-answer w-100" id="question-answer">
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- end contoh soal --}}
        </div>
      </div>

      <!-- Navigasi Soal -->
      <div class="col-md-3">
        <div class="card">
          <div class="card-header p-2 text-center">
            <div class="fw-medium">
              Soal
            </div>
          </div>
          <div class="card-body d-flex flex-wrap gap-2 justify-content-center">
            @foreach ($questions as $i => $question)
              <button type="button" style="font-size: 14px; padding: .5rem .8rem"
                class="btn btn-outline-secondary question-nav nav-q{{ ++$i }}" data-q="{{ $i }}"
                id="nav-q{{ $question['id'] }}{{ $question['question_type'] }}">
                {{ $i }}
              </button>
            @endforeach
          </div>
          <div class="card-footer text-center">
            <button class="btn btn-sm btn-primary w-100" id="btn-submit">Kirim</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src={{ asset('assets/js/glightbox.min.js') }}></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script>
    let hasDuration = {{ !is_null($exam->duration) ? 1 : 0 }};
    let duration = {{ $examResult?->remainingDuration ? round($examResult->remainingDuration * 60) : 0 }};
    let total = {{ $exam?->multipleQuestions->count() + $exam?->essayQuestions->count() ?? 0 }};
    const routeResult = @json(route('user.exam.workmanship.result', $exam->id));
    const exam_id = @json($exam->id);
    const old_answer = @json($examResult?->answers);
  </script>
  <script src="{{ asset('assets/js/exam-workmanship.js') }}"></script>
@endsection
