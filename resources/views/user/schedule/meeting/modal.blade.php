@php
    use App\Helpers\Helper;
@endphp

<div class="modal fade" id="editMeetingModal" tabindex="-1" aria-labelledby="editMeetingModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Pertemuan</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label class="form-label">Kelas</label>
                                <p class="c-o-light f-w-600">
                                    <span>
                                        {{ $schedule->class->name }}{{ $schedule->class->level }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Ruangan</label>
                                <p class="c-o-light f-w-600">
                                    <span>
                                        {{ $schedule->room->name }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <label class="form-label">Waktu</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex gap-2 c-o-light f-w-600 flex-wrap text-nowrap">
                                        <div class="d-flex align-items-center">
                                            <span class="icon d-inline-flex justify-content-center align-items-center">
                                                <i data-feather="calendar" style="width:18px; height: 18px"></i>
                                            </span>
                                            <span class="mb-0 ms-2" id="date">{{ $meeting->formatted_date }},
                                            </span>
                                        </div>
                                        <div>
                                            <span>
                                                <span
                                                    id="start_time">{{ $meeting->schedule_time->start_time->translatedFormat('H:i') }}</span>
                                                -
                                                <span
                                                    id="end_time">{{ $meeting->schedule_time->end_time->translatedFormat('H:i') }}</span>
                                                WIT
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="row g-3 needs-validation" method="POST" novalidate="" id="editMeetingForm"
                            data-code="{{ $schedule->subject->code }}" data-id='{{ $meeting->id }}'>
                            <div class="col-12">
                                <label class="form-label" for="meetingTitle">Judul</label>
                                <input class="form-control" id="meetingTitle" type="text" placeholder="Tulis judul"
                                    name="title" value='{{ $meeting->title }}' value="{{ $meeting->title }}">
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="meetingDescription">Deskripsi</label>
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
                                    <div id="meetingDescriptionQuill"></div>
                                    <input type="hidden" id="meetingDescription" name="description" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="meetingMethod">Metode Pelajaran<span
                                        class="txt-danger">*</span></label>
                                <select class="form-select" id="meetingMethod" name="meeting_method">
                                    @foreach ($meetingMethods as $item)
                                        <option value="{{ $item['value'] }}"
                                            @if ($item['value'] == $meeting->meeting_method) selected @endif>{{ $item['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="meetingType">Jenis<span
                                        class="txt-danger">*</span></label>
                                <select class="form-select" id="meetingType" name="type">
                                    @foreach ($meetingTypes as $item)
                                        <option value="{{ $item['value'] }}"
                                            @if ($item['value'] == $meeting->type) selected @endif>
                                            {{ $item['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fillRealizationModal" tabindex="-1" aria-labelledby="fillRealizationModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Isi Realisasi</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Tanggal</label>
                                <div class="c-o-light f-w-600">
                                    <div class="d-flex align-items-center">
                                        <span class="icon"><i data-feather="calendar"
                                                style="width:18px; height: 18px"></i></span>
                                        <span class="mb-0 ms-2"
                                            id="date">{{ $meeting?->formatted_date ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <label class="form-label">Waktu Mulai & Selesai</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="c-o-light f-w-600 text-nowrap">
                                        <span>
                                            <span
                                                id="start_time">{{ $meeting->schedule_time->start_time?->translatedFormat('H:i') }}</span>
                                            -
                                            <span
                                                id="end_time">{{ $meeting->schedule_time->end_time?->translatedFormat('H:i') }}</span>
                                            WIT
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label">Ruangan</label>
                                <p class="c-o-light f-w-600">
                                    <span>
                                        Ruangan 1
                                    </span>
                                </p>
                            </div>
                            <div class="col-12">
                                <div class="row justify-content-between align-items-center g-2">
                                    <div class="col-auto col-lg-6">
                                        <h6>Kehadiran Peserta</h6>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#fillAttendanceModal">
                                            Isi Kehadiran
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="row g-3 needs-validation" method="POST" novalidate=""
                            id="fillRealizationForm" data-id='{{ $meeting->id }}'>
                            <div class="col-12">
                                <label class="form-label" for="subjectMatter">Pokok Pembahasan<span
                                        class="txt-danger">*</span></label>
                                <input class="form-control" id="subjectMatter" type="text"
                                    placeholder="Masukan pokok pembahasan"
                                    value="{{ $meeting->teaching_journal?->subject_matter }}" name="subject_matter">
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="subSubjectMatter">Sub Pokok Pembahasan<span
                                        class="txt-danger">*</span></label>
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
                                    <input type="hidden" id="subSubjectMatter" name="sub_subject_matter"
                                        class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="additionalNote">Catatan Tambahan</label>
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
                                    <input type="hidden" id="additionalNote" name="additional_note" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade p-0 p-md-1" id="fillAttendanceModal" tabindex="-1" aria-labelledby="fillAttendanceModal"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1000px;">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Isi Kehadiran</h5>
                <button class="btn-close" type="button" data-bs-toggle="modal"
                    data-bs-target="#fillRealizationModal"></button>
            </div>
            <div class="modal-body p-0 overflow-hidden">
                <div id="fill_attendance">
                    <div class="list-product list-category">
                        <div class="recent-table table-responsive custom-scrollbar">
                            <table class="table table-bordered" id="attendance-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2"> <span class="c-o-light f-w-600">No</span></th>
                                        <th rowspan="2"> <span class="c-o-light f-w-600">Nama</span></th>
                                        <th class="status-column"> <span class="c-o-light f-w-600">Status</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="status-column">
                                            <div class="d-flex gap-3 align-items-center checkbox-checked">
                                                @foreach ($attendanceValue as $key => $value)
                                                    <div class="form-check">
                                                        <label class="form-check-label fs-6 mb-0">
                                                            <input
                                                                class="form-check-input border-secondary border status-all-{{ $value }}"
                                                                name="{{ 'status-all' }}"
                                                                value="{{ $value }}"
                                                                type="radio">{{ $value }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $key => $attendance)
                                        <tr>
                                            <td>
                                                <p class="f-light mb-0">{{ $key + 1 }}</p>
                                            </td>
                                            <td>
                                                <p class="f-light mb-0">
                                                    {{ $attendance['student']->name }}</p>
                                                <p class="f-light mb-0">{{ $attendance['student']->nis }}</p>
                                            </td>
                                            <td class="status-input" style="padding: 12px 20px;"
                                                data-user-id="{{ $attendance['student']->user_id }}">
                                                <div class="d-flex gap-3 align-items-center checkbox-checked">
                                                    @foreach ($attendanceValue as $value)
                                                        <div class="form-check">
                                                            <label class="form-check-label fs-6 mb-0">
                                                                <input class="form-check-input border-3 status-value"
                                                                    name="{{ 'status' . $key }}"
                                                                    value="{{ $value }}" type="radio"
                                                                    @if ($attendance['status'] == $value) checked @endif>{{ $value }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end gap-2 p-3">
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                aria-label="Close">
                                Batal
                            </button>
                            <button class="btn btn-primary" type="submit" id="save_attendance"
                                data-meeting-id="{{ $meeting->id }}">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Buat Materi</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="material-form needs-validation row g-3" method="POST" novalidate=""
                            id="addMaterialForm" data-id='{{ $meeting->id }}'>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="addMaterialTitle">Judul<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="addMaterialTitle" type="text"
                                            placeholder="Tulis judul" name="title">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="addMaterialDescription">Deskripsi<span
                                                class="txt-danger">*</span></label>
                                        <div class="toolbar-box">
                                            <div id="addMaterialMaterialToolbar">
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
                                            <div id="addMaterialDescriptionQuill"></div>
                                            <input type="hidden" id="addMaterialDescription" name="description"
                                                class="quill">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="addMaterialType">Tipe File<span
                                                class="txt-danger">*</span></label>
                                        <select class="form-select" id="addMaterialType" name="file_type">
                                            <option value="">Pilih Tipe</option>
                                            @foreach ($materialType as $item)
                                                <option value="{{ $item['value'] }}">
                                                    {{ $item['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="materialFile">
                                            <label class="form-label">File<span class="txt-danger">*</span></label>
                                            <div class="info text-danger mb-1"
                                                style="font-size: 12px; display: none;">
                                                Ukuran maksimal file 5mb
                                            </div>
                                            <div class="custom-file-upload w-100 border rounded-2 px-3 py-3"
                                                style="pointer-events: none; opacity: 0.6;" aria-disabled="true">
                                                <label for="addMaterialFile"
                                                    class="d-flex align-items-center mb-0 w-100"
                                                    style="cursor:pointer;">
                                                    <span
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                        <i class="fa fa-upload text-primary fs-5"></i>
                                                    </span>
                                                    <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                                </label>
                                            </div>
                                            <input type="file" class="form-control file_path" id="addMaterialFile"
                                                disabled name="file_path" style="display:none;">
                                            <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <div class="materialLink" style="display:none;">
                                            <label class="form-label" for="addMaterialLink">Link<span
                                                    class="txt-danger">*
                                                </span>
                                            </label>
                                            <input class="form-control" id="addMaterialLink" type="text"
                                                placeholder="Masukan link materi" name="material_link" />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Edit Materi</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="material-form needs-validation row g-3" method="POST" novalidate=""
                            id="editMaterialForm" data-id='{{ $meeting->id }}'>
                            <input type="hidden" name="deletedFile" value="0">
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="editMaterialTitle">Judul<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="editMaterialTitle" type="text"
                                            placeholder="Tulis judul" name="title">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="editMaterialDescription">Deskripsi<span
                                                class="txt-danger">*</span></label>
                                        <div class="toolbar-box">
                                            <div id="editMaterialMaterialToolbar">
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
                                            <div id="editMaterialDescriptionQuill"></div>
                                            <input type="hidden" id="editMaterialDescription" name="description"
                                                class="quill">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="editMaterialType">Tipe File<span
                                                class="txt-danger">*</span></label>
                                        <select class="form-select" id="editMaterialType" name="file_type">
                                            <option value="">Pilih Tipe</option>
                                            @foreach ($materialType as $item)
                                                <option value="{{ $item['value'] }}">
                                                    {{ $item['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="materialFile">
                                            <label class="form-label">File<span class="txt-danger">*</span></label>
                                            <div class="info text-danger mb-1"
                                                style="font-size: 12px; display: none;">
                                                Ukuran maksimal file 5mb
                                            </div>
                                            <div class="custom-file-upload w-100 border rounded-2 px-3 py-3"
                                                style="pointer-events: none; opacity: 0.6;" aria-disabled="true">
                                                <label for="editMaterialFile"
                                                    class="d-flex align-items-center mb-0 w-100"
                                                    style="cursor:pointer;">
                                                    <span
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                        <i class="fa fa-upload text-primary fs-5"></i>
                                                    </span>
                                                    <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                                </label>
                                            </div>
                                            <input type="file" class="form-control file_path" disabled
                                                id="editMaterialFile" name="file_path" style="display:none;">
                                            <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <div class="materialLink" style="display:none;">
                                            <label class="form-label" for="editMaterialLink">Link<span
                                                    class="txt-danger">*
                                                </span>
                                            </label>
                                            <input class="form-control" id="editMaterialLink" type="text"
                                                placeholder="Masukan link materi" name="material_link" />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Buat Tugas</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="material-form needs-validation row g-3" method="POST" novalidate=""
                            id="addTaskForm" data-id='{{ $meeting->id }}'>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="addMaterialTitle">Judul<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="addMaterialTitle" type="text"
                                            placeholder="Tulis judul" name="title">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="addTaskType">Jenis Tugas<span
                                                class="txt-danger">*</span></label>
                                        <select class="form-select" id="addTaskType" name="type">
                                            <option value="">Jenis Tugas</option>
                                            @foreach ($taskType as $item)
                                                <option value="{{ $item['value'] }}">
                                                    {{ $item['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="startDate">Waktu Mulai<span
                                                    class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" id="addTaskStartTime"
                                                type="date" placeholder="Pilih waktu mulai" name="start_time"
                                                data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="endDate">Waktu Selesai<span
                                                    class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" autocomplete="off"
                                                id="addTaskEndTime" type="date" placeholder="Pilih waktu selesai"
                                                name="end_time" data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="addAllowLateSubmission">Pengiriman
                                            Terlambat</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input flatpicker" id="addAllowLateSubmission"
                                                name="allow_late_submission" type="checkbox" role="switch">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12 lateSubmission" style="display: none;">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="endDate">Batas Waktu Terlambat</label>
                                            <input class="form-control flatpicker" autocomplete="off"
                                                id="addLateSubmissionTime" type="date"
                                                placeholder="Pilih batas waktu terlambat" name="late_submission_time"
                                                data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="taskFile">
                                            <label class="form-label">File</label>
                                            <div class="info text-danger mb-1" style="font-size: 12px;">
                                                Ukuran maksimal file 5mb
                                            </div>
                                            <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                                                <label for="addTaskFile" class="d-flex align-items-center mb-0 w-100"
                                                    style="cursor:pointer;">
                                                    <span
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                        <i class="fa fa-upload text-primary fs-5"></i>
                                                    </span>
                                                    <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                                </label>
                                            </div>
                                            <input type="file" class="form-control file_path" id="addTaskFile"
                                                name="file_path" hidden
                                                accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                            <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="addTaskDescription">Deskripsi<span
                                                class="txt-danger"></span></label>
                                        <div class="toolbar-box">
                                            <div id="addTaskToolbar">
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
                                            <div id="addTaskDescriptionQuill"></div>
                                            <input type="hidden" id="addTaskDescription" name="description"
                                                class="quill">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tugas</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="material-form needs-validation row g-3" method="POST" novalidate=""
                            id="editTaskForm" data-id='{{ $meeting->id }}'>
                            <input type="hidden" name="deletedFile" value="0">
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="editMaterialTitle">Judul<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="editMaterialTitle" type="text"
                                            placeholder="Tulis judul" name="title">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="editTaskType">Jenis Tugas<span
                                                class="txt-danger">*</span></label>
                                        <select class="form-select" id="editTaskType" name="type">
                                            <option value="">Jenis Tugas</option>
                                            @foreach ($taskType as $item)
                                                <option value="{{ $item['value'] }}">
                                                    {{ $item['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="startDate">Waktu Mulai<span
                                                    class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" id="editTaskStartTime"
                                                type="date" placeholder="Pilih waktu mulai" name="start_time"
                                                data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="endDate">Waktu Selesai<span
                                                    class="txt-danger">*</span></label>
                                            <input class="form-control flatpicker" autocomplete="off"
                                                id="editTaskEndTime" type="date" placeholder="Pilih waktu selesai"
                                                name="end_time" data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="editAllowLateSubmission">Pengiriman
                                            Terlambat</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" id="editAllowLateSubmission"
                                                name="allow_late_submission" type="checkbox" role="switch">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12 lateSubmission" style="display: none;">
                                        <div class="d-flex flex-column flatpicker-form">
                                            <label class="form-label" for="endDate">Batas Waktu Terlambat</label>
                                            <input class="form-control flatpicker" autocomplete="off"
                                                id="editLateSubmissionTime" type="date"
                                                placeholder="Pilih batas waktu terlambat" name="late_submission_time"
                                                data-language="id">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="taskFile">
                                            <label class="form-label">File</label>
                                            <div class="info text-danger mb-1" style="font-size: 12px;">
                                                Ukuran maksimal file 5mb
                                            </div>
                                            <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                                                <label for="editTaskFile" class="d-flex align-items-center mb-0 w-100"
                                                    style="cursor:pointer;">
                                                    <span
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                        <i class="fa fa-upload text-primary fs-5"></i>
                                                    </span>
                                                    <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                                </label>
                                            </div>
                                            <input type="file" class="form-control file_path" id="editTaskFile"
                                                accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                                name="file_path" hidden>
                                            <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="editTaskDescription">Deskripsi<span
                                                class="txt-danger"></span></label>
                                        <div class="toolbar-box">
                                            <div id="editTaskToolbar">
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
                                            <div id="editTaskDescriptionQuill"></div>
                                            <input type="hidden" id="editTaskDescription" name="description"
                                                class="quill">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMeetingTextModal" tabindex="-1" aria-labelledby="addMeetingTextModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Buat Teks Pertemuan</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="needs-validation row g-3" method="POST" novalidate=""
                            data-meeting-id="{{ $meeting->id }}" data-id='{{ $meeting->id }}'
                            id="addMeetingTextForm">
                            <div class="col-12">
                                <div class="toolbar-box">
                                    <div id="addMeetingTextQuill"></div>
                                    <input type="hidden" id="addMeetingTextQuill" name="text" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="editMeetingTextModal" tabindex="-1" aria-labelledby="editMeetingTextModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Edit Teks Pertemuan</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="needs-validation row g-3" method="POST" novalidate=""
                            id="editMeetingTextForm">
                            <div class="col-12">
                                <div class="toolbar-box">
                                    <div id="editMeetingTextQuill"></div>
                                    <input type="hidden" id="editMeetingTextQuill" name="text" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
