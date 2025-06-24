@extends('layouts.user.app')

@section('title', 'Manajemen Siswa')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    <style>
        /* Custom Select */
        .custom-select-search .selected-box:after {
            top: 6px !important;
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
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md">
                                <label class="form-label">Kelas</label>
                                <select class="form-select" id="class-filter" aria-label="Select class">
                                    <option value="" selected>Pilih Kelas</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }} - {{ $class->level }} -
                                            {{ $class->major }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md">
                                <label class="form-label">Wali Kelas</label>
                                <div class="select-box custom-select-search">
                                    <div class="options-container custom-scrollbar">
                                        @foreach ($teachers as $teacher)
                                            <div class="selection-option"><input class="radio" id="{{ $teacher->id }}"
                                                    type="radio" name="category"><label class="mb-0"
                                                    for="{{ $teacher->id }}">{{ $teacher->name }}</label></div>
                                        @endforeach
                                    </div>
                                    <div class="selected-box form-control" style="padding: 6px 36px 6px 12px;">Pilih Wali
                                        Kelas</div>
                                    <div class="search-box">
                                        <input type="text" placeholder="Cari...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="status-filter" aria-label="Select status">
                                    <option value="" selected>Pilih Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col d-flex justify-content-start align-items-end">
                                <a class="btn btn-primary f-w-500 w-100" id="filter-btn" href="#!">Submit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500 mb-2" href="#!" data-bs-toggle="modal"
                                data-bs-target="#addStudentModal"><i class="fa fa-plus pe-2"></i>Tambah
                            </a>
                            <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModal"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Siswa</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate=""
                                                        id="addStudentForm">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentName">Nama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentName" type="text"
                                                                placeholder="Masukan nama siswa" name="name">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentNis">NIS<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentNis" type="text"
                                                                placeholder="Masukan NIS" name="nis">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentNisn">NISN<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentNisn" type="text"
                                                                placeholder="Masukan NISN" name="nisn">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentClass">Kelas</label>
                                                            <select class="form-select" id="studentClass"
                                                                name="class_id">
                                                                <option value="">Pilih Kelas</option>
                                                                @foreach ($classes as $class)
                                                                    <option value="{{ $class->id }}">
                                                                        {{ $class->name }} - {{ $class->level }} -
                                                                        {{ $class->major }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentHomeroomTeacher">Wali
                                                                Kelas</label>
                                                            <select class="form-select" id="studentHomeroomTeacher"
                                                                name="homeroom_teacher_id">
                                                                <option value="">Pilih Wali Kelas</option>
                                                                @foreach ($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}">
                                                                        {{ $teacher->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentDateOfBirth">Tanggal
                                                                Lahir<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentDateOfBirth"
                                                                type="date" name="date_of_birth">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentBirthplace">Tempat
                                                                Lahir<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentBirthplace"
                                                                type="text" placeholder="Masukan tempat lahir"
                                                                name="birthplace">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentGender">Jenis
                                                                Kelamin<span class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentGender"
                                                                name="gender">
                                                                <option value="">Pilih Jenis Kelamin</option>
                                                                <option value="M">Laki-laki</option>
                                                                <option value="F">Perempuan</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentReligion">Agama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentReligion"
                                                                name="religion">
                                                                <option value="">Pilih Agama</option>
                                                                @foreach ($religions as $religion)
                                                                    <option value="{{ $religion }}">
                                                                        {{ $religion }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentAdmissionYear">Tahun
                                                                Masuk<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentAdmissionYear"
                                                                type="number" placeholder="Masukan tahun masuk"
                                                                name="admission_year" min="2000"
                                                                max="{{ date('Y') + 1 }}">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentStatus">Status<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentStatus"
                                                                name="status">
                                                                <option value="active" selected>Aktif</option>
                                                                <option value="transferred">Pindah</option>
                                                                <option value="graduated">Lulus</option>
                                                                <option value="dropout">Keluar</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="addStudentSubmitBtn">Tambah +</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="editStudentModal" tabindex="-1"
                                aria-labelledby="editStudentModal" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Edit Siswa</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate=""
                                                        id="editStudentForm">
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentName">Nama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentName" type="text"
                                                                placeholder="Masukan nama siswa" name="name">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentNis">NIS<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentNis" type="text"
                                                                placeholder="Masukan NIS" name="nis">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentNisn">NISN<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentNisn" type="text"
                                                                placeholder="Masukan NISN" name="nisn">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentClass">Kelas</label>
                                                            <select class="form-select" id="studentClass"
                                                                name="class_id">
                                                                @foreach ($classes as $class)
                                                                    <option value="{{ $class->id }}">
                                                                        {{ $class->name }} - {{ $class->level }} -
                                                                        {{ $class->major }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentHomeroomTeacher">Wali
                                                                Kelas</label>
                                                            <select class="form-select" id="studentHomeroomTeacher"
                                                                name="homeroom_teacher_id">
                                                                <option value="">Pilih Wali Kelas</option>
                                                                @foreach ($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}">
                                                                        {{ $teacher->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentDateOfBirth">Tanggal
                                                                Lahir<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentDateOfBirth"
                                                                type="date" name="date_of_birth">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentBirthplace">Tempat
                                                                Lahir<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentBirthplace"
                                                                type="text" placeholder="Masukan tempat lahir"
                                                                name="birthplace">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentGender">Jenis
                                                                Kelamin<span class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentGender"
                                                                name="gender">
                                                                <option value="M">Laki-laki</option>
                                                                <option value="F">Perempuan</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentReligion">Agama<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentReligion"
                                                                name="religion">
                                                                @foreach ($religions as $religion)
                                                                    <option value="{{ $religion }}">
                                                                        {{ $religion }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentAdmissionYear">Tahun
                                                                Masuk<span class="txt-danger">*</span>
                                                            </label>
                                                            <input class="form-control" id="studentAdmissionYear"
                                                                type="number" placeholder="Masukan tahun masuk"
                                                                name="admission_year" min="2000"
                                                                max="{{ date('Y') + 1 }}">
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label" for="studentStatus">Status<span
                                                                    class="txt-danger">*</span>
                                                            </label>
                                                            <select class="form-select" id="studentStatus"
                                                                name="status">
                                                                <option value="active">Aktif</option>
                                                                <option value="transferred">Pindah</option>
                                                                <option value="graduated">Lulus</option>
                                                                <option value="dropout">Keluar</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="editStudentSubmitBtn">Simpan</button>
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
                                    <button class="btn btn-danger mb-2" id="delete-selected" disabled>Hapus
                                        Terpilih</button>
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
                                                    <div
                                                        class="form-check d-flex justify-content-center align-items-center">
                                                        <input class="form-check-input" id="select-all" type="checkbox"
                                                            style="width: 12px; height: 12px;" value>
                                                    </div>
                                                </div>
                                            </th>
                                            <th> <span class="c-o-light f-w-600">Nama</span></th>
                                            <th> <span class="c-o-light f-w-600">NIS</span></th>
                                            <th> <span class="c-o-light f-w-600">NISN</span></th>
                                            <th> <span class="c-o-light f-w-600">Kelas</span></th>
                                            <th> <span class="c-o-light f-w-600">Wali Kelas</span></th>
                                            <th> <span class="c-o-light f-w-600">Status</span></th>
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
    <script src="{{ asset('assets/js/student-crud.js') }}"></script>
@endsection
