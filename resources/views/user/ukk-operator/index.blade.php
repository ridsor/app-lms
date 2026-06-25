@extends('layouts.user.app')

@section('title', 'Operator UKK')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Operator UKK</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Operator UKK</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="e-category">
    <div class="row">
      <div class="col-12">
        <div class="card rounded-responsive">
          <div class="card-header card-no-border">
            <div class="header-top">
              <h5>Akun Operator UKK</h5>
            </div>
          </div>
          <div class="card-body pt-0">
            <form method="GET" action="{{ route('user.ukk-operator.export') }}">
              <div class="row g-3">
                <div class="col-auto d-flex justify-content-start align-items-end">
                  <button type="submit" class="btn btn-success f-w-500 w-100">
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
              <a class="btn btn-primary f-w-500 mb-2" data-bs-toggle="modal" data-bs-target="#addUkkOperatorModal">
                <i class="fa fa-plus pe-2"></i>Tambah
              </a>

              <div class="modal fade" id="addUkkOperatorModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Tambah Operator UKK</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="p-20 text-start">
                        <form id="addUkkOperatorForm" class="row g-3">
                          <div class="col-12">
                            <label class="form-label">Nama Lengkap<span class="txt-danger">*</span></label>
                            <input class="form-control" type="text" name="name" placeholder="Masukan nama lengkap">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted mt-1">Username & Password akan dibuat secara otomatis berdasarkan
                              nama.</small>
                          </div>
                          <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit" id="addUkkOperatorSubmitBtn">Tambah</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal fade" id="editUkkOperatorModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Operator UKK</h5>
                      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 custom-input">
                      <div class="p-20 text-start">
                        <form id="editUkkOperatorForm" class="row g-3">
                          <div class="col-12">
                            <label class="form-label">Nama Lengkap<span class="txt-danger">*</span></label>
                            <input class="form-control" type="text" name="name" placeholder="Masukan nama lengkap">
                            <div class="invalid-feedback"></div>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Username<span class="txt-danger">*</span></label>
                            <input class="form-control" type="text" name="username" placeholder="Masukan username">
                            <div class="invalid-feedback"></div>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <input class="form-control" type="password" name="password"
                              placeholder="Masukan password baru">
                            <div class="invalid-feedback"></div>
                          </div>
                          <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit"
                              id="editUkkOperatorSubmitBtn">Simpan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row g-3 justify-content-end align-items-center" id="ukk-operator-action-buttons"
                style="display: none;">
                <div class="col-auto">
                  <span><span class="me-1" id="selected-count">0</span> dipilih</span>
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
                <table class="table" id="ukk-operator-table">
                  <thead>
                    <tr>
                      <th>
                        <div class="checkbox-checked">
                          <div class="form-check d-flex justify-content-center align-items-center">
                            <input class="form-check-input" id="select-all" type="checkbox"
                              style="width: 12px; height: 12px;">
                          </div>
                        </div>
                      </th>
                      <th> <span class="c-o-light f-w-600">Nama</span></th>
                      <th> <span class="c-o-light f-w-600">Username</span></th>
                      <th> <span class="c-o-light f-w-600">Waktu Dibuat</span></th>
                      <th> <span class="c-o-light f-w-600">Aksi</span></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
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
  <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
  <script src="{{ asset('assets/js/ukk-operator-crud.js') }}"></script>
@endsection
