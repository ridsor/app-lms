@extends('layouts.user.app')

@section('title', 'Siswa')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
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
  <div class="container-fluid e-category">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5>Akun Siswa</h5>
            </div>
          </div>
          <div class="card-body pt-0">
            <form id="export-student-account-form" method="GET" action="{{ route('user.student.account.export') }}">
              <div class="row g-3">
                @can('student.*')
                  @if ($majors->count() > 0)
                    <div class="col-md">
                      <label class="form-label" for="export-student-account-major-filter">Jurusan</label>
                      <select class="form-select" id="export-student-account-major-filter" name="major"
                        aria-label="Select major">
                        <option value="" selected>Pilih Jurusan</option>
                        @foreach ($majors as $major)
                          <option value="{{ $major->name }}">{{ $major->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  @endif
                @endcan
                @can('student.view.homeroomteacher')
                  @if ($majors->count() > 0)
                    <div class="col-md">
                      <label class="form-label" for="export-student-account-major-filter">Jurusan</label>
                      <select class="form-select"disabled id="export-student-account-major-filter" name="major"
                        aria-label="Select major">
                        <option value="{{ $homeroomTeacherClass->major->name }}" selected>
                          {{ $homeroomTeacherClass->major->name }}</option>
                      </select>
                    </div>
                  @endif
                @endcan
                @can('student.*')
                  <div class="col-md">
                    <label class="form-label" for="export-student-account-class-filter">Kelas</label>
                    <select class="form-select" id="export-student-account-class-filter" name="class"
                      aria-label="Select class">
                      <option value="" selected>Pilih Kelas</option>
                      @foreach ($classNames as $class)
                        <option value="{{ $class->name }}">{{ $class->name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md">
                    <label class="form-label" for="export-student-account-class-filter">Kelas</label>
                    <select class="form-select" disabled id="export-student-account-class-filter" name="class"
                      aria-label="Select class">
                      <option value="{{ $homeroomTeacherClass->name }}" selected>{{ $homeroomTeacherClass->name }}
                      </option>
                    </select>
                  </div>
                @endcan
                @can('student.*')
                  <div class="col-md">
                    <label class="form-label" for="export-student-account-level-filter">Tingkat</label>
                    <select class="form-select" id="export-student-account-level-filter" name="level"
                      aria-label="Select level">
                      <option value="" selected>Pilih Tingkat</option>
                      @foreach ($classLevels as $classLevel)
                        <option value="{{ $classLevel->level }}">{{ $classLevel->level }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md">
                    <label class="form-label" for="export-student-account-level-filter">Tingkat</label>
                    <select class="form-select" disabled id="export-student-account-level-filter" name="level"
                      aria-label="Select level">
                      <option value="{{ $homeroomTeacherClass->level }}" selected>{{ $homeroomTeacherClass->level }}
                      </option>
                    </select>
                  </div>
                @endcan
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <button type="submit" class="btn btn-success f-w-500 w-100" id="export-student-account-btn">
                    <i class="fa-solid fa-file-excel"></i> Export
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5>Akun Orang Tua</h5>
            </div>
          </div>
          <div class="card-body pt-0">
            <form id="export-parent-account-form" method="GET"
              action="{{ route('user.student.parent.account.export') }}">
              <div class="row g-3">
                @can('student.*')
                  @if ($majors->count() > 0)
                    <div class="col-md">
                      <label class="form-label" for="export-parent-account-major-filter">Jurusan</label>
                      <select class="form-select" id="export-parent-account-major-filter" name="major"
                        aria-label="Select major">
                        <option value="" selected>Pilih Jurusan</option>
                        @foreach ($majors as $major)
                          <option value="{{ $major->name }}">{{ $major->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  @endif
                @endcan
                @can('student.view.homeroomteacher')
                  @if ($majors->count() > 0)
                    <div class="col-md">
                      <label class="form-label" for="export-parent-account-major-filter">Jurusan</label>
                      <select class="form-select" disabled id="export-parent-account-major-filter" name="major"
                        aria-label="Select major">
                        <option value="{{ $homeroomTeacherClass->major->name }}" selected>
                          {{ $homeroomTeacherClass->major->name }}</option>
                      </select>
                    </div>
                  @endif
                @endcan
                @can('student.*')
                  <div class="col-md">
                    <label class="form-label" for="export-parent-account-class-filter">Kelas</label>
                    <select class="form-select" id="export-parent-account-class-filter" name="class"
                      aria-label="Select class">
                      <option value="" selected>Pilih Kelas</option>
                      @foreach ($classNames as $class)
                        <option value="{{ $class->name }}">{{ $class->name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md">
                    <label class="form-label" for="export-parent-account-class-filter">Kelas</label>
                    <select class="form-select" disabled id="export-parent-account-class-filter" name="class"
                      aria-label="Select class">
                      <option value="{{ $homeroomTeacherClass->name }}" selected>{{ $homeroomTeacherClass->name }}
                      </option>
                    </select>
                  </div>
                @endcan
                @can('student.*')
                  <div class="col-md">
                    <label class="form-label" for="export-parent-account-level-filter">Tingkat</label>
                    <select class="form-select" id="export-parent-account-level-filter" name="level"
                      aria-label="Select level">
                      <option value="" selected>Pilih Tingkat</option>
                      @foreach ($classLevels as $classLevel)
                        <option value="{{ $classLevel->level }}">{{ $classLevel->level }}</option>
                      @endforeach
                    </select>
                  </div>
                @endcan
                @can('student.view.homeroomteacher')
                  <div class="col-md">
                    <label class="form-label" for="export-parent-account-level-filter">Tingkat</label>
                    <select class="form-select" disabled id="export-parent-account-level-filter" name="level"
                      aria-label="Select level">
                      <option value="{{ $homeroomTeacherClass->level }}" selected>{{ $homeroomTeacherClass->level }}
                      </option>
                    </select>
                  </div>
                @endcan
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <button type="submit" class="btn btn-success f-w-500 w-100" id="export-parent-account-btn">
                    <i class="fa-solid fa-file-excel"></i> Export
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5>Filter</h5>
            </div>
          </div>
          <div class="card-body pt-0">
            <div class="row g-3">
              @can('student.*')
                @if ($majors->count() > 0)
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="major-filter">Jurusan</label>
                    <select class="form-select" id="major-filter" aria-label="Select major">
                      <option value="" selected>Pilih Jurusan</option>
                      @foreach ($majors as $major)
                        <option value="{{ $major->name }}">{{ $major->name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
              @endcan
              @can('student.view.homeroomteacher')
                @if ($majors->count() > 0)
                  <div class="col-md-3 col-xl">
                    <label class="form-label" for="major-filter">Jurusan</label>
                    <select class="form-select" disabled id="major-filter" aria-label="Select major">
                      <option value="{{ $homeroomTeacherClass->major->name }}" selected>
                        {{ $homeroomTeacherClass->major->name }}</option>
                    </select>
                  </div>
                @endif
              @endcan
              @can('student.*')
                <div class="col-md-3 col-xl">
                  <label class="form-label" for="class-filter">Kelas</label>
                  <select class="form-select" id="class-filter" aria-label="Select class">
                    <option value="" selected>Pilih Kelas</option>
                    @foreach ($classNames as $class)
                      <option value="{{ $class->name }}">{{ $class->name }}</option>
                    @endforeach
                  </select>
                </div>
              @endcan
              @can('student.view.homeroomteacher')
                <div class="col-md-3 col-xl">
                  <label class="form-label" for="class-filter">Kelas</label>
                  <select class="form-select" disabled id="class-filter" aria-label="Select class">
                    <option value="{{ $homeroomTeacherClass->name }}" selected>{{ $homeroomTeacherClass->name }}</option>
                  </select>
                </div>
              @endcan
              @can('student.*')
                <div class="col-md-3 col-xl">
                  <label class="form-label" for="level-filter">Tingkat</label>
                  <select class="form-select" id="level-filter" aria-label="Select level">
                    <option value="" selected>Pilih Tingkat</option>
                    @foreach ($classLevels as $classLevel)
                      <option value="{{ $classLevel->level }}">{{ $classLevel->level }}</option>
                    @endforeach
                  </select>
                </div>
              @endcan
              @can('student.view.homeroomteacher')
                <div class="col-md-3 col-xl">
                  <label class="form-label" for="level-filter">Tingkat</label>
                  <select class="form-select" disabled id="level-filter" aria-label="Select level">
                    <option value="{{ $homeroomTeacherClass->level }}" selected>{{ $homeroomTeacherClass->level }}
                    </option>
                  </select>
                </div>
              @endcan
              <div class="col-md-3 col-xl">
                <label class="form-label" for="status-filter">Status</label>
                <select class="form-select" id="status-filter" aria-label="Select status">
                  <option value="" selected>Pilih Status</option>
                  @foreach ($statuses as $status)
                    <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-auto d-flex justify-content-start align-items-end">
                <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="card">
          <div class="card-header card-no-border text-end">
            <div class="card-header-right-icon">
              <button class="btn btn-primary f-w-500 mb-2" @cannot('student.*') disabled @endcannot
                data-bs-toggle="modal" data-bs-target="#addStudentModal"><i class="fa fa-plus pe-2"></i>Tambah
              </button>
              <div class="row g-3 justify-content-end align-items-center" id="student-action-buttons">
                <div class="col-auto">
                  <span>
                    <span class="me-1 text-dark" id="selected-count">0</span> dipilih
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
              <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Tambah Siswa</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate="" id="addStudentForm">
                            <div class="col-lg-6">
                              <label class="form-label" for="studentName">Nama<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="studentName" type="text"
                                placeholder="Masukan nama siswa" name="name">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentNis">NIS<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="studentNis" type="text" placeholder="Masukan NIS"
                                name="nis">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentNisn">NISN<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="studentNisn" type="text" placeholder="Masukan NISN"
                                name="nisn">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            @if ($majors->count() > 0)
                              <div class="col-lg-6">
                                <label class="form-label" for="studentMajor">Jurusan</label>
                                <select class="form-select" id="studentMajor" name="major_id">
                                  <option value="">Pilih Jurusan</option>
                                  @foreach ($majors as $major)
                                    <option value="{{ $major->id }}">
                                      {{ $major->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            @endif
                            <div class="col-lg-6">
                              <label class="form-label" for="studentClass">Kelas</label>
                              <select class="form-select" id="studentClass" name="class_id">
                                <option value="">Pilih Kelas</option>
                                @foreach ($classes as $class)
                                  <option value="{{ $class->id }}">
                                    {{ $class->name }} - {{ $class->level }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentBirthplace">Tempat
                                Lahir<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="studentBirthplace" type="text"
                                placeholder="Masukan tempat lahir" name="birthplace">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentDateOfBirth">Tanggal
                                Lahir<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control datepicker-here" autocomplete="off" id="studentDateOfBirth"
                                type="text" name="date_of_birth" placeholder="dd/mm/yyyy" data-language="id">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentGender">Jenis
                                Kelamin<span class="txt-danger">*</span>
                              </label>
                              <select class="form-select" id="studentGender" name="gender">
                                <option value="">Pilih Jenis Kelamin</option>
                                @foreach ($genders as $gender)
                                  <option value="{{ $gender['value'] }}">
                                    {{ $gender['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentReligion">Agama<span class="txt-danger">*</span>
                              </label>
                              <select class="form-select" id="studentReligion" name="religion">
                                <option value="">Pilih Agama</option>
                                @foreach ($religions as $religion)
                                  <option value="{{ $religion }}">
                                    {{ $religion }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentAdmissionYear">Tahun
                                Masuk<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="studentAdmissionYear" type="number"
                                placeholder="Masukan tahun masuk" name="admission_year" min="2000"
                                max="{{ date('Y') + 1 }}">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="studentStatus">Status
                              </label>
                              <select class="form-select" id="studentStatus" name="status">
                                @foreach ($statuses as $status)
                                  <option value="{{ $status['value'] }}">
                                    {{ $status['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="addStudentSubmitBtn">Tambah +</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Edit Siswa</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate="" id="editStudentForm">
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentName">Nama<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentName" type="text"
                                placeholder="Masukan nama siswa" name="name">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentNis">NIS<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentNis" type="text" placeholder="Masukan NIS"
                                name="nis">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentNisn">NISN<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentNisn" type="text"
                                placeholder="Masukan NISN" name="nisn">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            @if ($majors->count() > 0)
                              <div class="col-lg-6">
                                <label class="form-label" for="editStudentMajor">Jurusan</label>
                                <select class="form-select" id="editStudentMajor" name="major_id"
                                  @cannot('student.*') disabled @endcannot>
                                  <option value="">Pilih Jurusan</option>
                                  @foreach ($majors as $major)
                                    <option value="{{ $major->id }}">
                                      {{ $major->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            @endif
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentClass">Kelas<span
                                  class="txt-danger">*</span></label>
                              <select class="form-select" id="editStudentClass" name="class_id">
                                <option value="">Pilih Kelas</option>
                                @foreach ($classes as $class)
                                  <option value="{{ $class->id }}">
                                    {{ $class->name }} - {{ $class->level }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentBirthplace">Tempat
                                Lahir<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentBirthplace" type="text"
                                placeholder="Masukan tempat lahir" name="birthplace">
                              <div class="invalid-feedback">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentDateOfBirth">Tanggal
                                Lahir<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentDateOfBirth" type="text"
                                name="date_of_birth">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentGender">Jenis
                                Kelamin<span class="txt-danger">*</span>
                              </label>
                              <select class="form-select" id="editStudentGender" name="gender">
                                @foreach ($genders as $gender)
                                  <option value="{{ $gender['value'] }}">
                                    {{ $gender['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentReligion">Agama<span
                                  class="txt-danger">*</span>
                              </label>
                              <select class="form-select" id="editStudentReligion" name="religion">
                                @foreach ($religions as $religion)
                                  <option value="{{ $religion }}">
                                    {{ $religion }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentAdmissionYear">Tahun
                                Masuk<span class="txt-danger">*</span>
                              </label>
                              <input class="form-control" id="editStudentAdmissionYear" type="number"
                                placeholder="Masukan tahun masuk" name="admission_year" min="2000"
                                max="{{ date('Y') + 1 }}">
                              <div class="invalid-feedback">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editStudentStatus">Status
                              </label>
                              <select class="form-select" id="editStudentStatus" name="status">
                                @foreach ($statuses as $status)
                                  <option value="{{ $status['value'] }}">
                                    {{ $status['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback">
                              </div>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="editStudentSubmitBtn">Simpan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal View Student -->
              <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="viewStudentModalLabel">Detail Siswa</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <div class="row g-3">
                            <div class="col-lg-6">
                              <label class="form-label">Nama</label>
                              <div class="form-control-plaintext" id="viewStudentName">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">NIS</label>
                              <div class="form-control-plaintext" id="viewStudentNis"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">NISN</label>
                              <div class="form-control-plaintext" id="viewStudentNisn">
                              </div>
                            </div>
                            @if ($majors->count() > 0)
                              <div class="col-lg-6">
                                <label class="form-label">Jurusan</label>
                                <div class="form-control-plaintext" id="viewStudentMajor">
                                </div>
                              </div>
                            @endif
                            <div class="col-lg-6">
                              <label class="form-label">Kelas</label>
                              <div class="form-control-plaintext" id="viewStudentClass">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Tempat Lahir</label>
                              <div class="form-control-plaintext" id="viewStudentBirthplace"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Tanggal Lahir</label>
                              <div class="form-control-plaintext" id="viewStudentDateOfBirth"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Jenis Kelamin</label>
                              <div class="form-control-plaintext" id="viewStudentGender">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Agama</label>
                              <div class="form-control-plaintext" id="viewStudentReligion">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Tahun Masuk</label>
                              <div class="form-control-plaintext" id="viewStudentAdmissionYear"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Status</label>
                              <div class="form-control-plaintext" id="viewStudentStatus">
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label">Waktu Dibuat</label>
                              <div class="form-control-plaintext" id="viewStudentCreatedAt">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal Bulk Edit -->
              <div class="modal fade" id="bulkEditStudentModal" tabindex="-1"
                aria-labelledby="bulkEditStudentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="bulkEditStudentModalLabel">Edit Massal Siswa</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <div class="mb-3">
                            <strong>Catatan:</strong>
                            <ul>
                              <li><span class="text-danger">Biarkan data kosong jika tidak ingin mengubah data tersebut.
                                </span></li>
                              <li><span class="text-danger">Data yang diubah hanya akan berpengaruh pada siswa yang
                                  dipilih.</span></li>
                            </ul>
                          </div>
                          <form class="row g-3" id="bulkEditStudentForm">
                            <div class="col-12">
                              <label class="form-label" for="bulkEditStudentStatus">Status</label>
                              <select class="form-select" id="bulkEditStudentStatus" name="status">
                                <option value="">Pilih Status</option>
                                @foreach ($statuses as $status)
                                  <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                @endforeach
                              </select>
                            </div>

                            <div class="col-12">
                              <label class="form-label" for="bulkEditStudentMajor">Jurusan</label>
                              <select class="form-select" id="bulkEditStudentMajor" name="major_id"
                                @cannot('student.*') disabled @endcannot>
                                <option value="">Pilih Jurusan</option>
                                @foreach ($majors as $major)
                                  <option value="{{ $major->id }}" @if ($major->id == $homeroomTeacherClass?->major_id) selected @endif>
                                    {{ $major->name }}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-12">
                              <label class="form-label" for="bulkEditStudentClass">Kelas</label>
                              <select class="form-select" id="bulkEditStudentClass" name="class_id">
                                <option value="">Pilih Kelas</option>
                                @foreach ($classes as $class)
                                  <option value="{{ $class->id }}" data-major="{{ $class->major_id }}">
                                    {{ $class->name }} - {{ $class->level }}</option>
                                @endforeach
                              </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit"
                                id="bulkEditStudentSubmitBtn">Simpan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
    const classes = @json($classes);
  </script>
  <script src="{{ asset('assets/js/student-crud.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
@endsection
