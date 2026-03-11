@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Ujian')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
  <style>
    button.dropdown-toggle[data-id='editSchedule'] .filter-option-inner-inner {
      text-transform: uppercase !important;
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
              <a href="{{ route('user.exam.index') }}">
                Ujian
              </a>
            </li>
          </ol>
        </div>
      </div>
    </div>
    @include('user.exam.menu')
    <div class="container-fluid e-category p-0">
      <div class="row g-0 mb-4">
        <div class="col-12 p-0">
          <div class="card h-100 my-0 rounded-responsive">
            <div class="card h-100 my-0 rounded-responsive">
              <div class="card-body">
                @role('operator')
                  <div class="d-flex justify-content-between align-items-center  mb-3">
                    <div class="d-flex justify-content-end flex-grow-1 align-items-center gap-2">
                      <button
                        class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                        style="width: 38px; height: 38px;" onclick="handleEditExam(event, {{ $exam->id }})">
                        <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                      </button>
                      <button
                        class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                        style="width: 38px; height: 38px;" data-redirect="{{ route('user.exam.index') }}"
                        onclick="handleDeleteExam(event, {{ $exam->id }})">
                        <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                      </button>
                    </div>
                    <div class="modal fade" id="editExamModal" tabindex="-1" aria-labelledby="editExamModal"
                      aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content category-popup">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Ujian</h5>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body p-0 custom-input">
                            <form action=""></form>
                            <form class="needs-validation" method="POST" action="" novalidate="" id="editExamForm">
                              <div class="text-start">
                                <div class="p-20">
                                  <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                      <div class="row g-3">
                                        <div class="col-12">
                                          <label class="form-label" for="editMaterialTitle">Judul<span
                                              class="txt-danger">*</span></label>
                                          <input class="form-control" id="editMaterialTitle" type="text"
                                            placeholder="Tulis judul" name="title">
                                          <div class="invalid-feedback">
                                          </div>
                                        </div>
                                        <div class="col-12">
                                          <label class="form-label" for="editExamType">Jenis Ujian<span
                                              class="txt-danger">*</span></label>
                                          <select class="form-select" id="editExamType" name="type">
                                            <option value="">Jenis Ujian
                                            </option>
                                            @foreach ($examTypes as $item)
                                              <option value="{{ $item['value'] }}">
                                                {{ $item['label'] }}</option>
                                            @endforeach
                                          </select>
                                          <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                          <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="startDate">Waktu Mulai<span
                                                class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" id="editExamStartTime" type="date"
                                              placeholder="Pilih waktu mulai" name="start_time" data-language="id">
                                            <div class="invalid-feedback"></div>
                                          </div>
                                        </div>
                                        <div class="col-12">
                                          <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="endDate">Waktu Selesai<span
                                                class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" autocomplete="off"
                                              id="editExamEndTime" type="date" placeholder="Pilih waktu selesai"
                                              name="end_time" data-language="id">
                                            <div class="invalid-feedback"></div>
                                          </div>
                                        </div>
                                        <div class="col-12">
                                          <label class="form-label" for="editSchedule">Jadwal<span
                                              class="txt-danger">*</span></label>
                                          <select class="selectpicker search-picker" data-live-search="true"
                                            id="editSchedule" name="schedule_id">
                                            <option value="">Pilih Jadwal
                                            </option>
                                            @foreach ($schedules as $schedule)
                                              <option value="{{ $schedule->id }}" class="text-uppercase">
                                                {{ $schedule->subject->code }}
                                                -
                                                {{ $schedule->subject->name }}
                                                -
                                                {{ $schedule->class->name }}{{ $schedule->class->level }}{{ ' ' . $schedule->class->major?->name ?? '' }}
                                              </option>
                                            @endforeach
                                          </select>
                                          <div class="invalid-feedback">
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                      <div class="row g-3">
                                        <div class="col-12">
                                          <label class="form-label" for="editExamDescription">Deskripsi<span
                                              class="txt-danger"></span></label>
                                          <div class="toolbar-box">
                                            <div id="editExamToolbar">
                                              <button class="ql-bold">Bold</button>
                                              <button class="ql-italic">Italic</button>
                                              <button class="ql-underline">underline</button>
                                              <button class="ql-strike">Strike</button>
                                              <button class="ql-list" value="ordered">List</button>
                                              <button class="ql-list" value="bullet"></button>
                                              <button class="ql-indent" value="-1"></button>
                                              <button class="ql-indent" value="+1"></button>
                                              <button class="ql-link"></button>
                                            </div>
                                            <div id="editExamDescriptionQuill">
                                            </div>
                                            <input type="hidden" id="editExamDescription" name="description"
                                              class="quill">
                                          </div>
                                          <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                          <label class="form-label" for="editAllowLateSubmission">Sifat</label>
                                          <div class="checkbox-checked d-flex gap-2">
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="Closed Book" checked name="exam_mode"
                                                class="me-2 form-check-input radio" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Tutup
                                                Buku</span>
                                            </label>
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="Open Book" class="me-2 form-check-input radio"
                                                name="exam_mode" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Buka
                                                Terbuka</span>
                                            </label>
                                          </div>
                                          <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                          <label class="form-label" for="editAllowLateSubmission">Acak
                                            Soal</label>
                                          <div class="checkbox-checked d-flex gap-2">
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="0" checked name="is_shuffle_questions"
                                                class="me-2 form-check-input radio" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Tidak</span>
                                            </label>
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="1" class="me-2 form-check-input radio"
                                                name="is_shuffle_questions" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Ya</span>
                                            </label>
                                          </div>
                                          <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                          <label class="form-label">Batas
                                            Waktu</label>
                                          <div class="checkbox-checked d-flex gap-2">
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="0" checked name="allow_duration"
                                                class="me-2 form-check-input radio" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Tidak</span>
                                            </label>
                                            <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                                              <input type="radio" value="1" class="me-2 form-check-input radio"
                                                name="allow_duration" style="transform: translateY(-2px)">
                                              <span class="fw-bold text-uppercase">Ya</span>
                                            </label>
                                          </div>
                                          <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 duration" style="display: none;">
                                          <div class="d-flex flex-column">
                                            <label class="form-label" for="editDurationExam">Batas
                                              Waktu</label>
                                            <input class="form-control" autocomplete="off" id="editDurationExam"
                                              type="number" placeholder="(menit)" name="duration">
                                            <div class="invalid-feedback"></div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                          aria-label="Close">
                                          Batal
                                        </button>
                                        <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endrole
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
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('assets/js/exam.js') }}"></script>
@endsection
