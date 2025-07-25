@extends('layouts.user.app')

@section('title', 'Guru')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
  <style>
    #teacher-table td:nth-child(2) p,
    #teacher-table td:nth-child(2),
    #teacher-table th:nth-child(2) p,
    #teacher-table th:nth-child(2) {
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
          <h3>Guru</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Guru</li>
          </ol>
        </div>
      </div>
    </div>
  </div><!-- Container-fluid starts-->
  <div class="e-category">
    <div class="row">
      <div class="col-12">
        <div class="card rounded-responsive">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5>Akun Guru</h5>
            </div>
          </div>
          <div class="card-body pt-0">
            <form id="export-teacher-account-form" method="GET" action="{{ route('user.teacher.account.export') }}">
              <div class="row g-3">
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <button type="submit" class="btn btn-success f-w-500 w-100" id="export-teacher-account-btn">
                    <i class="fa-solid fa-file-excel"></i> Export
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card rounded-responsive">
          <div class="card-header card-no-border text-end">
            <div class="card-header-right-icon">
              <a class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal" data-bs-target="#addTeacherModal"><i
                  class="fa fa-plus pe-2"></i>Tambah
              </a>
              <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Tambah Guru</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate="" id="addTeacherForm">
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherName">Nama<span class="txt-danger">*</span></label>
                              <input class="form-control" id="teacherName" type="text" placeholder="Masukan nama guru"
                                name="name">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherNip">NIP<span class="txt-danger">*</span></label>
                              <input class="form-control" id="teacherNip" type="text" placeholder="Masukan NIP"
                                name="nip">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherSpecialization">Spesialisasi</label>
                              <input class="form-control" id="teacherSpecialization" type="text"
                                placeholder="Masukan spesialisasi" name="specialization">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherBirthplace">Tempat Lahir<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="teacherBirthplace" type="text"
                                placeholder="Masukan tempat lahir" name="birthplace">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherDateOfBirth">Tanggal Lahir<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control datepicker-here" autocomplete="off" id="teacherDateOfBirth"
                                type="text" name="date_of_birth" placeholder="dd/mm/yyyy" data-language="id">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherReligion">Agama<span
                                  class="txt-danger">*</span></label>
                              <select class="form-select" id="teacherReligion" name="religion">
                                <option value="">Pilih Agama</option>
                                @foreach ($religions as $religion)
                                  <option value="{{ $religion }}">{{ $religion }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="teacherGender">Jenis Kelamin<span
                                  class="txt-danger">*</span></label>
                              <select class="form-select" id="teacherGender" name="gender">
                                <option value="">Pilih Jenis Kelamin</option>
                                @foreach ($genders as $gender)
                                  <option value="{{ $gender['value'] }}">{{ $gender['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="addTeacherSubmitBtn">Tambah
                                +</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Edit Guru</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate="" id="editTeacherForm">
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherName">Nama<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="editTeacherName" type="text"
                                placeholder="Masukan nama guru" name="name">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherNip">NIP<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="editTeacherNip" type="text" placeholder="Masukan NIP"
                                name="nip">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherSpecialization">Spesialisasi</label>
                              <input class="form-control" id="editTeacherSpecialization" type="text"
                                placeholder="Masukan spesialisasi" name="specialization">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherBirthplace">Tempat Lahir<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="editTeacherBirthplace" type="text"
                                placeholder="Masukan tempat lahir" name="birthplace">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherDateOfBirth">Tanggal Lahir<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control datepicker-here" autocomplete="off"
                                id="editTeacherDateOfBirth" type="text" placeholder="dd/mm/yyyy" data-language="id"
                                name="date_of_birth">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherReligion">Agama<span
                                  class="txt-danger">*</span></label>
                              <select class="form-select" id="editTeacherReligion" name="religion">
                                <option value="">Pilih Agama</option>
                                @foreach ($religions as $religion)
                                  <option value="{{ $religion }}">{{ $religion }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                              <label class="form-label" for="editTeacherGender">Jenis Kelamin<span
                                  class="txt-danger">*</span></label>
                              <select class="form-select" id="editTeacherGender" name="gender">
                                <option value="">Pilih Jenis Kelamin</option>
                                @foreach ($genders as $gender)
                                  <option value="{{ $gender['value'] }}">{{ $gender['label'] }}</option>
                                @endforeach
                              </select>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="editTeacherSubmitBtn">Simpan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-3 justify-content-end align-items-center" id="teacher-action-buttons">
                <div class="col-auto">
                  <span>
                    <span class="me-1" id="selected-count">0</span> dipilih
                  </span>
                </div>
                <div class="col-auto">
                  <a id="delete-selected"
                    class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-2 py-2"
                    style="cursor: pointer;">
                    <i class="fa-solid fa-trash-can txt-danger"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0">
            <div class="list-product list-category">
              <div class="recent-table table-responsive custom-scrollbar">
                <table class="table" id="teacher-table">
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
                      <th> <span class="c-o-light f-w-600">NIP</span></th>
                      <th> <span class="c-o-light f-w-600">Spesialisasi</span></th>
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

  <!-- Modal View Teacher -->
  <div class="modal fade" id="viewTeacherModal" tabindex="-1" aria-labelledby="viewTeacherModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content category-popup">
        <div class="modal-header">
          <h5 class="modal-title" id="viewTeacherModalLabel">Detail Guru</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0 custom-input">
          <div class="text-start">
            <div class="p-20">
              <div class="row g-3">
                <div class="col-lg-6">
                  <label class="form-label">Nama</label>
                  <div class="form-control-plaintext" id="viewTeacherName"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">NIP</label>
                  <div class="form-control-plaintext" id="viewTeacherNip"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Spesialisasi</label>
                  <div class="form-control-plaintext" id="viewTeacherSpecialization"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Tempat Lahir</label>
                  <div class="form-control-plaintext" id="viewTeacherBirthplace"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Tanggal Lahir</label>
                  <div class="form-control-plaintext" id="viewTeacherDateOfBirth"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Jenis Kelamin</label>
                  <div class="form-control-plaintext" id="viewTeacherGender"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Agama</label>
                  <div class="form-control-plaintext" id="viewTeacherReligion"></div>
                </div>
                <div class="col-lg-6">
                  <label class="form-label">Waktu Dibuat</label>
                  <div class="form-control-plaintext" id="viewTeacherCreatedAt"></div>
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
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.select.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script src="{{ asset('assets/js/teacher-crud.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
@endsection
