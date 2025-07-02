@extends('layouts.user.app')

@section('title', 'Mata Pelajaran')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <style>
    #subject-table td:nth-child(2) p,
    #subject-table td:nth-child(2),
    #subject-table th:nth-child(2) p,
    #subject-table th:nth-child(2) {
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
          <h3>Mata Pelajaran ({{ $curriculum->name }})</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.curriculum.index') }}">Kurikulum</a></li>
            <li class="breadcrumb-item active">Mata Pelajaran</li>
          </ol>
        </div>
      </div>
    </div>
  </div><!-- Container-fluid starts-->
  <div class="container-fluid e-category">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header card-no-border text-end">
            <div class="card-header-right-icon">
              <button class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal" data-bs-target="#addSubjectModal"><i
                  class="fa fa-plus pe-2"></i>Tambah
              </button>
              <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate id="addSubjectForm">
                            <div class="col">
                              <label class="form-label" for="subjectName">Mata Pelajaran<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="subjectName" type="text"
                                placeholder="Nama Mata Pelajaran" name="name">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="addSubjectSubmitBtn">Tambah +</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Mata Pelajaran</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate id="editSubjectForm">
                            <div class="col">
                              <label class="form-label" for="editSubjectName">Mata Pelajaran<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="editSubjectName" type="text"
                                placeholder="Nama Mata Pelajaran" name="name">
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="editSubjectSubmitBtn">Simpan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-3 justify-content-end align-items-center" id="subject-action-buttons">
                <div class="col-auto">
                  <span>
                    <span class="me-1 text-dark" id="selected-count">0</span> dipilih
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
          <div class="card-body pt-0">
            <div class="list-product list-category mt-3">
              <div class="recent-table table-responsive custom-scrollbar">
                <table class="table" id="subject-table">
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
                      <th><span class="c-o-light f-w-600">Kode</span></th>
                      <th><span class="c-o-light f-w-600">Nama</span></th>
                      <th><span class="c-o-light f-w-600">Waktu</span></th>
                      <th><span class="c-o-light f-w-600">Aksi</span></th>
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
  <script>
    const curriculumId = @json($curriculum->id);
  </script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/subject-crud.js') }}"></script>
@endsection
