@props(['question', 'number', 'ukk'])
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
                  <a href="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType, 'v' => $question->updated_at?->timestamp]) }}"
                    class="glightbox" data-gallery="question-{{ $question->id }}" data-type="image">
                    <img src="{{ route('user.question.file.get', ['id' => $question->id, 'type' => $qType, 'v' => $question->updated_at?->timestamp]) }}"
                      alt="soal gambar"
                      style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                  </a>
                </div>
              @break
              {{-- Case audio, video, etc can be added here following same logic --}}
              @default
            @endswitch
          </div>
        @endif
        <div class="text-warning mb-2">
          Jawaban Siswa
        </div>
        @if ($qType === 'multiple')
          <div class="question-options">
            <div class="option-list d-flex flex-column gap-2">
              @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                @php $optField = "option_$opt"; @endphp
                @if($question->$optField)
                  <div class="option-item checkbox-checked">
                    <div class="d-flex align-items-center gap-2">
                      <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                        <input type="radio" @if ($question?->student_answer?->answer == $opt) checked @endif disabled
                          class="me-2 form-check-input" style="transform: translateY(-2px)">
                        <span class="fw-bold text-uppercase">{{ $opt }}.</span>
                      </label>
                      <div class="option-label">
                        <p class="mb-0 @if($question->correct_answer == $opt) text-success fw-bold @endif">
                          {{ $question->$optField }}
                          @if($question->correct_answer == $opt) (Kunci Jawaban) @endif
                        </p>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @else
          <div class="essay-answer border rounded-2 p-3 w-100">
            <p class="mb-0">
              {{ $question->student_answer->answer ?? '-' }}
            </p>
          </div>
        @endif
      </div>
    </div>
    <div class="d-flex justify-content-end col-12 order-1 order-md-3 col-md-auto">
      <div class="d-flex gap-2 align-items-center">
      </div>
    </div>
  </div>
</div>
