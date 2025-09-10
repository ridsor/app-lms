<div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Buat Ujian</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <form action=""></form>
                <form class="needs-validation" method="POST" action="" novalidate="" id="addExamForm">
                    <div class="text-start">
                        <div class="p-20">
                            <div class="row g-3">
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
                                            <label class="form-label" for="addExamType">Jenis Ujian<span
                                                    class="txt-danger">*</span></label>
                                            <select class="form-select" id="addExamType" name="type">
                                                <option value="">Jenis Ujian</option>
                                                @foreach ($examTypes as $item)
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
                                                <input class="form-control flatpicker" id="addExamStartTime"
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
                                                    id="addExamEndTime" type="date" placeholder="Pilih waktu selesai"
                                                    name="end_time" data-language="id">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="addSchedule">Jadwal<span
                                                    class="txt-danger">*</span></label>
                                            <select class="selectpicker search-picker" data-live-search="true"
                                                id="addSchedule" name="schedule_id">
                                                <option value="">Pilih Jadwal</option>
                                                @foreach ($schedules as $schedule)
                                                    <option value="{{ $schedule->id }}" class="text-uppercase">
                                                        {{ $schedule->subject->code }} - {{ $schedule->subject->name }}
                                                        -
                                                        {{ $schedule->class->name }}{{ $schedule->class->level }}{{ ' ' . $schedule->class->major?->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label" for="addExamDescription">Deskripsi<span
                                                    class="txt-danger"></span></label>
                                            <div class="toolbar-box">
                                                <div id="addExamToolbar">
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
                                                <div id="addExamDescriptionQuill"></div>
                                                <input type="hidden" id="addExamDescription" name="description"
                                                    class="quill">
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="addAllowLateSubmission">Sifat</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="Closed Book" checked
                                                        name="exam_mode" class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tutup Buku</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="Open Book"
                                                        class="me-2 form-check-input radio" name="exam_mode"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Buka Terbuka</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="addAllowLateSubmission">Acak Soal</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="0" checked
                                                        name="is_shuffle_questions"
                                                        class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tidak</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="1"
                                                        class="me-2 form-check-input radio"
                                                        name="is_shuffle_questions"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Ya</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Batas Waktu</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="0" checked
                                                        name="allow_duration" class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tidak</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="1"
                                                        class="me-2 form-check-input radio" name="allow_duration"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Ya</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 duration" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <label class="form-label" for="addDurationExam">Batas Waktu</label>
                                                <input class="form-control" autocomplete="off" id="addDurationExam"
                                                    type="number" placeholder="(menit)" name="duration">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-outline-secondary" type="button"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            Batal
                                        </button>
                                        <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editExamModal" tabindex="-1" aria-labelledby="editExamModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ujian</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <form action=""></form>
                <form class="needs-validation" method="POST" action="" novalidate="" id="editExamForm">
                    <div class="text-start">
                        <div class="p-20">
                            <div class="row g-3">
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
                                            <label class="form-label" for="editExamType">Jenis Ujian<span
                                                    class="txt-danger">*</span></label>
                                            <select class="form-select" id="editExamType" name="type">
                                                <option value="">Jenis Ujian</option>
                                                @foreach ($examTypes as $item)
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
                                                <input class="form-control flatpicker" id="editExamStartTime"
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
                                                    id="editExamEndTime" type="date"
                                                    placeholder="Pilih waktu selesai" name="end_time"
                                                    data-language="id">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="editSchedule">Jadwal<span
                                                    class="txt-danger">*</span></label>
                                            <select class="selectpicker search-picker" data-live-search="true"
                                                id="editSchedule" name="schedule_id">
                                                <option value="">Pilih Jadwal</option>
                                                @foreach ($schedules as $schedule)
                                                    <option value="{{ $schedule->id }}" class="text-uppercase">
                                                        {{ $schedule->subject->code }} -
                                                        {{ $schedule->subject->name }}
                                                        -
                                                        {{ $schedule->class->name }}{{ $schedule->class->level }}{{ ' ' . $schedule->class->major?->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label" for="editExamDescription">Deskripsi<span
                                                    class="txt-danger"></span></label>
                                            <div class="toolbar-box">
                                                <div id="editExamToolbar">
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
                                                <div id="editExamDescriptionQuill"></div>
                                                <input type="hidden" id="editExamDescription" name="description"
                                                    class="quill">
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="editAllowLateSubmission">Sifat</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="Closed Book" checked
                                                        name="exam_mode" class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tutup Buku</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="Open Book"
                                                        class="me-2 form-check-input radio" name="exam_mode"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Buka Terbuka</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="editAllowLateSubmission">Acak Soal</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="0" checked
                                                        name="is_shuffle_questions"
                                                        class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tidak</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="1"
                                                        class="me-2 form-check-input radio"
                                                        name="is_shuffle_questions"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Ya</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Batas Waktu</label>
                                            <div class="checkbox-checked d-flex gap-2">
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="0" checked
                                                        name="allow_duration" class="me-2 form-check-input radio"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Tidak</span>
                                                </label>
                                                <label class="d-flex align-items-center mb-0"
                                                    style="align-self: flex-start">
                                                    <input type="radio" value="1"
                                                        class="me-2 form-check-input radio" name="allow_duration"
                                                        style="transform: translateY(-2px)">
                                                    <span class="fw-bold text-uppercase">Ya</span>
                                                </label>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 duration" style="display: none;">
                                            <div class="d-flex flex-column">
                                                <label class="form-label" for="editDurationExam">Batas Waktu</label>
                                                <input class="form-control" autocomplete="off" id="editDurationExam"
                                                    type="number" placeholder="(menit)" name="duration">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-outline-secondary" type="button"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            Batal
                                        </button>
                                        <button class="btn btn-primary" type="submit" id="submit">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
