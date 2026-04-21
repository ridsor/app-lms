@props(['question', 'number'])
@php
  use App\Helpers\Helper;
  $qType = $question instanceof \App\Models\EssayQuestion ? 'essay' : 'multiple';
@endphp

<div class="col item">

  <div class="row g-2">
    <span class="question-number fw-bold col-auto order-2 order-md-1">
      {{ $number }}.
    </span>
    <div class="question-content col order-3 order-md-2">
      <div class="d-flex flex-column">
        <div class="question-text mb-2">
          <div class="ql-editor text-wrap h-auto p-0">
            {!! $question->question_text !!}
          </div>
        </div>
        @if ($question->question_file)
          <div class="question-file mb-2">
            @switch(Helper::getFileType($question->question_file))
              @case('image')
                <div class="image">
                  <a href="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType]) }}"
                    class="glightbox" data-gallery="question-{{ $question->id }}" data-type="image">
                    <img src="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType]) }}"
                      alt="soal gambar"
                      style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                  </a>
                </div>
              @break

              @case('audio')
                <div class="audio">
                  <audio controls>
                    @if (strtolower(pathinfo($question->question_file, PATHINFO_EXTENSION)) == 'mp3')
                      <source src="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType]) }}"
                        type="audio/mpeg">
                    @endif
                    @if (strtolower(pathinfo($question->question_file, PATHINFO_EXTENSION)) == 'mav')
                      <source src="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType]) }}" type="audio/wav">
                    @endif

                    Browser Anda tidak mendukung elemen audio.
                  </audio>
                </div>
              @break

              @case('video')
                <div class="video">
                  <video width="320" height="240" controls>
                    <source src="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType]) }}"
                      type="video/mp4">
                    Browser Anda tidak mendukung elemen video.
                  </video>
                </div>
              @break

              @case('archive')
                <div class="archive">
                  <div class="rounded-2 d-flex align-items-center gap-3">
                    <div style="display:flex;align-items:center;justify-content:center;min-width:24px;min-height:24px;">
                      <i class="fa fa-file text-primary fs-3"></i>
                    </div>
                    <div class="fw-medium text-break">
                      File Arsip
                    </div>
                    <a href="{{ route('user.question.file.download', ['id' => $question->id, 'type' => $qType]) }}"
                      style="width: 32px; height: 32px;"
                      class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                      <i data-feather="download" style="width: 20px; height: 20px"></i>
                    </a>
                  </div>
                </div>
              @break

              @case('document')
                <div class="document">
                  <div class="rounded-2 d-flex align-items-center gap-3">
                    <div style="display:flex;align-items:center;justify-content:center;min-width:24px;min-height:24px;">
                      <i class="fa fa-file text-primary fs-3"></i>
                    </div>
                    <div class="fw-medium text-break">
                      Dokumen
                    </div>
                    <a href="{{ route('user.question.file.download', ['id' => $question->id, 'type' => $qType]) }}"
                      style="width: 32px; height: 32px;"
                      class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                      <i data-feather="download" style="width: 20px; height: 20px"></i>
                    </a>
                  </div>
                </div>
              @break

              @default
            @endswitch
          </div>
        @endif
        @if ($qType === 'multiple')
          <div class="question-options">
            <div class="text-warning mb-2">
              Kunci Jawaban
            </div>
            <div class="option-list d-flex flex-column gap-2">
              <div class="option-item checkbox-checked">
                <div class="d-flex align-items-center gap-2">
                  <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                    <input type="radio" @if ($question->correct_answer == 'a') checked @endif value="a" disabled
                      class="me-2 form-check-input" style="transform: translateY(-2px)">
                    <span class="fw-bold text-uppercase">a.</span>
                  </label>
                  <div class="option-label">
                    <p class="mb-0">
                      {{ $question->option_a }}
                    </p>
                    @if ($question->option_a_image)
                      <div class="option-image mt-1">
                        <a href="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'a']) }}"
                          class="glightbox" data-type="image" data-gallery="option-{{ $question->id }}-a">
                          <img
                            src="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'a']) }}"
                            alt="opsi gambar"
                            style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                        </a>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="option-item checkbox-checked">
                <div class="d-flex align-items-center gap-2">
                  <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                    <input type="radio" @if ($question->correct_answer == 'b') checked @endif value="b" disabled
                      class="me-2 form-check-input" style="transform: translateY(-2px)">
                    <span class="fw-bold text-uppercase">b.</span>
                  </label>
                  <div class="option-label">
                    <p class="mb-0">
                      {{ $question->option_b }}
                    </p>
                    @if ($question->option_b_image)
                      <div class="option-image mt-1">
                        <a href="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'b']) }}"
                          class="glightbox" data-type="image" data-gallery="option-{{ $question->id }}-b">
                          <img
                            src="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'b']) }}"
                            alt="opsi gambar"
                            style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                        </a>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="option-item checkbox-checked">
                <div class="d-flex align-items-center gap-2">
                  <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                    <input type="radio" @if ($question->correct_answer == 'c') checked @endif value="c" disabled
                      class="me-2 form-check-input" style="transform: translateY(-2px)">
                    <span class="fw-bold text-uppercase">c.</span>
                  </label>
                  <div class="option-label">
                    <p class="mb-0">
                      {{ $question->option_c }}
                    </p>
                    @if ($question->option_c_image)
                      <div class="option-image mt-1">
                        <a href="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'c']) }}"
                          class="glightbox" data-type="image" data-gallery="option-{{ $question->id }}-c">
                          <img
                            src="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'c']) }}"
                            alt="opsi gambar"
                            style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                        </a>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              @if ($question->option_d)
                <div class="option-item checkbox-checked">
                  <div class="d-flex align-items-center gap-2">
                    <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                      <input type="radio" @if ($question->correct_answer == 'd') checked @endif value="d" disabled
                        class="me-2 form-check-input" style="transform: translateY(-2px)">
                      <span class="fw-bold text-uppercase">d.</span>
                    </label>
                    <div class="option-label">
                      <p class="mb-0">
                        {{ $question->option_d }}
                      </p>
                      @if ($question->option_d_image)
                        <div class="option-image mt-1">
                          <a href="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'd']) }}"
                            class="glightbox" data-type="image" data-gallery="option-{{ $question->id }}-d">
                            <img
                              src="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'd']) }}"
                              alt="opsi gambar"
                              style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                          </a>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endif
              @if ($question->option_e)
                <div class="option-item checkbox-checked">
                  <div class="d-flex align-items-center gap-2">
                    <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                      <input type="radio" @if ($question->correct_answer == 'e') checked @endif value="e" disabled
                        class="me-2 form-check-input" style="transform: translateY(-2px)">
                      <span class="fw-bold text-uppercase">e.</span>
                    </label>
                    <div class="option-label">
                      <p class="mb-0">
                        {{ $question->option_e }}
                      </p>
                      @if ($question->option_e_image)
                        <div class="option-image mt-1">
                          <a href="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'e']) }}"
                            class="glightbox" data-type="image" data-gallery="option-{{ $question->id }}-e">
                            <img
                              src="{{ route('user.question.option.file.get', ['id' => $question->id, 'option' => 'e']) }}"
                              alt="opsi gambar"
                              style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                          </a>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

        @endif
      </div>
    </div>
    <div class="d-flex justify-content-end col-12 order-1 order-md-3 col-md-auto">
      <div class="d-flex gap-2 align-items-center">
        @role(['operator', 'teacher'])
          <div class="bg-white">
            <button onclick="handleEditQuestion(event, {{ $question->id }}, '{{ $qType }}')"
              class="edit btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
              style="width: 38px; height: 38px;">
              <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
            </button>
          </div>
          <div class="bg-white">
            <button onclick="handleDeleteQuestion(event, {{ $question->id }}, '{{ $qType }}')"
              style="width: 38px; height: 38px;"
              class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2">
              <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
            </button>
          </div>
        @endrole
        <div class="border rounded-3 d-flex px-3 py-2 align-items-center gap-2">
          <img src="{{ asset('assets/svg/star.svg') }}" alt="star" />
          <p class="mb-0">
            <span class="question-poin">
              {{ $question->question_points }}
            </span>
            Poin
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
