@extends('layouts.user.app')

@section('title', 'Manajemen Kelas')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3> Category</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item active">Kelas</li>
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
                        <div class="row g-3">
                            <div class="col-md"><label class="form-label">Tingkat</label><select class="form-select"
                                    id="level-filter" aria-label="Select parent category">
                                    <option value="" selected>Pilih Tingkat</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}">{{ $level->level }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"> Please choose a parent category.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col-md"><label class="form-label">Jurusan</label><select class="form-select"
                                    id="major-filter" aria-label="Select parent category">
                                    <option value="" selected>Pilih Jurusan</option>
                                    @foreach ($majors as $major)
                                        <option value="{{ $major->major }}">{{ $major->major }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"> Please choose a category type.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="col d-flex justify-content-start align-items-center m-t-40"><a
                                    class="btn btn-primary f-w-500" id="filter-btn" href="#!">Submit</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon"><a class="btn btn-primary f-w-500" href="#!"
                                data-bs-toggle="modal" data-bs-target="#dashboard8"><i class="fa fa-plus pe-2"></i>Tambah
                                Kelas</a>
                            <div class="modal fade" id="dashboard8" tabindex="-1" aria-labelledby="dashboard8"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content category-popup">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modaldashboard">Tambah Kelas</h5>
                                            <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 custom-input">
                                            <div class="text-start">
                                                <div class="p-20">
                                                    <form class="row g-3 needs-validation" novalidate="">
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="validationCategoryName">Category
                                                                Name<span class="txt-danger">*</span></label><input
                                                                class="form-control" id="validationCategoryName"
                                                                type="text" placeholder="Enter your category name"
                                                                required="">
                                                            <div class="invalid-feedback"> Please enter a
                                                                category name.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label"
                                                                for="validationSlug">Slug<span
                                                                    class="txt-danger">*</span></label><input
                                                                class="form-control" id="validationSlug" type="text"
                                                                placeholder="Enter slug" required="">
                                                            <div class="invalid-feedback"> Please enter a
                                                                slug name.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-12"><label class="form-label">Parent
                                                                Category<span class="txt-danger">*</span></label><select
                                                                class="form-select" aria-label="Select parent category">
                                                                <option selected="">T-shirts</option>
                                                                <option value="1">Purse</option>
                                                                <option value="2">Cameras</option>
                                                                <option value="3">Shoes </option>
                                                                <option value="4">Handbags</option>
                                                                <option value="5">Sleepers</option>
                                                                <option value="6">Watches</option>
                                                            </select>
                                                            <div class="invalid-feedback"> Please choose a
                                                                parent category.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label">Category Type<span
                                                                    class="txt-danger">*</span></label><select
                                                                class="form-select" aria-label="Select category type">
                                                                <option selected="">Electronic</option>
                                                                <option value="1">Accessories</option>
                                                                <option value="2">Footwear</option>
                                                                <option value="3">Clothing</option>
                                                                <option value="4">Furniture</option>
                                                            </select>
                                                            <div class="invalid-feedback"> Please choose a
                                                                category type.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-6"><label class="form-label">Category
                                                                Status<span class="txt-danger">*</span></label><select
                                                                class="form-select" aria-label="Select category status">
                                                                <option selected="">Active</option>
                                                                <option value="1">Inactive</option>
                                                            </select>
                                                            <div class="invalid-feedback"> Please choose a
                                                                category status.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-12"><label class="form-label">Category
                                                                Description</label>
                                                            <div class="toolbar-box">
                                                                <div id="toolbar9"><button class="ql-bold">Bold
                                                                    </button><button class="ql-italic">Italic
                                                                    </button><button
                                                                        class="ql-underline">underline</button><button
                                                                        class="ql-strike">Strike
                                                                    </button><button class="ql-list" value="ordered">List
                                                                    </button><button class="ql-list" value="bullet">
                                                                    </button><button class="ql-indent" value="-1">
                                                                    </button><button class="ql-indent"
                                                                        value="+1"></button><button
                                                                        class="ql-link"></button></div>
                                                                <div id="editor9"></div>
                                                            </div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="main-divider">
                                                                <div class="divider-body">
                                                                    <h6>SEO Tags</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6"><label class="form-label"
                                                                for="validationMetaTitle">Meta Title<span
                                                                    class="txt-danger">*</span></label><input
                                                                class="form-control" id="validationMetaTitle"
                                                                type="text" placeholder="Enter meta title"
                                                                required="">
                                                            <div class="invalid-feedback"> Please enter a
                                                                meta title.</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-lg-6"><label class="form-label"
                                                                for="validationKeyword">Meta Keywords<span
                                                                    class="txt-danger">*</span><span
                                                                    class="c-o-light">&nbsp;(In comma
                                                                    separated)</span></label><input class="form-control"
                                                                id="validationKeyword" type="text"
                                                                placeholder="Enter meta keywords" required="">
                                                            <div class="invalid-feedback"> Please enter a
                                                                meta keywords(In comma separated).</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-12"><label class="form-label">Meta
                                                                Description</label>
                                                            <div class="toolbar-box">
                                                                <div id="toolbar10"><button class="ql-bold">Bold
                                                                    </button><button class="ql-italic">Italic
                                                                    </button><button
                                                                        class="ql-underline">underline</button><button
                                                                        class="ql-strike">Strike
                                                                    </button><button class="ql-list" value="ordered">List
                                                                    </button><button class="ql-list" value="bullet">
                                                                    </button><button class="ql-indent" value="-1">
                                                                    </button><button class="ql-indent"
                                                                        value="+1"></button><button
                                                                        class="ql-link"></button></div>
                                                                <div id="editor10"></div>
                                                            </div>
                                                            <div class="invalid-feedback"> Please enter a
                                                                meta description</div>
                                                            <div class="valid-feedback">Looks good!</div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit">Create
                                                                +</button>
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
                                <table class="table" id="class-table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th> <span class="c-o-light f-w-600">Nama</span></th>
                                            <th> <span class="c-o-light f-w-600">Tingkat</span></th>
                                            <th> <span class="c-o-light f-w-600">Jurusan</span></th>
                                            <th> <span class="c-o-light f-w-600">Kapasitas</span></th>
                                            <th> <span class="c-o-light f-w-600">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @foreach ($classes as $class)
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <div class="product-names">
                                                        <p>{{ $class->name }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="f-light">{{ $class->level }}</p>
                                                </td>
                                                <td> <span class="badge badge-light-primary">{{ $class->major }}</span>
                                                </td>
                                                <td>
                                                    <p class="f-light">{{ $class->capacity }}</p>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start"> <a
                                                            class="square-white" href="#!"><svg>
                                                                <use
                                                                    href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}">
                                                                </use>
                                                            </svg></a><a class="square-white trash-3" href="#!"><svg>
                                                                <use
                                                                    href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}">
                                                                </use>
                                                            </svg></a></div>
                                                </td>
                                            </tr>
                                        @endforeach --}}
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
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/modalpage/validation-modal.js') }}"></script>
    <script>
        var editor9 = new Quill("#editor9", {
            modules: {
                toolbar: "#toolbar9"
            },
            theme: "snow",
            placeholder: "Enter your messages...",
        });
        var editor10 = new Quill("#editor10", {
            modules: {
                toolbar: "#toolbar10"
            },
            theme: "snow",
            placeholder: "Enter your messages...",
        });

        $(function() {
            var t = $("#class-table").DataTable({
                columnDefs: [{
                    orderable: false,
                    render: $.fn.dataTable.render.select(),
                    targets: 0,
                }, ],

                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.class.index') }}",
                    data: function(d) {
                        d.level = $('#level-filter').val();
                        d.major = $('#major-filter').val();
                        // Tambahkan parameter search per kolom
                        d.search_name = $('#search-name').val();
                        d.search_level = $('#search-level').val();
                        d.search_major = $('#search-major').val();
                        d.search_capacity = $('#search-capacity').val();
                    }
                },
                columns: [{
                        data: '',
                        name: ''
                    },
                    {
                        data: 'Nama',
                        name: 'Nama'
                    },
                    {
                        data: 'Tingkat',
                        name: 'Tingkat'
                    },
                    {
                        data: 'Jurusan',
                        name: 'Jurusan'
                    },
                    {
                        data: 'Kapasitas',
                        name: 'Kapasitas'
                    },
                    {
                        data: 'Aksi',
                        name: 'Aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix": "",
                    "sSearch": "Cari:",
                    "sUrl": "",
                    // "oPaginate": {
                    //     "sFirst": "Pertama",
                    //     "sPrevious": "Sebelumnya",
                    //     "sNext": "Selanjutnya",
                    //     "sLast": "Terakhir"
                    // }
                },
                fixedColumns: {
                    leftColumns: 2,
                },
                order: [
                    [1, "asc"]
                ],
                scrollCollapse: true,
                select: {
                    style: "multi",
                    selector: "td:first-child",
                },
                searchable: true,
                pageLength: 10,
                responsive: true,
                lengthMenu: [10, 14, 18, 22],
                autoWidth: false,
            });

            // Event handler untuk filter
            $('#filter-btn').click(function(e) {
                e.preventDefault();
                t.draw();
            });

            // Event handler untuk search per kolom
            $('#search-name, #search-level, #search-major, #search-capacity').on('keyup', function() {
                t.draw();
            });

            // Event handler untuk clear search
            $('#clear-search').click(function() {
                $('#search-name, #search-level, #search-major, #search-capacity').val('');
                t.draw();
            });

            // Event handler untuk search global
            $('#global-search').on('keyup', function() {
                t.search($(this).val()).draw();
            });

            // Event handler untuk clear global search
            $('#clear-global-search').click(function() {
                $('#global-search').val('');
                t.search('').draw();
            });
        });
    </script>
@endsection
