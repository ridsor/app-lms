<input type="hidden" id="roomId" name="room_id">
<input type="hidden" name="_method" id="methodField" value="POST">
<div class="col-12">
    <label class="form-label" for="roomName">Nama<span> <span class="font-danger">*</span></span></label>
    <input class="form-control" aria-invalid="false" type="text" id="roomName" name="name"
        placeholder="Masukan nama ruangan">
    <div class="invalid-feedback">
        <span class="text-danger" id="nameError"></span>
    </div>
</div>
<div class="col-12">
    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">Batal</button>
</div>
