@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'UKK Praktik')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <style>
    .view_file_path .Archive, .view_file_path .Link { background: #f5f6f9 !important; }
    .dark-only .view_file_path .Archive, .dark-only .view_file_path .Link { background: #1d1e26 !important; }
  </style>
@endsection

@section('main_content')
<div class="container-fluid p-0">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-6">
        <h3>Uji Kompetensi Keahlian (Praktik)</h3>
      </div>
      <div class="col-sm-6 text-end">
        <a href="{{ route('user.ukk.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Informasi UKK --}}
    <div class="col-xl-8 col-lg-7">
      <div class="card rounded-responsive">
        <div class="card-body">
          <h5>{{ $ukk->title }}</h5>
          <div class="mt-3">
            <label class="form-label fw-bold">Instruksi:</label>
            <div class="ql-editor p-0">{!! $ukk->instructions !!}</div>
          </div>

          @if($ukk->file_path)
          <div class="mt-4">
            <label class="form-label fw-bold">File Referensi / Soal:</label>
            <div class="Archive p-3 rounded d-flex align-items-center justify-content-between bg-light">
              <div class="d-flex align-items-center">
                <i class="fa fa-file-pdf-o fs-4 me-3 text-danger"></i>
                <div>
                  <div class="fw-bold">{{ $ukk->file_name }}</div>
                  <small>{{ number_format($ukk->file_size / 1024, 2) }} KB</small>
                </div>
              </div>
              <a href="{{ route('user.ukk.file.download', $ukk->id) }}" class="btn btn-primary btn-sm">Unduh</a>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Form Pengumpulan --}}
    <div class="col-xl-4 col-lg-5">
      <div class="card rounded-responsive">
        <div class="card-header pb-0">
          <h5>Status Pengumpulan</h5>
        </div>
        <div class="card-body">
          @if($practice_result)
            <div class="alert alert-light-success text-success mb-3">
              <i class="fa fa-check-circle me-2"></i> Sudah Dikumpulkan pada {{ $practice_result->submitted_at->translatedFormat('d M Y H:i') }}
            </div>
            
            @if($practice_result->graded_at)
              <div class="card border mb-3">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Nilai:</span>
                    <span class="badge badge-primary fs-6">{{ $practice_result->formatted_score }}</span>
                  </div>
                  @if($practice_result->feedback)
                    <div class="mt-2">
                      <small class="text-muted d-block">Feedback:</small>
                      <p class="mb-0">{{ $practice_result->feedback }}</p>
                    </div>
                  @endif
                </div>
              </div>
            @else
              <div class="alert alert-light-warning text-warning mb-3">
                <i class="fa fa-clock-o me-2"></i> Menunggu Penilaian Operator
              </div>
            @endif
          @endif

          @role('student')
            @php
              $now = now();
              $can_submit = $now >= $ukk->start_time && $now <= $ukk->end_time;
            @endphp

            @if($can_submit)
              <form id="practice-submit-form" action="{{ route('user.ukk.praktik.submit', $ukk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Deskripsi / Catatan (Opsional)</label>
                  <textarea name="description" class="form-control" rows="3">{{ $practice_result->contents['description'] ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Tautan Pekerjaan (misal: GitHub, Drive)</label>
                  <div id="links-container">
                    @php $links = $practice_result->contents['links'] ?? ['']; @endphp
                    @foreach($links as $link)
                      <div class="input-group mb-2">
                        <input type="url" name="links[]" class="form-control" placeholder="https://" value="{{ $link }}">
                        <button type="button" class="btn btn-outline-danger remove-link"><i class="fa fa-times"></i></button>
                      </div>
                    @endforeach
                  </div>
                  <button type="button" class="btn btn-xs btn-outline-primary" id="add-link">+ Tambah Link</button>
                </div>

                <div class="mb-3">
                  <label class="form-label">Unggah File (Max 10MB)</label>
                  <input type="file" name="files[]" class="form-control" multiple>
                  @if($practice_result && !empty($practice_result->contents['files']))
                    <div class="mt-2">
                      <small class="text-muted">File sebelumnya:</small>
                      <ul class="list-unstyled">
                        @foreach($practice_result->contents['files'] as $file)
                          <li><i class="fa fa-paperclip me-1"></i> {{ $file['name'] }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                </div>

                <button type="submit" class="btn btn-primary w-100">Kumpulkan</button>
              </form>
            @else
              <div class="alert alert-danger mb-0">
                <i class="fa fa-exclamation-triangle me-2"></i> 
                @if($now < $ukk->start_time)
                  Pengumpulan belum dibuka.
                @else
                  Waktu pengumpulan sudah berakhir.
                @endif
              </div>
            @endif
          @endrole
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
    $('#add-link').click(function() {
      $('#links-container').append(`
        <div class="input-group mb-2">
          <input type="url" name="links[]" class="form-control" placeholder="https://">
          <button type="button" class="btn btn-outline-danger remove-link"><i class="fa fa-times"></i></button>
        </div>
      `);
    });

    $(document).on('click', '.remove-link', function() {
      $(this).closest('.input-group').remove();
    });

    $('#practice-submit-form').submit(function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      
      swal({
        title: "Konfirmasi",
        text: "Kumpulkan hasil praktik sekarang?",
        icon: "warning",
        buttons: true,
        dangerMode: false,
      }).then((willSubmit) => {
        if (willSubmit) {
          $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
              swal("Berhasil!", res.message, "success").then(() => location.reload());
            },
            error: function(err) {
              swal("Gagal!", err.responseJSON.message || "Terjadi kesalahan", "error");
            }
          });
        }
      });
    });
  });
</script>
@endsection
