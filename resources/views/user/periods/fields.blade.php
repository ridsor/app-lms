<input type="hidden" id="periodId" name="period_id">
<input type="hidden" name="_method" id="methodField" value="POST">
<div class="col-12">
    <label class="form-label" for="semester">Semester<span> <span class="font-danger">*</span></span></label>
    <select class="form-control" id="semester" name="semester" required>
        <option value="" selected>Pilih Semester</option>
        <option value="odd">Ganjil</option>
        <option value="even">Genap</option>
    </select>
    <div class="invalid-feedback">
    </div>
</div>
<div class="col-12">
    <label class="form-label" for="academicYear">Tahun Akademik<span> <span class="font-danger">*</span></span></label>
    <input class="form-control" aria-invalid="false" type="text" id="academicYear" name="academic_year"
        placeholder="Contoh: 2024/2025">
    <div class="invalid-feedback">
    </div>
</div>
<div>
    <label class="col-form-label text-end pt-0">Periode:<span class="font-danger">*</span></label>
    <div class="row g-2">
        <div class="col-sm-6">
            <input class="datepicker-here-minmax form-control digits" type="text" data-language="id" id="startDate"
                name="start_date" placeholder="Tanggal Mulai" autocomplete="off">
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-sm-6">
            <input class="datepicker-here-minmax form-control digits" type="text" data-language="id" id="endDate"
                name="end_date" placeholder="Tanggal Selesai" autocomplete="off">
            <div class="invalid-feedback">
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">Batal</button>
</div>
