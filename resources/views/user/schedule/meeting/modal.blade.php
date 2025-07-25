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
                                            <i class="fa-solid fa-calendar"></i>
                                            <span class="mb-0 ms-2"
                                                id="date">{{ Helper::getDayName($schedule_time->day) }},
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
                                <input class="form-control" id="meetingTitle" type="text"
                                    placeholder="Masukan judul pertemuan" name="title" value='{{ $meeting->title }}'
                                    value="{{ $meeting->title }}">
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
                                            @if ($item['value'] == $meeting->type) checked @endif>
                                            {{ $item['label'] }}</option>
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
                                            @if ($item['value'] == $meeting->type) checked @endif>
                                            {{ $item['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fillRealizationModal" tabindex="-1" aria-labelledby="fillRealizationModal"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
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
                                        <i class="fa-solid fa-calendar"></i>
                                        <span class="mb-0 ms-2" id="date">{{ $meeting?->date ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <label class="form-label">Waktu Mulai & Selesai</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="c-o-light f-w-600 text-nowrap">
                                        <span>
                                            <span
                                                id="start_time">{{ $meeting->started_at?->translatedFormat('H:i') }}</span>
                                            -
                                            <span
                                                id="end_time">{{ $meeting->schedule_time->end_time->translatedFormat('H:i') }}</span>
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
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                            </div>
                        </form>
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
                <h5 class="modal-title">Materi</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="needs-validation row g-3" method="POST" novalidate="" id="addMaterialForm" data-meeting-id="{{ $meeting->id }}"
                            data-id='{{ $meeting->id }}'>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="materialTitle">Judul<span
                                                class="txt-danger">*</span></label>
                                        <input class="form-control" id="materialTitle" type="text"
                                            placeholder="Masukan judul pertemuan" name="title">
                                        <div class="invalid-feedback">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="materialDescription">Deskripsi<span
                                                class="txt-danger">*</span></label>
                                        <div class="toolbar-box">
                                            <div id="materialToolbar">
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
                                            <div id="materialDescriptionQuill"></div>
                                            <input type="hidden" id="materialDescription" name="description"
                                                class="quill">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="materialType">Tipe File<span
                                                class="txt-danger">*</span></label>
                                        <select class="form-select" id="materialType" name="file_type">
                                            <option value="">Pilih Tipe</option>
                                            @foreach ($materialType as $item)
                                                <option value="{{ $item['value'] }}"
                                                    @if ($item['value'] == $meeting->type) checked @endif>
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
                                            <div class="w-100 border rounded-2 px-3 py-3" id="custom-file-upload"
                                                style="pointer-events: none; opacity: 0.6;" aria-disabled="true">
                                                <label for="materialFile" class="d-flex align-items-center mb-0 w-100"
                                                    style="cursor:pointer;">
                                                    <span
                                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                        <i class="fa fa-upload text-primary fs-5"></i>
                                                    </span>
                                                    <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                                </label>
                                            </div>
                                            <input type="file" class="form-control file_path" id="materialFile"
                                                name="file_path" style="display:none;">
                                            <div id="file-preview"></div>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <div class="materialLink" style="display:none;">
                                            <label class="form-label" for="materialLink">Link<span
                                                    class="txt-danger">*
                                                </span>
                                            </label>
                                            <input class="form-control" id="materialLink" type="text"
                                                placeholder="Masukan link materi" name="material_link" />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
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
                                                <p class="f-light mb-0">{{ $attendance['student']->nisn }}</p>
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
                    <div class="col-md-12 p-3 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit" id="save_attendance"
                            data-meeting-id="{{ $meeting->id }}">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
