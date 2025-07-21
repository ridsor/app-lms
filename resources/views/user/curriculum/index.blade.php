@extends('layouts.user.app')

@section('title', 'Kurikulum')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <style>
    #curriculum-table td:nth-child(2) p,
    #curriculum-table td:nth-child(2),
    #curriculum-table th:nth-child(2) p,
    #curriculum-table th:nth-child(2) {
      white-space: nowrap !important;
      overflow: visible !important;
      text-overflow: unset !important;
      max-width: none !important;
    }
    #curriculum-table td:nth-child(4) p,
    #curriculum-table td:nth-child(4),
    #curriculum-table th:nth-child(4) p,
    #curriculum-table th:nth-child(4) {
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
          <h3>Kurikulum</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Kurikulum</li>
          </ol>
        </div>
      </div>
    </div>
  </div><!-- Container-fluid starts-->
  <div class="container-fluid e-category">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header card-no-border text-end">
            <div class="card-header-right-icon">
              <a class="btn btn-primary f-w-500 mb-2" href="#" data-bs-toggle="modal"
                data-bs-target="#addCurriculumModal"><i class="fa fa-plus pe-2"></i>Tambah
              </a>
              <div class="modal fade" id="addCurriculumModal" tabindex="-1" aria-labelledby="addCurriculumModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Tambah Kurikulum</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate id="addCurriculumForm">
                            @csrf
                            <input type="hidden" name="_method" value="POST">
                            <div class="col-12">
                              <label class="form-label" for="name">Nama Kurikulum<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="addName" type="text" placeholder="Nama Kurikulum"
                                name="name" required>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                              <label class="form-label" for="description">Deskripsi</label>
                              <div class="toolbar-box">
                                <div id="toolbar9">
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
                                <div id="addDescriptionEditor"></div>
                                <input type="hidden" id="addDescription" name="description" class="quill">
                              </div>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit" id="addCurriculumSubmitBtn">Tambah +</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="editCurriculumModal" tabindex="-1" aria-labelledby="editCurriculumModal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content category-popup">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modaldashboard">Edit Kurikulum</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="text-start">
                        <div class="p-20">
                          <form class="row g-3 needs-validation" novalidate id="editCurriculumForm">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="editCurriculumId" name="curriculum_id">
                            <div class="col-12">
                              <label class="form-label" for="name">Nama Kurikulum<span
                                  class="txt-danger">*</span></label>
                              <input class="form-control" id="editName" type="text" placeholder="Nama Kurikulum"
                                name="name" required>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                              <label class="form-label" for="description">Deskripsi</label>
                              <div class="toolbar-box">
                                <div id="toolbar10">
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
                                <div id="editDescriptionEditor"></div>
                                <input type="hidden" id="editDescription" name="description" class="quill">
                              </div>
                              <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                              <button class="btn btn-primary" type="submit"
                                id="editCurriculumSubmitBtn">Simpan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row g-3 justify-content-end">
                <div class="col-auto" style="display: none;">
                  <a id="delete-selected"
                    class="d-block rounded-2 d-flex justify-content-center align-items-center light-square bg-light-danger px-3 py-2"
                    style="cursor: pointer;">
                    <span class="me-1 text-dark" id="delete-selected-count">0</span>
                    <i class="fa-solid fa-trash-can txt-danger"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0">
            <div class="list-product list-category">
              <div class="recent-table table-responsive custom-scrollbar">
                <table class="table" id="curriculum-table">
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
                      <th> <span class="c-o-light f-w-600">Deskripsi</span></th>
                      <th> <span class="c-o-light f-w-600">Jumlah Mata Pelajaran</span></th>
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
  <script src="{{ asset('assets/js/datatable/datatables/select.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
  <script src="{{ asset('assets/js/curriculum-crud.js') }}"></script>
@endsection
