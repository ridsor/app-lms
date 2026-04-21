@extends('layouts.user.app')

@section('title', 'Evaluasi UKK Praktik')

@section('main_content')
<div class="container-fluid p-0">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-6">
        <h3>Evaluasi UKK Praktik</h3>
      </div>
      <div class="col-sm-6 text-end">
        <a href="{{ route('user.ukk.result.praktik', $ukk->id) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Hasil Pekerjaan Siswa --}}
    <div class="col-xl-8 col-lg-7">
      <div class="card rounded-responsive">
        <div class="card-header pb-0">
          <h5>Pekerjaan: {{ $practice_result->student->name }} ({{ $practice_result->student->nis }})</h5>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <label class="form-label fw-bold">Deskripsi / Catatan Siswa:</label>
            <p class="border p-3 rounded bg-light">{{ $practice_result->contents['description'] ?: 'Tidak ada catatan.' }}</p>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold">Tautan (Links):</label>
            <ul class="list-group">
              @forelse($practice_result->contents['links'] ?? [] as $link)
                @if($link)
                  <li class="list-group-item">
                    <i class="fa fa-external-link me-2"></i>
                    <a href="{{ $link }}" target="_blank">{{ $link }}</a>
                  </li>
                @endif
              @empty
                <li class="list-group-item text-muted">Tidak ada tautan.</li>
              @endforelse
            </ul>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold">File Terlampir:</label>
            <div class="row g-2">
              @forelse($practice_result->contents['files'] ?? [] as $file)
                <div class="col-md-6">
                  <div class="border p-2 rounded d-flex align-items-center justify-content-between">
                    <div class="text-truncate">
                      <i class="fa fa-file-o me-2"></i> {{ $file['name'] }}
                    </div>
                    <a href="{{ Storage::url($file['path']) }}" class="btn btn-xs btn-outline-info" target="_blank">Lihat</a>
                  </div>
                </div>
              @empty
                <div class="col-12 text-muted">Tidak ada file.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Form Penilaian --}}
    <div class="col-xl-4 col-lg-5">
      <div class="card rounded-responsive">
        <div class="card-header pb-0">
          <h5>Form Penilaian</h5>
        </div>
        <div class="card-body">
          <form id="evaluation-form" action="{{ route('user.ukk.praktik.updateScore', $practice_result->id) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Skor (0 - 100)</label>
              <input type="number" name="score" class="form-control" value="{{ $practice_result->score }}" min="0" max="100" step="0.1" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Feedback / Catatan Penilai</label>
              <textarea name="feedback" class="form-control" rows="5" placeholder="Berikan feedback untuk siswa...">{{ $practice_result->feedback }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Simpan Nilai</button>
          </form>
          
          @if($practice_result->graded_at)
            <div class="mt-3 text-center">
              <small class="text-muted">Dinilai oleh: {{ $practice_result->grader->name }}<br>pada {{ $practice_result->graded_at->translatedFormat('d M Y H:i') }}</small>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
<script>
  $(document).ready(function() {
    $('#evaluation-form').submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(res) {
          swal("Berhasil!", res.message, "success");
        },
        error: function(err) {
          swal("Gagal!", "Terjadi kesalahan saat menyimpan nilai.", "error");
        }
      });
    });
  });
</script>
@endsection
