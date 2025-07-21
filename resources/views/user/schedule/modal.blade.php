{{-- Add --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jadwal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="addScheduleForm">
              <input type="hidden" name="class_id" value="{{ $class->id }}">
              <div class="col-lg-6">
                <label class="form-label" for="addScheduleCurriculum">Kurikulum</label>
                <select class="form-select" @if (!($activeCurriculum->count() > 0)) disabled @endif id="addScheduleCurriculum"
                  name="curriculum_id">
                  @if ($activeCurriculum->count() > 0)
                    @foreach ($activeCurriculum as $curriculum)
                      <option value="{{ $curriculum->id }}">{{ $curriculum->name }}</option>
                    @endforeach
                  @else
                    <option value="">Tidak ada kurikulum aktif</option>
                  @endif
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="addScheduleSubject">Mata Pelajaran<span
                    class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="addScheduleSubject"
                  name="subject_id">
                  <option value="">Pilih Mata Pelajaran</option>
                  @foreach ($activeCurriculum[0]->subjects ?? [] as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="scheduleTeacher">Pengajar<span class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="scheduleTeacher"
                  name="teacher_id">
                  <option value="">Pilih Guru</option>
                  @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }} - {{ $teacher->specialization }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="scheduleRoom">Ruangan<span class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="scheduleRoom" name="room_id">
                  <option value="">Pilih Ruangan</option>
                  @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="scheduleDay">Hari<span class="txt-danger">*</span></label>
                <select class="form-select" id="scheduleDay" name="day">
                  <option value="">Pilih Hari</option>
                  @foreach ($days as $day)
                    <option value="{{ $day['value'] }}">{{ $day['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-3">
                <label class="form-label" for="scheduleStart">Jam Mulai<span class="txt-danger">*</span></label>
                <input class="form-control flatpickr-input twenty-four-hour" value="00:00" id="scheduleStart"
                  name="start_time" type="time" autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-3">
                <label class="form-label" for="scheduleEnd">Jam Selesai<span class="txt-danger">*</span></label>
                <input class="form-control flatpickr-input twenty-four-hour" value="00:00" id="scheduleEnd"
                  name="end_time" type="time" autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="scheduleMethod">Metode Pertemuan</label>
                <select class="form-select" id="scheduleMethod" name="meeting_method">
                  @foreach ($meetingMethods as $method)
                    <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit" id="addScheduleSubmitBtn">Tambah +</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Edit --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModal"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title">Edit Jadwal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <form class="row g-3 needs-validation" novalidate="" id="editScheduleForm">
              <input type="hidden" name="class_id" value="{{ $class->id }}">
              <input type="hidden" name="schedule_id">
              <input type="hidden" name="schedule_time_id">
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleCurriculum">Kurikulum<span
                    class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="editScheduleCurriculum"
                  name="curriculum_id">
                  <option value="">Pilih Kurikulum</option>
                  @foreach ($activeCurriculum as $curriculum)
                    <option value="{{ $curriculum->id }}">{{ $curriculum->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleSubject">Mata Pelajaran<span
                    class="txt-danger">*</span></label>
                <select disabled class="selectpicker search-picker" data-live-search="true" id="editScheduleSubject"
                  name="subject_id">
                  <option value="">Pilih Mata Pelajaran</option>
                </select>
                <div class="invalid-feedback">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleTeacher">Pengajar<span class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="editScheduleTeacher"
                  name="teacher_id">
                  <option value="">Pilih Guru</option>
                  @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                      {{ $teacher->name }}{{ $teacher->specialization ? ' - ' . $teacher->specialization : '' }}
                    </option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleRoom">Ruangan<span class="txt-danger">*</span></label>
                <select class="selectpicker search-picker" data-live-search="true" id="editScheduleRoom"
                  name="room_id">
                  <option value="">Pilih Ruangan</option>
                  @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleDay">Hari<span class="txt-danger">*</span></label>
                <select class="form-select" id="editScheduleDay" name="day">
                  <option value="">Pilih Hari</option>
                  @foreach ($days as $day)
                    <option value="{{ $day['value'] }}">{{ $day['label'] }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-3">
                <label class="form-label" for="editScheduleStart">Jam Mulai<span class="txt-danger">*</span></label>
                <input class="form-control flatpickr-input twenty-four-hour" value="00:00" id="editScheduleStart"
                  name="start_time" type="time" autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-3">
                <label class="form-label" for="editScheduleEnd">Jam Selesai<span class="txt-danger">*</span></label>
                <input class="form-control flatpickr-input twenty-four-hour" value="00:00" id="editScheduleEnd"
                  name="end_time" type="time" autocomplete="off">
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label" for="editScheduleMethod">Metode Pertemuan</label>
                <select class="form-select" id="editScheduleMethod" name="meeting_method">
                  @foreach ($meetingMethods as $method)
                    <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit" id="editScheduleSubmitBtn">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- View -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-labelledby="viewScheduleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content category-popup">
      <div class="modal-header">
        <h5 class="modal-title" id="viewScheduleModalLabel">Detail Jadwal</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 custom-input">
        <div class="text-start">
          <div class="p-20">
            <div class="row g-3">
              <div class="col-lg-6">
                <label class="form-label">Mata Pelajaran</label>
                <div class="form-control-plaintext" id="viewScheduleSubject">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Pengajar</label>
                <div class="form-control-plaintext" id="viewScheduleTeacher"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Ruangan</label>
                <div class="form-control-plaintext" id="viewScheduleRoom">
                </div>
              </div>
              @if ($hasMajors)
                <div class="col-lg-6">
                  <label class="form-label">Jurusan</label>
                  <div class="form-control-plaintext" id="viewScheduleMajor">
                  </div>
                </div>
              @endif
              <div class="col-lg-6">
                <label class="form-label">Kelas</label>
                <div class="form-control-plaintext" id="viewScheduleClass">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Hari</label>
                <div class="form-control-plaintext" id="viewScheduleDay"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Jam Mulai</label>
                <div class="form-control-plaintext" id="viewScheduleStartTime"></div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Jam Selesai</label>
                <div class="form-control-plaintext" id="viewScheduleEndTime">
                </div>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Metode Pertemuan</label>
                <div class="form-control-plaintext" id="viewScheduleMethod">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
