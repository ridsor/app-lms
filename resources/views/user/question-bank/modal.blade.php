<div class="modal fade" id="addQuestionBankModal" tabindex="-1" aria-labelledby="addQuestionBankModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldashboard">Tambah Bank Soal</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="row g-3 needs-validation" novalidate="" id="addQuestionBankForm">
                            <div class="col-lg-12">
                                <label class="form-label" for="addTitle">Judul<span class="txt-danger">*</span>
                                </label>
                                <input class="form-control" id="addTitle" type="text" placeholder="Masukan judul"
                                    name="title">
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label" for="addSubject">Mata Pelajaran<span
                                        class="txt-danger">*</span></label>
                                <select
                                    class="selectpicker search-picker" data-live-search="true" id="addSubject"
                                    name="subject_id">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->name }} - {{ $subject->curriculum->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="addDescription">Deskripsi<span
                                        class="txt-danger">*</span></label>
                                <textarea class="form-control" rows="3" name="description"></textarea>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
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

<div class="modal fade" id="editQuestionBankModal" tabindex="-1" aria-labelledby="editQuestionBankModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content category-popup">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldashboard">Edit Bank Soal</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 custom-input">
                <div class="text-start">
                    <div class="p-20">
                        <form class="row g-3 needs-validation" novalidate="" id="editQuestionBankForm">
                            <div class="col-lg-12">
                                <label class="form-label" for="editTitle">Judul<span class="txt-danger">*</span>
                                </label>
                                <input class="form-control" id="editTitle" type="text" placeholder="Masukan judul"
                                    name="title">
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label" for="editSubject">Mata Pelajaran<span
                                        class="txt-danger">*</span></label>
                                <select @if ($hasMajors) disabled @endif
                                    class="selectpicker search-picker" data-live-search="true" id="editSubject"
                                    name="subject_id">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->name }} - {{ $subject->curriculum->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="editDescription">Deskripsi<span
                                        class="txt-danger">*</span></label>
                                <textarea class="form-control" rows="3" name="description"></textarea>
                                <div class="invalid-feedback">
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit" id="editStudentSubmitBtn">Tambah
                                    +</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
