@extends('layouts.user.app')

@section('title', 'Jurnal Mengajar')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid e-category p-0">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Jurnal Mengajar</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card rounded-responsive">
                    <div class="card-header card-no-border text-end">
                        <div class="py-3"></div>
                        <div class="modal fade" id="journalModal" tabindex="-1" aria-labelledby="journalModal"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content category-popup">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Jurnal</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0 custom-input">
                                        <div class="text-start">
                                            <div class="p-20">
                                                <div class="row g-2 mb-4">
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Kode Matpel</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span>
                                                                {{ $schedule->subject->code }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Mata Pelajaran</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span>
                                                                {{ $schedule->subject->name }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Kelas</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span>
                                                                {{ $schedule->class->name }}{{ $schedule->class->level }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    @if ($schedule->class->major)
                                                        <div class="col-12 col-md-6 col-lg-4">
                                                            <label class="form-label">Jurusan</label>
                                                            <p class="c-o-light f-w-600">
                                                                <span>
                                                                    {{ $schedule->class->major->name }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Tanggal</label>
                                                        <div class="c-o-light f-w-600">
                                                            <div class="d-flex align-items-center">
                                                                <span
                                                                    class="icon d-inline-flex justify-content-center align-items-center">
                                                                    <i data-feather="calendar"
                                                                        style="width:18px; height: 18px"></i>
                                                                </span>
                                                                <span class="mb-0 ms-2"
                                                                    id="date">{{ '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Ruangan</label>
                                                        <p class="c-o-light f-w-600">
                                                            <span>
                                                                {{ $schedule->room->name }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Waktu Mulai & Selesai</label>
                                                        <div class="d-flex flex-column gap-1">
                                                            <div class="c-o-light f-w-600 text-nowrap">
                                                                <span>
                                                                    <span id="start_time">-</span>
                                                                    -
                                                                    <span id="end_time">-</span>
                                                                    WIT
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <label class="form-label">Kelas Dimulai</label>
                                                        <div class="c-o-light f-w-600">
                                                            <div class="d-flex align-items-center">
                                                                <span
                                                                    class="icon d-inline-flex justify-content-center align-items-center">
                                                                    <i data-feather="calendar"
                                                                        style="width:18px; height: 18px"></i>
                                                                </span>
                                                                <span class="mb-0 ms-2"
                                                                    id="started_at">{{ '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label" for="subjectMatter">Pokok
                                                            Pembahasan</label>
                                                        <input class="form-control w-100" disabled id="subjectMatter"
                                                            type="text" placeholder="Masukan pokok pembahasan"
                                                            value="" name="subject_matter">
                                                        <div class="invalid-feedback">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label" for="subSubjectMatter">Sub Pokok
                                                            Pembahasan</label>
                                                        <div class="toolbar-box">
                                                            <div id="toolbarsubSubjectMatter">
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
                                                            <div id="subSubjectMatterQuill"></div>
                                                            <input type="hidden" id="subSubjectMatter"
                                                                name="sub_subject_matter" class="quill">
                                                        </div>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label" for="additionalNote">Catatan
                                                            Tambahan</label>
                                                        <div class="toolbar-box">
                                                            <div id="toolbaradditionalNote">
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
                                                            <div id="additionalNoteQuill"></div>
                                                            <input type="hidden" id="additionalNote"
                                                                name="additional_note" class="quill">
                                                        </div>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product list-category">
                            <div class="recent-table table-responsive custom-scrollbar">
                                <table class="table table-bordered" id="teaching-journal-schedule-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">Pertemuan</span></th>
                                            <th><span class="c-o-light f-w-600">Waktu</span></th>
                                            <th><span class="c-o-light f-w-600"></span> </th>
                                        </tr>
                                    </thead>
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
    <script>
        const schedule_id = @json($schedule->id);
    </script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/datatable-pipeline.js') }}"></script>
    <script src="{{ asset('assets/js/teaching-journal-meeting-by-schedule.js') }}"></script>
@endsection
