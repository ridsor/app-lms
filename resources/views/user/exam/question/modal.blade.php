<div class="modal fade" id="addQuestionkModal" tabindex="-1" aria-labelledby="addQuestionkModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldashboard">Tambah Soal</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="row g-3 needs-validation" novalidate="" id="addQuestionForm"
                            data-id="{{ $exam->id }}">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <label class="d-flex gap-2 align-items-center justify-content-center">
                                        <p class="mb-0">Poin : </p>
                                        <input type="number" style="width: 50px" name="question_points"
                                            class="form-control form-control-sm text-center question-points">
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="toolbar-box">
                                    <div id="addQuestionTextQuill"></div>
                                    <input type="hidden" id="addQuestionText" name="question_text" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <div class="taskFile">
                                    <label class="form-label">File</label>
                                    <div class="info text-danger mb-1" style="font-size: 12px;">
                                        Format file PDF / DOC / DOCX / XLS / XLSX / PPT / PPTX / ZIP / RAR / PNG / JPG /
                                        JPEG / MP3 / WAV / MP4 / WEBM
                                        <br />
                                        Ukuran maksimal 5MB
                                    </div>
                                    <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                                        <label for="addQuestionFile" class="d-flex align-items-center mb-0 w-100"
                                            style="cursor:pointer;">
                                            <span
                                                style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                <i class="fa fa-upload text-primary fs-5"></i>
                                            </span>
                                            <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                        </label>
                                    </div>
                                    <input type="file" class="form-control file_path" id="addQuestionFile"
                                        name="question_file" hidden
                                        accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp3,.wav,.mp4,.webm">
                                    <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <p class="text-warning">Pilih untuk tetapkan kunci jawaban</p>
                                <div id="optionsForm">
                                    <div id="optionsContainer">
                                        <!-- Opsi A -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="a"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">a.</span>
                                            <input type="text" class="form-control" name="option_a"
                                                placeholder="Tulis pilihan jawaban">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_a_image"
                                                    class="form-control form-control-sm option-image" accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                        <!-- Opsi B -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="b"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">b.</span>
                                            <input type="text" class="form-control" name="option_b"
                                                placeholder="Tulis pilihan jawaban">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_b_image"
                                                    class="form-control form-control-sm option-image" accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                        <!-- Opsi C -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="c"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">c.</span>
                                            <input type="text" class="form-control"
                                                placeholder="Tulis pilihan jawaban" name="option_c">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_c_image"
                                                    class="form-control form-control-sm option-image"
                                                    accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                    <button class="addOption text-primary p-0 bg-transparent border-0">Tambahkan
                                        Opsi</button>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Batal
                                </button>
                                <button class="btn btn-primary" type="submit" id="addStudentSubmitBtn">Tambah
                                    +</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldashboard">Edit Soal</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="row g-3 needs-validation" novalidate="" id="editQuestionForm">
                            <input type="hidden" name="deleteData[]" />
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <label class="d-flex gap-2 align-items-center justify-content-center">
                                        <p class="mb-0">Poin : </p>
                                        <input type="number" style="width: 50px" name="question_points"
                                            class="form-control form-control-sm text-center question-points">
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="toolbar-box">
                                    <div id="editQuestionTextQuill"></div>
                                    <input type="hidden" id="editQuestionText" name="question_text" class="quill">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <div class="taskFile">
                                    <label class="form-label">File</label>
                                    <div class="info text-danger mb-1" style="font-size: 12px;">
                                        Format file PDF / DOC / DOCX / XLS / XLSX / PPT / PPTX / ZIP / RAR / PNG / JPG /
                                        JPEG / MP3 / WAV / MP4 / WEBM
                                        <br />
                                        Ukuran maksimal 5MB
                                    </div>
                                    <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                                        <label for="editQuestionFile" class="d-flex align-items-center mb-0 w-100"
                                            style="cursor:pointer;">
                                            <span
                                                style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                                                <i class="fa fa-upload text-primary fs-5"></i>
                                            </span>
                                            <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                                        </label>
                                    </div>
                                    <input type="file" class="form-control file_path" id="editQuestionFile"
                                        name="question_file" hidden
                                        accept=".zip,.rar,.pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp3,.wav,.mp4,.webm">
                                    <div id="file-preview" class="d-flex flex-column gap-1"></div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <p class="text-warning">Pilih untuk tetapkan kunci jawaban</p>
                                <div id="optionsForm">
                                    <div id="optionsContainer">
                                        <!-- Opsi A -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="a"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">a.</span>
                                            <input type="text" class="form-control" name="option_a"
                                                placeholder="Tulis pilihan jawaban">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_a_image"
                                                    class="form-control form-control-sm option-image"
                                                    accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                        <!-- Opsi B -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="b"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">b.</span>
                                            <input type="text" class="form-control" name="option_b"
                                                placeholder="Tulis pilihan jawaban">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_b_image"
                                                    class="form-control form-control-sm option-image"
                                                    accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                        <!-- Opsi C -->
                                        <div class="answer-option d-flex align-items-center checkbox-checked">
                                            <input type="radio" name="correct_answer" value="c"
                                                class="me-2 form-check-input">
                                            <span class="fw-bold text-uppercase">c.</span>
                                            <input type="text" class="form-control"
                                                placeholder="Tulis pilihan jawaban" name="option_c">
                                            <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                                                <div class="file-icon">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <input type="file" hidden name="option_c_image"
                                                    class="form-control form-control-sm option-image"
                                                    accept="image/*">
                                                <img class="img-preview"
                                                    style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                                            </label>
                                            <span class="remove-option">&times;</span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                    <button class="addOption text-primary p-0 bg-transparent border-0">Tambahkan
                                        Opsi</button>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Batal
                                </button>
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

<div class="modal fade" id="copyQuestionModal" tabindex="-1" aria-labelledby="copyQuestionModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldashboard">Salin Bank Soal</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="e-category">
                        <div class="row">
                            <div class="col-12">
                                <div class="card rounded-responsive">
                                    <div class="card-header card-no-border">
                                        <div class="header-top">
                                            <h5>Filter</h5>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row g-3">
                                            <div class="col-md-4 col-xl">
                                                <label class="form-label" for="subject-filter">Mata Pelajaran</label>
                                                <select class="selectpicker search-picker filter"
                                                    data-live-search="true" id="subject-filter">
                                                    <option value="">Pilih Mata Pelajaran</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{ $subject->name }}">{{ $subject->name }} -
                                                            {{ $subject->curriculum->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-auto d-flex justify-content-start align-items-end">
                                                <a class="btn btn-primary f-w-500 w-100" id="filter-btn">Terapkan</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card rounded-responsive">
                                    <div class="card-header card-no-border text-end">
                                        <div class="py-2"></div>
                                    </div>
                                    <div class="card-body pt-0 px-0">
                                        <div class="list-product list-category">
                                            <div class="recent-table table-responsive custom-scrollbar">
                                                <table class="table table-bordered" id="question-bank-table"
                                                    data-exam-id="{{ $exam->id }}">
                                                    <thead>
                                                        <tr>
                                                            <th><span class="c-o-light f-w-600">Judul</span></th>
                                                            <th><span class="c-o-light f-w-600">Mata Pelajaran</span>
                                                            </th>
                                                            <th><span class="c-o-light f-w-600">Soal</span></th>
                                                            <th><span class="c-o-light f-w-600">Waktu</span></th>
                                                            <th><span class="c-o-light f-w-600"></span></th>
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
                </div>
            </div>
        </div>
    </div>
</div>
