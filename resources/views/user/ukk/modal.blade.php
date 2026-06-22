<div class="modal fade" id="addUkkModal" tabindex="-1" aria-labelledby="addUkkModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title">Buat UKK</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <form class="needs-validation" method="POST" action="" novalidate="" id="addUkkForm"
          enctype="multipart/form-data">
          <div class="text-start">
            <div class="p-20">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label" for="addUkkTitle">Judul<span class="txt-danger">*</span></label>
                      <input class="form-control" id="addUkkTitle" type="text" placeholder="Tulis judul"
                        name="title">
                      <div class="invalid-feedback">
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="addUkkOperator">Operator<span class="txt-danger">*</span></label>
                      <select class="form-select" id="addUkkOperator" name="operator_id">
                        <option value="">Pilih Operator</option>
                        @foreach ($operators as $op)
                          <option value="{{ $op->id }}">
                            {{ $op->name }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="addUkkType">Jenis UKK<span class="txt-danger">*</span></label>
                      <select class="form-select" id="addUkkType" name="type">
                        <option value="">Jenis UKK</option>
                        @foreach ($ukkTypes as $item)
                          <option value="{{ $item['value'] }}">
                            {{ $item['label'] }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="addUkkMajor">Jurusan<span class="txt-danger">*</span></label>
                      <select class="selectpicker search-picker" data-live-search="true" id="addUkkMajor"
                        name="major">
                        <option value="">Pilih Jurusan</option>
                        @foreach ($majors as $major)
                          <option value="{{ $major->name }}" class="text-uppercase">
                            {{ $major->name }}
                          </option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">
                      </div>
                    </div>
                    <div class="extra-fields-praktik" style="display: none;">
                      <div class="col-12">
                        <label class="form-label" for="addUkkCode">Kode</label>
                        <input class="form-control" id="addUkkCode" type="text" placeholder="Contoh: KM25.3.4.1"
                          name="code">
                        <div class="invalid-feedback"></div>
                      </div>
                      <div class="row g-3 mt-0">
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="addUkkPackage">Nomor Paket</label>
                          <input class="form-control" id="addUkkPackage" type="text" placeholder="Contoh: 4"
                            name="package_number">
                          <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="addUkkFormat">Bentuk Soal</label>
                          <input class="form-control" id="addUkkFormat" type="text"
                            placeholder="Contoh: Penugasan Perorangan" name="exam_format">
                          <div class="invalid-feedback"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="row g-3">
                    <div class="col-12">
                      <div class="d-flex flex-column flatpicker-form">
                        <label class="form-label" for="startDate">Waktu Mulai<span
                            class="txt-danger">*</span></label>
                        <input class="form-control flatpicker" id="addUkkStartTime" type="text"
                          placeholder="Pilih waktu mulai" name="start_time" data-language="id">
                        <div class="invalid-feedback"></div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="d-flex flex-column flatpicker-form">
                        <label class="form-label" for="endDate">Waktu Selesai<span
                            class="txt-danger">*</span></label>
                        <input class="form-control flatpicker" autocomplete="off" id="addUkkEndTime" type="text"
                          placeholder="Pilih waktu selesai" name="end_time" data-language="id">
                        <div class="invalid-feedback"></div>
                      </div>
                    </div>
                    <div class="col-12 shuffle-questions-section" style="display: none;">
                      <label class="form-label">Acak Soal</label>
                      <div class="checkbox-checked d-flex gap-2">
                        <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                          <input type="radio" value="0" checked name="is_shuffle_questions"
                            class="me-2 form-check-input radio" style="transform: translateY(-2px)">
                          <span class="fw-bold text-uppercase">Tidak</span>
                        </label>
                        <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                          <input type="radio" value="1" class="me-2 form-check-input radio"
                            name="is_shuffle_questions" style="transform: translateY(-2px)">
                          <span class="fw-bold text-uppercase">Ya</span>
                        </label>
                      </div>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <div class="taskFile">
                        <label class="form-label" for="addUkkFile">File (Opsional)</label>
                        <div class="info text-danger mb-1" style="font-size: 12px;">
                          Ukuran maksimal file 10mb
                        </div>
                        <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                          <label for="addUkkFile" class="d-flex align-items-center mb-0 w-100"
                            style="cursor:pointer;">
                            <span
                              style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                              <i class="fa fa-upload text-primary fs-5"></i>
                            </span>
                            <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                          </label>
                        </div>
                        <input type="file" class="form-control file_path" id="addUkkFile" name="file_path" hidden
                          accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png,.txt">
                        <div id="add-file-preview" class="d-flex flex-column gap-1 mt-2"></div>
                      </div>
                      <div class="invalid-feedback"></div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label" for="addUkkInstruction">Instruksi</label>
                  <div class="toolbar-box">
                    <div id="addUkkToolbar">
                      <select class="ql-header">
                        <option value="1"></option>
                        <option value="2"></option>
                        <option value="3"></option>
                        <option value="4"></option>
                        <option value="5"></option>
                        <option value="6"></option>
                        <option selected></option>
                      </select>
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
                    <div id="addUkkInstructionQuill"></div>
                    <input type="hidden" id="addUkkInstructionInput" name="instructions" class="quill">
                  </div>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-12 rubric-section" style="display: none;">
                  <hr>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-bold">Penilian Elemen Kompetensi</label>
                    <button type="button" class="btn btn-xs btn-primary add-rubric-row">
                      <i class="fa fa-plus me-1"></i> Tambah Elemen
                    </button>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered rubric-table">
                      <thead>
                        <tr>
                          <th style="width: 200px;">Kategori</th>
                          <th>Penilian Elemen Kompetensi</th>
                          <th style="width: 50px;"></th>
                        </tr>
                      </thead>
                      <tbody>
                        {{-- Rows will be added dynamically --}}
                      </tbody>
                    </table>
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
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editUkkModal" tabindex="-1" aria-labelledby="editUkkModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title">Edit UKK</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <form class="needs-validation" method="POST" action="" novalidate="" id="editUkkForm"
          enctype="multipart/form-data">
          <input type="hidden" name="deletedFile" id="editUkkDeletedFile" value="0">
          <div class="text-start">
            <div class="p-20">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label" for="editUkkTitle">Judul<span class="txt-danger">*</span></label>
                      <input class="form-control" id="editUkkTitle" type="text" placeholder="Tulis judul"
                        name="title">
                      <div class="invalid-feedback">
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="editUkkOperator">Operator<span
                          class="txt-danger">*</span></label>
                      <select class="form-select" id="editUkkOperator" name="operator_id">
                        <option value="">Pilih Operator</option>
                        @foreach ($operators as $op)
                          <option value="{{ $op->id }}">
                            {{ $op->name }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="editUkkType">Jenis UKK<span class="txt-danger">*</span></label>
                      <select class="form-select" id="editUkkType" name="type">
                        <option value="">Jenis UKK</option>
                        @foreach ($ukkTypes as $item)
                          <option value="{{ $item['value'] }}">
                            {{ $item['label'] }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="editUkkMajor">Jurusan<span class="txt-danger">*</span></label>
                      <select class="selectpicker search-picker" data-live-search="true" id="editUkkMajor"
                        name="major">
                        <option value="">Pilih Jurusan</option>
                        @foreach ($majors as $major)
                          <option value="{{ $major->name }}" class="text-uppercase">
                            {{ $major->name }}
                          </option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">
                      </div>
                    </div>
                    <div class="extra-fields-praktik" style="display: none;">
                      <div class="col-12">
                        <label class="form-label" for="editUkkCode">Kode</label>
                        <input class="form-control" id="editUkkCode" type="text" placeholder="Contoh: KM25.3.4.1"
                          name="code">
                        <div class="invalid-feedback"></div>
                      </div>
                      <div class="row g-3 mt-0">
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="editUkkPackage">Nomor Paket</label>
                          <input class="form-control" id="editUkkPackage" type="text" placeholder="Contoh: 4"
                            name="package_number">
                          <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="editUkkFormat">Bentuk Soal</label>
                          <input class="form-control" id="editUkkFormat" type="text"
                            placeholder="Contoh: Penugasan Perorangan" name="exam_format">
                          <div class="invalid-feedback"></div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="row g-3">

                    <div class="col-12">
                      <div class="d-flex flex-column flatpicker-form">
                        <label class="form-label" for="editUkkStartTime">Waktu Mulai<span
                            class="txt-danger">*</span></label>
                        <input class="form-control flatpicker" id="editUkkStartTime" type="text"
                          placeholder="Pilih waktu mulai" name="start_time" data-language="id">
                        <div class="invalid-feedback"></div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="d-flex flex-column flatpicker-form">
                        <label class="form-label" for="editUkkEndTime">Waktu Selesai<span
                            class="txt-danger">*</span></label>
                        <input class="form-control flatpicker" autocomplete="off" id="editUkkEndTime" type="text"
                          placeholder="Pilih waktu selesai" name="end_time" data-language="id">
                        <div class="invalid-feedback"></div>
                      </div>
                    </div>
                    <div class="col-12 shuffle-questions-section" style="display: none;">
                      <label class="form-label">Acak Soal</label>
                      <div class="checkbox-checked d-flex gap-2">
                        <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                          <input type="radio" value="0" checked name="is_shuffle_questions"
                            class="me-2 form-check-input radio" style="transform: translateY(-2px)">
                          <span class="fw-bold text-uppercase">Tidak</span>
                        </label>
                        <label class="d-flex align-items-center mb-0" style="align-self: flex-start">
                          <input type="radio" value="1" class="me-2 form-check-input radio"
                            name="is_shuffle_questions" style="transform: translateY(-2px)">
                          <span class="fw-bold text-uppercase">Ya</span>
                        </label>
                      </div>
                      <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-12">
                      <div class="taskFile">
                        <label class="form-label" for="editUkkFile">File (Opsional)</label>
                        <div class="info text-danger mb-1" style="font-size: 12px;">
                          Ukuran maksimal file 10mb
                        </div>
                        <div class="custom-file-upload w-100 border rounded-2 px-3 py-3">
                          <label for="editUkkFile" class="d-flex align-items-center mb-0 w-100"
                            style="cursor:pointer;">
                            <span
                              style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f0ff; border-radius:6px; margin-right:12px;">
                              <i class="fa fa-upload text-primary fs-5"></i>
                            </span>
                            <span style="color:#b0b0b0; font-weight:500;">Unggah File</span>
                          </label>
                        </div>
                        <input type="file" class="form-control file_path" id="editUkkFile" name="file_path"
                          hidden accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png,.txt">
                        <div id="edit-file-preview" class="d-flex flex-column gap-1 mt-2"></div>
                      </div>
                      <div class="invalid-feedback"></div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label" for="editUkkInstruction">Instruksi</label>
                  <div class="toolbar-box">
                    <div id="editUkkToolbar">
                      <select class="ql-header">
                        <option value="1"></option>
                        <option value="2"></option>
                        <option value="3"></option>
                        <option value="4"></option>
                        <option value="5"></option>
                        <option value="6"></option>
                        <option selected></option>
                      </select>
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
                    <div id="editUkkInstructionQuill"></div>
                    <input type="hidden" id="editUkkInstructionInput" name="instructions" class="quill">
                  </div>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-12 rubric-section" style="display: none;">
                  <hr>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-bold">Penilian Elemen Kompetensi</label>
                    <button type="button" class="btn btn-xs btn-primary add-rubric-row">
                      <i class="fa fa-plus me-1"></i> Tambah Elemen
                    </button>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered rubric-table">
                      <thead>
                        <tr>
                          <th style="width: 200px;">Kategori</th>
                          <th>Penilian Elemen Kompetensi</th>
                          <th style="width: 50px;"></th>
                        </tr>
                      </thead>
                      <tbody>
                        {{-- Rows will be added dynamically --}}
                      </tbody>
                    </table>
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
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
