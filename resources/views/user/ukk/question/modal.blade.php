<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="modaldashboard">Tambah Soal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="addQuestionForm" data-id="{{ $ukk->id }}" data-type="ukk">
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
                    Format file PNG / JPG /
                    JPEG / MP3 / WAV / MP4 / WEBM
                    <br />
                    Ukuran maksimal 5MB
                  </div>
                  <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                    <label for="addQuestionFile" class="d-flex align-items-center mb-0 w-100" style="cursor:pointer;">
                      <span
                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                        <i class="fa fa-upload text-primary fs-5"></i>
                      </span>
                      <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                    </label>
                  </div>
                  <input type="file" class="form-control file_path" id="addQuestionFile" name="question_file" hidden
                    accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
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
                      <input type="radio" name="correct_answer" value="a" class="me-2 form-check-input">
                      <span class="fw-bold text-uppercase">a.</span>
                      <input type="text" class="form-control" name="option_a" placeholder="Tulis pilihan jawaban">
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
                      <input type="radio" name="correct_answer" value="b" class="me-2 form-check-input">
                      <span class="fw-bold text-uppercase">b.</span>
                      <input type="text" class="form-control" name="option_b" placeholder="Tulis pilihan jawaban">
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
                    <!-- Opsi C -->
                    <div class="answer-option d-flex align-items-center checkbox-checked">
                      <input type="radio" name="correct_answer" value="c" class="me-2 form-check-input">
                      <span class="fw-bold text-uppercase">c.</span>
                      <input type="text" class="form-control" placeholder="Tulis pilihan jawaban"
                        name="option_c">
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
                  </div>
                  <div class="invalid-feedback"></div>
                  <button class="addOption text-primary p-0 bg-transparent border-0">Tambahkan
                    Opsi</button>
                </div>
              </div>
              <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal" aria-label="Close">
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

<div class="modal fade" id="addEssayQuestionModal" tabindex="-1" aria-labelledby="addEssayQuestionModal"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="modaldashboard">Tambah Soal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="addEssayQuestionForm"
              data-id="{{ $ukk->id }}" data-type="ukk">
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
                  <div id="addEssayQuestionTextQuill"></div>
                  <input type="hidden" id="addEssayQuestionText" name="question_text" class="quill">
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12">
                <div class="taskFile">
                  <label class="form-label">File</label>
                  <div class="info text-danger mb-1" style="font-size: 12px;">
                    Format file PNG / JPG /
                    JPEG / MP3 / WAV / MP4 / WEBM
                    <br />
                    Ukuran maksimal 5MB
                  </div>
                  <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                    <label for="addEssayQuestionFile" class="d-flex align-items-center mb-0 w-100"
                      style="cursor:pointer;">
                      <span
                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                        <i class="fa fa-upload text-primary fs-5"></i>
                      </span>
                      <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                    </label>
                  </div>
                  <input type="file" class="form-control file_path" id="addEssayQuestionFile"
                    name="question_file" hidden
                    accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
                  <div id="file-preview" class="d-flex flex-column gap-1"></div>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal" aria-label="Close">
                  Batal
                </button>
                <button class="btn btn-primary" type="submit" id="addEssayQuestionSubmitBtn">Tambah
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
                    Format file PNG / JPG /   JPEG / MP3 / WAV / MP4 / WEBM
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
                  <input type="file" class="form-control file_path" id="editQuestionFile" name="question_file"
                    hidden
                    accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
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
                      <input type="radio" name="correct_answer" value="a" class="me-2 form-check-input">
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
                      <input type="radio" name="correct_answer" value="b" class="me-2 form-check-input">
                      <span class="fw-bold text-uppercase">b.</span>
                      <input type="text" class="form-control" name="option_b"
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
                    <!-- Opsi C -->
                    <div class="answer-option d-flex align-items-center checkbox-checked">
                      <input type="radio" name="correct_answer" value="c" class="me-2 form-check-input">
                      <span class="fw-bold text-uppercase">c.</span>
                      <input type="text" class="form-control" placeholder="Tulis pilihan jawaban"
                        name="option_c">
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
                  </div>
                  <div class="invalid-feedback"></div>
                  <button class="addOption text-primary p-0 bg-transparent border-0">Tambahkan
                    Opsi</button>
                </div>
              </div>
              <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal" aria-label="Close">
                  Batal
                </button>
                <button class="btn btn-primary" type="submit" id="editStudentSubmitBtn">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editEssayQuestionModal" tabindex="-1" aria-labelledby="editEssayQuestionModal"
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
            <form class="row g-3 needs-validation" novalidate="" id="editEssayQuestionForm">
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
                  <div id="editEssayQuestionTextQuill"></div>
                  <input type="hidden" id="editEssayQuestionText" name="question_text" class="quill">
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12">
                <div class="taskFile">
                  <label class="form-label">File</label>
                  <div class="info text-danger mb-1" style="font-size: 12px;">
                    Format file PNG / JPG / JPEG / MP3 / WAV / MP4 / WEBM
                    <br />
                    Ukuran maksimal 5MB
                  </div>
                  <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                    <label for="editEssayQuestionFile" class="d-flex align-items-center mb-0 w-100"
                      style="cursor:pointer;">
                      <span
                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                        <i class="fa fa-upload text-primary fs-5"></i>
                      </span>
                      <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                    </label>
                  </div>
                  <input type="file" class="form-control file_path" id="editEssayQuestionFile"
                    name="question_file" hidden
                    accept=".jpg,.jpeg,.png,.mp3,.wav,.mp4,.webm">
                  <div id="file-preview" class="d-flex flex-column gap-1"></div>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal" aria-label="Close">
                  Batal
                </button>
                <button class="btn btn-primary" type="submit" id="editStudentSubmitBtn">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importQuestionModal" tabindex="-1" aria-labelledby="importQuestionModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title">Import Soal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="importQuestionForm" method="POST"
              action="{{ route('user.ukk.importQuestions', $ukk->id) }}" enctype="multipart/form-data">
              @csrf
              <div class="col-12">
                <div class="taskFile">
                  <label class="form-label">File Template Soal</label>
                  <div class="info text-secondary mb-3" style="font-size: 12px; line-height: 1.5;">
                    <span class="text-danger fw-bold">Format file: .xls, .xlsx, atau .zip</span><br>
                    Jika menyertakan gambar, unggah file <strong>.zip</strong> dengan struktur berikut:
                    <div class="bg-light p-2 rounded-2 mt-2 border border-light-subtle">
                      <code class="text-dark" style="font-family: monospace; white-space: pre-wrap;">arsip_soal.zip
├── soal_ukk.xlsx (Data Soal)
└── media/ (Folder Gambar)
    ├── soal1_soal.png
    └── soal1_opsi_a.png</code>
                    </div>
                    <p class="mt-2 mb-1 fw-bold text-dark">Langkah-langkah:</p>
                    <ol class="ps-3 mb-2">
                      <li>Siapkan file Excel sesuai template.</li>
                      <li>Masukkan semua gambar ke dalam folder bernama <strong class="text-primary">media</strong> (huruf kecil semua).</li>
                      <li>Pilih file Excel dan folder media secara bersamaan.</li>
                      <li>Klik kanan > <strong>Compress to ZIP</strong>.</li>
                    </ol>
                    <div class="alert alert-light-info p-2 mb-0" style="font-size: 11px;">
                      <i class="fa fa-info-circle me-1"></i> Pastikan nama file di Excel sama persis dengan nama file di folder media.
                    </div>
                  </div>
                  <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                    <label for="importQuestionFile" class="d-flex align-items-center mb-0 w-100"
                      style="cursor:pointer;">
                      <span
                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                        <i class="fa fa-upload text-primary fs-5"></i>
                      </span>
                      <span style="color:#b0b0b0; font-weight:500;">Unggah File Template</span>
                    </label>
                  </div>
                  <input type="file" class="form-control file_path" id="importQuestionFile" name="import_file" hidden
                    accept=".xls,.xlsx,.zip">
                  <div id="import-file-preview" class="d-flex flex-column gap-1 mt-2"></div>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-md-12 d-flex justify-content-end gap-2 mt-4">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal"
                  aria-label="Close">Batal</button>
                <button class="btn btn-primary" type="submit" id="importQuestionSubmitBtn">Import</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
