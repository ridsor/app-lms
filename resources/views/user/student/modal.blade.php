<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="modaldashboard">Tambah Siswa</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="addStudentForm">
              <div class="col-lg-6">
                <label class="form-label" for="studentName">Nama<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="studentName" type="text" placeholder="Masukan nama siswa"
                  name="name">
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentNisn">NISN<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="studentNisn" type="text" placeholder="Masukan NISN" name="nisn">
                <div class="invalid-feedback">
                </div>
              </div>
              @if ($hasMajors)
                <div class="col-lg-6">
                  <label class="form-label" for="studentMajor">Jurusan</label>
                  <select class="form-select" id="studentMajor" name="major_id">
                    <option value="">Pilih Jurusan</option>
                    @foreach ($majors as $major)
                      <option value="{{ $major->id }}">
                        {{ $major->name }}</option>
                    @endforeach
                  </select>
                </div>
              @endif
              <div class="col-lg-6">
                <label class="form-label" for="studentClass">Kelas</label>
                <select @if ($hasMajors) disabled @endif class="selectpicker search-picker"
                  data-live-search="true" id="studentClass" name="class_id">
                  <option value="">Pilih Kelas</option>
                  @foreach ($classes as $class)
                    <option value="{{ $class->id }}">
                      {{ $class->name }}{{ $class->level }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6 d-none">
                <label class="form-label" for="studentHomeroomTeacher">Wali Kelas</label>
                <select class="selectpicker search-picker" data-live-search="true" id="studentHomeroomTeacher"
                  name="homeroom_teacher_id" @cannot('student.*') disabled @endcannot>
                  <option value="">Pilih Wali Kelas</option>
                  @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                      {{ $teacher->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentBirthplace">Tempat
                  Lahir<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="studentBirthplace" type="text" placeholder="Masukan tempat lahir"
                  name="birthplace">
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentDateOfBirth">Tanggal
                  Lahir<span class="txt-danger">*</span>
                </label>
                <input class="form-control datepicker-here" autocomplete="off" id="studentDateOfBirth" type="text"
                  name="date_of_birth" placeholder="dd/mm/yyyy" data-language="id">
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentGender">Jenis
                  Kelamin<span class="txt-danger">*</span>
                </label>
                <select class="form-select" id="studentGender" name="gender">
                  <option value="">Pilih Jenis Kelamin</option>
                  @foreach ($genders as $gender)
                    <option value="{{ $gender['value'] }}">
                      {{ $gender['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentReligion">Agama<span class="txt-danger">*</span>
                </label>
                <select class="form-select" id="studentReligion" name="religion">
                  <option value="">Pilih Agama</option>
                  @foreach ($religions as $religion)
                    <option value="{{ $religion }}">
                      {{ $religion }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="studentStatus">Status
                </label>
                <select class="form-select" id="studentStatus" name="status">
                  @foreach ($statuses as $status)
                    <option value="{{ $status['value'] }}">
                      {{ $status['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>

              <div class="col-md-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit" id="addStudentSubmitBtn">Tambah +</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="modaldashboard">Edit Siswa</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="editStudentForm">
              <div class="col-lg-6">
                <label class="form-label" for="editStudentName">Nama<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="editStudentName" type="text" placeholder="Masukan nama siswa"
                  name="name">
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editStudentNisn">NISN<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="editStudentNisn" type="text" placeholder="Masukan NISN"
                  name="nisn">
                <div class="invalid-feedback">
                </div>
              </div>
              @if ($hasMajors)
                <div class="col-lg-6">
                  <label class="form-label" for="editStudentMajor">Jurusan</label>
                  <select class="form-select" id="editStudentMajor" name="major_id"
                    @cannot('student.*') disabled @endcannot>
                    <option value="">Pilih Jurusan</option>
                    @foreach ($majors as $major)
                      <option value="{{ $major->id }}">
                        {{ $major->name }}</option>
                    @endforeach
                  </select>
                </div>
              @endif
              <div class="col-lg-6">
                <label class="form-label" for="editStudentClass">Kelas<span class="txt-danger">*</span></label>
                <select @if ($hasMajors) disabled @endif class="selectpicker search-picker"
                  data-live-search="true" id="editStudentClass" name="class_id">
                  <option value="">Pilih Kelas</option>
                  @foreach ($classes as $class)
                    <option value="{{ $class->id }}">
                      {{ $class->name }}{{ $class->level }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6 d-none">
                <label class="form-label" for="editStudentHomeroomTeacher">Wali Kelas</label>
                <select class="selectpicker search-picker" data-live-search="true" id="editStudentHomeroomTeacher"
                  name="homeroom_teacher_id" @cannot('student.*') disabled @endcannot>
                  <option value="">Pilih Wali Kelas</option>
                  @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                      {{ $teacher->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editStudentBirthplace">Tempat
                  Lahir<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="editStudentBirthplace" type="text"
                  placeholder="Masukan tempat lahir" name="birthplace">
                <div class="invalid-feedback">
                </div>
              </div>

              <div class="col-lg-6">
                <label class="form-label" for="editStudentDateOfBirth">Tanggal
                  Lahir<span class="txt-danger">*</span>
                </label>
                <input class="form-control" id="editStudentDateOfBirth" type="text" name="date_of_birth">
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editStudentGender">Jenis
                  Kelamin<span class="txt-danger">*</span>
                </label>
                <select class="form-select" id="editStudentGender" name="gender">
                  @foreach ($genders as $gender)
                    <option value="{{ $gender['value'] }}">
                      {{ $gender['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editStudentReligion">Agama<span class="txt-danger">*</span>
                </label>
                <select class="form-select" id="editStudentReligion" name="religion">
                  @foreach ($religions as $religion)
                    <option value="{{ $religion }}">
                      {{ $religion }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editStudentStatus">Status
                </label>
                <select class="form-select" id="editStudentStatus" name="status">
                  @foreach ($statuses as $status)
                    <option value="{{ $status['value'] }}">
                      {{ $status['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>

              <div class="col-md-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit" id="editStudentSubmitBtn">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal View Student -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="viewStudentModalLabel">Detail Siswa</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <div class="row g-3">
              <div class="col-lg-6">
                <label class="form-label">Nama</label>
                <div class="form-control-plaintext" id="viewStudentName">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">NISN</label>
                <div class="form-control-plaintext" id="viewStudentNisn">
                </div>
              </div>
              @if ($hasMajors)
                <div class="col-lg-6">
                  <label class="form-label">Jurusan</label>
                  <div class="form-control-plaintext" id="viewStudentMajor">
                  </div>
                </div>
              @endif
              <div class="col-lg-6">
                <label class="form-label">Kelas</label>
                <div class="form-control-plaintext" id="viewStudentClass">
                </div>
              </div>
              <div class="col-lg-6 d-none">
                <label class="form-label">Wali Kelas</label>
                <div class="form-control-plaintext" id="viewStudentHomeroomTeacher">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Tempat Lahir</label>
                <div class="form-control-plaintext" id="viewStudentBirthplace"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Tanggal Lahir</label>
                <div class="form-control-plaintext" id="viewStudentDateOfBirth"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Jenis Kelamin</label>
                <div class="form-control-plaintext" id="viewStudentGender">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Agama</label>
                <div class="form-control-plaintext" id="viewStudentReligion">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Status</label>
                <div class="form-control-plaintext" id="viewStudentStatus">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Waktu Dibuat</label>
                <div class="form-control-plaintext" id="viewStudentCreatedAt">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Bulk Edit -->
<div class="modal fade" id="bulkEditStudentModal" tabindex="-1" aria-labelledby="bulkEditStudentModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkEditStudentModalLabel">Edit Massal Siswa</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <div class="mb-3">
              <strong>Catatan:</strong>
              <ul>
                <li><span class="text-danger">Biarkan data kosong jika tidak ingin mengubah data tersebut.
                  </span></li>
                <li><span class="text-danger">Data yang diubah hanya akan berpengaruh pada siswa yang
                    dipilih.</span></li>
              </ul>
            </div>
            <form class="row g-3" id="bulkEditStudentForm">
              @can('student.*')
                <div class="col-12 d-none">
                  <label class="form-label" for="bulkEditStudentHomeroomTeacher">Wali Kelas</label>
                  <select class="selectpicker search-picker" data-live-search="true"
                    id="bulkEditStudentHomeroomTeacher" name="homeroom_teacher_id">
                    <option value="">Pilih Wali Kelas</option>
                    <option value="nothing">Tidak Ada</option>
                    @foreach ($teachers as $teacher)
                      <option value="{{ $teacher->id }}">
                        {{ $teacher->name }}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">
                  </div>
                </div>
              @endcan
              <div class="col-12">
                <label class="form-label" for="bulkEditStudentStatus">Status</label>
                <select class="form-select" id="bulkEditStudentStatus" name="status">
                  <option value="">Pilih Status</option>
                  @foreach ($statuses as $status)
                    <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              @if ($hasMajors)
                <div class="col-12">
                  <label class="form-label" for="bulkEditStudentMajor">Jurusan</label>
                  <select class="form-select" id="bulkEditStudentMajor" name="major_id">
                    <option value="">Pilih Jurusan</option>
                    @foreach ($majors as $major)
                      <option value="{{ $major->id }}">
                        {{ $major->name }}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback">
                  </div>
                </div>
              @endif
              <div class="col-12">
                <label class="form-label" for="bulkEditStudentClass">Kelas</label>
                <select class="selectpicker search-picker" data-live-search="true" id="bulkEditStudentClass"
                  name="class_id">
                  <option value="">Pilih Kelas</option>
                  <option value="nothing">Tidak Ada</option>
                </select>
                <div class="invalid-feedback">
                </div>
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit" id="bulkEditStudentSubmitBtn">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
