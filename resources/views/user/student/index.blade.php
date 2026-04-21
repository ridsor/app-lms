@extends('layouts.user.app')

@section('title', 'Siswa')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select/bootstrap-select.min.css') }}">
  <style>
    #student-table td:nth-child(2) p,
    #student-table td:nth-child(2),
    #student-table th:nth-child(2) p,
    #student-table th:nth-child(2) {
      white-space: nowrap !important;
      overflow: visible !important;
      text-overflow: unset !important;
      max-width: none !important;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Siswa</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Siswa</li>
          </ol>
        </div>
      </div>
    </div>
  </div><!-- Container-fluid starts-->
  <div class="e-category">
    <div class="row">
      <div class="col-12">
        <div class="card rounded-responsive">
          <div class="card-body common-offcanvas">
            <div class="row g-2 align-items-center justify-content-end">
              <div class="col-auto">
                <button type="button" data-bs-toggle="offcanvas" data-bs-target="#studentAccount"
                  aria-controls="studentAccount"
                  class="btn btn-outline-success gap-1 btn-sm d-flex justify-content-center align-items-center">
                  <i data-feather="user" style="width:18px; height:18px"></i>
                  <span>Akun Siswa</span>
                </button>
                <div class="offcanvas offcanvas-end" id="studentAccount" tabindex="-1"
                  aria-labelledby="studentAccountLabel">
                  <div class="offcanvas-header pb-0">
                    <h5 class="offcanvas-title" id="studentAccountLabel">Akun Siswa</h5><button class="btn-close"
                      type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body custom-input custom-scrollbar">
                    <form id="export-student-account-form" method="GET"
                      action="{{ route('user.student.account.export') }}">
                      <div class="row g-3">
                        @if ($majors->count() > 0)
                          <div class="col-12">
                            <label class="form-label" for="export-student-account-major-filter">Jurusan</label>
                            <select class="form-select" @cannot('student.*') disabled @endcannot
                              id="export-student-account-major-filter" name="major" aria-label="Select major">
                              <option value="" selected>Pilih Jurusan</option>
                              @foreach ($majors as $major)
                                <option value="{{ $major->name }}">{{ $major->name }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        @endif
                        <div class="col-12">
                          <label class="form-label" for="export-student-account-class-filter">Kelas</label>
                          <select class="form-select" @cannot('student.*') disabled @endcannot
                            id="export-student-account-class-filter" name="class" aria-label="Select class">
                            <option value="" selected>Pilih Kelas</option>
                            @foreach ($classNames as $class)
                              <option value="{{ $class->name }}">{{ $class->name }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12">
                          <label class="form-label" for="export-student-account-level-filter">Tingkat</label>
                          <select class="form-select" id="export-student-account-level-filter" name="level"
                            aria-label="Select level">
                            <option value="" selected>Pilih Tingkat</option>
                            @foreach ($classLevels as $classLevel)
                              <option value="{{ $classLevel->level }}">
                                {{ $classLevel->level }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end align-items-center">
                          <button type="submit" class="btn btn-success f-w-500 w-100" id="export-student-account-btn">
                            <i class="fa-solid fa-file-excel"></i> Export
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <button type="button" data-bs-toggle="offcanvas" data-bs-target="#filter" aria-controls="filter"
                  class="btn btn-outline-success gap-1 btn-sm d-flex justify-content-center align-items-center">
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
                      @if ($majors->count() > 0)
                        <div class="col-12">
                          <label class="form-label" for="major-filter">Jurusan</label>
                          <select class="form-select" id="major-filter" aria-label="Select major"
                            @cannot('student.*') disabled @endcannot>
                            <option value="" selected>Pilih Jurusan</option>
                            @foreach ($majors as $major)
                              <option value="{{ $major->name }}">
                                {{ $major->name }}</option>
                            @endforeach
                          </select>
                        </div>
                      @endif
                      <div class="col-12">
                        <label class="form-label" for="class-filter">Kelas</label>
                        <select class="form-select" @cannot('student.*') disabled @endcannot id="class-filter"
                          aria-label="Select class">
                          <option value="" selected>Pilih Kelas</option>
                          @foreach ($classNames as $class)
                            <option value="{{ $class->name }}">{{ $class->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-12">
                        <label class="form-label" for="level-filter">Tingkat</label>
                        <select class="form-select" id="level-filter" aria-label="Select level">
                          <option value="" selected>Pilih Tingkat</option>
                          @foreach ($classLevels as $classLevel)
                            <option value="{{ $classLevel->level }}">
                              {{ $classLevel->level }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-12">
                        <label class="form-label" for="teacher-filter">Wali Kelas</label>
                        <select class="selectpicker search-picker filter" data-live-search="true" id="teacher-filter"
                          @cannot('student.*') disabled @endcannot>
                          <option value="">Pilih Wali Kelas</option>
                          @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->name }}">{{ $teacher->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-12">
                        <label class="form-label" for="status-filter">Status</label>
                        <select class="form-select" id="status-filter" aria-label="Select status">
                          <option value="" selected>Pilih Status</option>
                          @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}">
                              {{ $status['label'] }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-12 d-flex justify-content-end align-items-center gap-2">
                        <a class="btn btn-outline-primary f-w-500" id="filter-reset-btn">Reset</a>

                        <button class="btn btn-primary f-w-500" id="filter-btn">Terapkan</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card rounded-responsive">
          <div class="card-header card-no-border text-end">
            <div class="card-header-right-icon">
              <button class="btn btn-primary f-w-500 mb-2" @cannot('student.*') disabled @endcannot
                data-bs-toggle="modal" data-bs-target="#addStudentModal"><i class="fa fa-plus pe-2"></i>Tambah
              </button>
              <div class="row g-3 justify-content-end align-items-center" id="student-action-buttons">
                <div class="col-auto">
                  <span>
                    <span class="me-1" id="selected-count">0</span> dipilih
                  </span>
                </div>
                @can('student.*')
                  <div class="col-auto">
                    <a id="delete-selected"
                      class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-2 py-2"
                      style="cursor: pointer;">
                      <i class="fa-solid fa-trash-can txt-danger"></i>
                    </a>
                  </div>
                @endcan
                <div class="col-auto">
                  <a id="bulk-edit-selected"
                    class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-primary px-2 py-2"
                    style="cursor: pointer;">
                    <i class="fa-solid fa-pen-to-square txt-primary"></i>
                  </a>
                </div>
              </div>
              @include('user.student.modal')
            </div>
          </div>

          <div class="card-body px-0 pt-0">
            <div class="list-product list-category">
              <div class="recent-table table-responsive custom-scrollbar">
                <table class="table" id="student-table">
                  <thead>
                    <tr>
                      <th>
                        <div class="checkbox-checked">
                          <div class="form-check d-flex justify-content-center align-items-center">
                            <input class="form-check-input" id="select-all" type="checkbox"
                              style="width: 12px; height: 12px;" value>
                          </div>
                        </div>
                      </th>
                      <th> <span class="c-o-light f-w-600">Nama</span></th>
                      <th> <span class="c-o-light f-w-600">NIS</span></th>
                      <th> <span class="c-o-light f-w-600">NISN</span></th>
                      <th> <span class="c-o-light f-w-600">Jurusan</span></th>
                      <th> <span class="c-o-light f-w-600">Kelas</span></th>
                      <th> <span class="c-o-light f-w-600">Wali Kelas</span></th>
                      <th> <span class="c-o-light f-w-600">Status</span></th>
                      <th> <span class="c-o-light f-w-600">Waktu</span></th>
                      <th> <span class="c-o-light f-w-600">Aksi</span></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script>
    const majors = @json($majors);
    const classes = @json($classes);
  </script>
  <script src="{{ asset('assets/js/student-crud.js') }}"></script>
  <script src="{{ asset('assets/js/select/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
@endsection
