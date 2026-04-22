@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Uji Kompetensi Keahlian')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <style>
    button.dropdown-toggle[data-id='editSchedule'] .filter-option-inner-inner {
      text-transform: uppercase !important;
    }

    button.dropdown-toggle[data-id='addSchedule'] .filter-option-inner-inner {
      text-transform: uppercase !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Uji Kompetensi Keahlian</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active"><a>Uji Kompetensi Keahlian</a></li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="e-category container-fluid p-0">
    <div class="row g-0 mb-4">
      <div class="col-12 p-0">
        <div class="card h-100 my-0 rounded-responsive">
          <form method="GET" action="">
            <div class="card-body px-2 mb-2 px-md-3 py-4 common-offcanvas">
              @can('ukk.create')
                <button class="btn btn-primary f-w-500 mb-2 w-100" type="button" data-bs-toggle="modal"
                  data-bs-target="#addUkkModal"><i class="fa fa-plus pe-2"></i>Tambah UKK</button>
              @endcan
              <div class="row g-3 align-items-center mb-4">
                <div class="d-flex align-items-center col gap-2">
                  <i data-feather="search"></i>
                  <input type="search" class="form-control w-100" placeholder="Cari judul ukk"
                    value="{{ request()->query('cari') }}" name="cari" id="globalSearch" />
                </div>
                <div class="col-auto">
                  <button type="button" data-bs-toggle="offcanvas" data-bs-target="#filter" aria-controls="filter"
                    class="btn btn-outline-success gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
                    <i data-feather="filter" style="width:18px; height:18px"></i>
                    <span>Filter</span>
                  </button>
                  <div class="offcanvas offcanvas-end" id="filter" tabindex="-1" aria-labelledby="filterLabel">
                    <div class="offcanvas-header pb-0">
                      <h5 class="offcanvas-title" id="filterLabel">Filter</h5><button class="btn-close" type="button"
                        data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body custom-input custom-scrollbar">
                      <div class="row g-3">
                        <div class="col-12">
                          <label class="form-label" for="period-filter">Periode</label>
                          <select class="form-select" id="period-filter" name="periode" aria-label="Select period">
                            <option value="" selected>Pilih Periode</option>
                            @foreach ($periods as $period)
                              @if (empty(request()->input('periode')) && $activePeriod?->id == $period->id)
                                <option value="{{ $period->id }}" selected>
                                  {{ $period->academic_year }}
                                  {{ Helper::getSemesterLabel($period->semester) }}
                                </option>
                              @elseif($period->id == request()->input('periode'))
                                <option value="{{ $period->id }}" selected>
                                  {{ $period->academic_year }}
                                  {{ Helper::getSemesterLabel($period->semester) }}
                                </option>
                              @else
                                <option value="{{ $period->id }}">
                                  {{ $period->academic_year }}
                                  {{ Helper::getSemesterLabel($period->semester) }}
                                </option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        @role(['operator'])
                          @if ($majors->count() > 0)
                            <div class="col-12">
                              <label class="form-label" for="major-filter">Jurusan</label>
                              <select class="form-select" id="major-filter" name="jurusan" aria-label="Select major">
                                <option value="" selected>Pilih Jurusan</option>
                                @foreach ($majors as $major)
                                  @if ($major->name == request()->input('jurusan'))
                                    <option value="{{ $major->name }}" selected>
                                      {{ $major->name }}
                                    </option>
                                  @else
                                    <option value="{{ $major->name }}">
                                      {{ $major->name }}
                                    </option>
                                  @endif
                                @endforeach
                              </select>
                            </div>
                          @endif
                        @endrole
                        <div class="col-12">
                          <label class="form-label" for="type-filter">Jenis</label>
                          <select class="form-select" id="type-filter" name="tipe" aria-label="Select type">
                            <option value="" selected>Pilih tipe</option>
                            @foreach ($ukkTypes as $type)
                              @if ($type['value'] == request()->input('tipe'))
                                <option value="{{ $type['value'] }}" selected>
                                  {{ $type['label'] }}
                                </option>
                              @else
                                <option value="{{ $type['value'] }}">{{ $type['label'] }}
                                </option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12">
                          <label class="form-label">Rentang Waktu Dari</label>
                          <input class="form-control flatpickr-input start-date" id="start-date-filter" type="date"
                            autocomplete="off" name="rentang-waktu-dari" placeholder="YYYY-MM-DD H:i">
                        </div>
                        <div class="col-12"><label class="form-label">Rentang Waktu Sampai</label>
                          <input class="form-control flatpickr-input end-date" id="end-date-filter"
                            placeholder="YYYY-MM-DD H:i" type="date" name="rentang-waktu-sampai"
                            autocomplete="off">
                        </div>
                        <div class="col-12 d-flex justify-content-end align-items-center gap-2">
                          <a class="btn btn-outline-primary f-w-500"
                            href="?{{ http_build_query(['value' => request()->query('value')]) }}"
                            id="filter-reset-btn">Reset</a>

                          <button class="btn btn-primary f-w-500" id="filter-btn">Terapkan</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="ukk-list d-flex flex-column gap-2">
                @if (count($ukks) > 0)
                  @foreach ($ukks as $ukk)
                    <div class="task-item border rounded-1 w-100 p-3">
                      <div class="row g-2">
                        <div class="col-12 col-md">
                          <p class="fw-medium mb-1">{{ $ukk->title }}</p>
                          <p class="mb-1">{{ $ukk->major }}
                            &middot; {{ $ukk->type }}</p>
                          <div style="font-size: .8rem;" class="text-secondary">
                            <div class="row g-0 g-sm-2">
                              <div class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                <div class="d-flex align-items-center">
                                  <span class="icon"><i data-feather="calendar"
                                      style="width:18px; height: 18px"></i></span>
                                  <span class="mb-0 ms-2">{{ $ukk->start_time->translatedFormat('d M Y') }}</span>
                                </div>
                                <div>&middot;</div>
                                <span>
                                  {{ $ukk->start_time->translatedFormat('H:i') }} WIT
                                </span>
                              </div>
                              <div class="col-12 col-md-auto d-flex justify-content-center align-items-center">
                                <span class="icon d-flex align-items-center justify-content-center">
                                  <i data-feather="minus" style="width:18px; height: 18px"></i>
                                </span>
                              </div>
                              <div class="col-12 col-md-auto d-flex align-items-center gap-2 justify-content-center">
                                <div class="d-flex align-items-center">
                                  <span class="icon"><i data-feather="calendar"
                                      style="width:18px; height: 18px"></i></span>
                                  <span class="mb-0 ms-2">{{ $ukk->end_time->translatedFormat('d M Y') }}</span>
                                </div>
                                <div>&middot;</div>
                                <span>
                                  {{ $ukk->end_time->translatedFormat('H:i') }} WIT
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 justify-content-end flex-wrap col-md-auto d-flex align-items-center gap-2">
                          @can('ukk.evaluation')
                            @if ($ukk?->not_yet_rated)
                              <span class="badge m-0 badge-light-danger px-2 py-1 d-flex align-items-center">Belum
                                dinilai <span class="badge ms-1 badge-danger">{{ $ukk->not_yet_rated }}</span></span>
                            @endif
                          @endcan
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
                            @endif
                          @endrole
                          @can(['ukk.edit', 'ukk.delete'])
                            <button
                              class="btn d-flex align-items-center bg-20-warning border justify-content-center text-warning p-2"
                              style="width: 38px; height: 38px;" onclick="handleEditUkk(event, {{ $ukk->id }})">
                              <i data-feather="edit-2" style="width: 20px; height: 20px"></i>
                            </button>
                            <button
                              class="btn d-flex align-items-center bg-20-danger border justify-content-center text-danger p-2"
                              style="width: 38px; height: 38px;" onclick="handleDeleteUkk(event, {{ $ukk->id }})">
                              <i data-feather="trash-2" style="width: 20px; height: 20px"></i>
                            </button>
                          @endrole
                          <a @role(['student', 'parent'])
                                @if ($ukk->type === 'Teori')
                                    href="{{ route('user.ukk.teori.info', $ukk->id) }}"
                                @else
                                    href="{{ route('user.ukk.praktik.info', $ukk->id) }}"
                                @endif
                             @endrole
                            @role(['operator'])
                                @if ($ukk->type === 'Teori')
                                    href="{{ route('user.ukk.result.teori', $ukk->id) }}"
                                @else
                                    href="{{ route('user.ukk.result.praktik', $ukk->id) }}"
                                @endif
                             @endrole
                            class="btn btn-outline-primary gap-1 px-3 btn-sm d-flex justify-content-center align-items-center">
                            <span>Lihat</span>
                          </a>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  <div class="pagination-wrapper w-100">
                    {{ $ukks->withQueryString()->links('pagination::bootstrap-5') }}
                  </div>
                @else
                  {{-- empty data --}}
                  <div class="d-flex justify-content-center mb-3 w-100">
                    <div class="px-4 py-5 d-grid" style="justify-items: center">
                      <img style="width: 120px; height: 120px" src="{{ asset('assets/images/data-empty.png') }}" />
                      <p class="fw-semibold mb-0 text-center">Ups! Data Kosong</p>
                      <p class="mb-0 text-center">Belum ada data yang tersedia.</p>
                    </div>
                  </div>
                @endif
              </div>

              @include('user.ukk.modal')
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script>
    const default_end_date = "{{ request()->query('rentang-waktu-sampai') }}";
    const default_start_date = "{{ request()->query('rentang-waktu-dari') }}";
    const filter_end_date = flatpickr("#end-date-filter", {
      defaultDate: new Date(default_end_date),
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      locale: flatpickrLocationID,
    });
    const filter_start_date = flatpickr("#start-date-filter", {
      defaultDate: new Date(default_start_date),
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates.length > 0) {
          const selectedDate = selectedDates[0];
          filter_end_date.set("minDate", selectedDate);
        }
      },
      locale: flatpickrLocationID,
    });
  </script>
  <script src="{{ asset('assets/js/ukk.js') }}"></script>
@endsection
