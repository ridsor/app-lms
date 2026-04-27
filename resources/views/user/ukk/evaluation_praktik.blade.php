@php
  use App\Helpers\Helper;
@endphp

@extends('layouts.user.app')

@section('title', 'Evaluasi UKK Praktik')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    /* Memaksa semua parent untuk mengizinkan sticky */
    .page-wrapper,
    .page-body-wrapper,
    .page-body,
    .container-fluid,
    .e-category {
      overflow: visible !important;
    }

    .preview-card {
      transition: transform 0.2s;
      border: 1px solid #e6edef;
      background: #fff;
      width: 100%;
    }

    .preview-card:hover {
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .instruction-box {
      max-height: 300px;
      overflow-y: auto;
      font-size: 0.85rem;
      background: #f9f9f9;
      padding: 10px;
      border-radius: 5px;
    }

    .map-container {
      height: 500px;
      width: 100%;
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Evaluasi</h3>
        </div>
        <div class="col-sm-6 text-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <i data-feather="home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.ukk.index') }}">Uji Kompetensi Keahlian</a></li>
            <li class="breadcrumb-item active">Evaluasi</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="container-fluid e-category p-0">
      <div class="mb-3 px-3">
        <div class="d-flex justify-content-between align-items-center gap-2">
          <a {{ $practice_results->onFirstPage() ? 'aria-disabled="true"' : '' }} role="button"
            {{ $practice_results->onFirstPage() ? '' : 'href=' . route('user.ukk.praktik.evaluation', ['id' => $ukk->id, 'page' => $practice_results->currentPage() - 1]) }}
            class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ $practice_results->onFirstPage() ? 'disabled' : '' }}">
            <i data-feather="chevron-left" style="width:18px; height: 18px"></i>
          </a>
          <div class="d-flex flex-column align-items-center justify-content-center px-2">
            <p class="mb-0 fw-medium text-break">
              {{ $practice_result->student->name }}
            </p>
            <p class="f-light mb-0 text-break">{{ $practice_result->student->nis }}</p>
          </div>
          <a {{ $practice_results->hasMorePages() ? 'href=' . route('user.ukk.praktik.evaluation', ['id' => $ukk->id, 'page' => $practice_results->currentPage() + 1]) : '' }}
            role="button" {{ !$practice_results->hasMorePages() ? 'aria-disabled="true"' : '' }}
            class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ !$practice_results->hasMorePages() ? 'disabled' : '' }}">
            <i data-feather="chevron-right" style="width:18px; height: 18px"></i>
          </a>
        </div>
      </div>

      {{-- Row default (stretch) agar col-lg-4 setinggi col-lg-8 --}}
      <div class="row">
        {{-- Sisi Kiri: Jawaban Siswa & Previews (Area Luas) --}}
        <div class="col-lg-8">
          <div class="card rounded-responsive mb-4">
            <div class="card-header pb-0 d-flex justify-content-between bg-white border-bottom-0">
              <h5>Hasil Pekerjaan Siswa</h5>
              <span class="text-muted small">Dikumpulkan:
                {{ $practice_result->submitted_at->translatedFormat('d M Y, H:i') }} WIT</span>
            </div>
            <div class="card-body">
              {{-- Deskripsi Siswa --}}
              @if ($practice_result->contents['description'])
                <div class="alert alert-light-primary border-0 mb-4">
                  <h6 class="alert-heading fw-bold small text-uppercase">Catatan Siswa:</h6>
                  <p class="mb-0 italic text-dark">
                    {{ $practice_result->contents['description'] }}</p>
                </div>
              @endif

              <div class="d-flex flex-column gap-4">
                {{-- File & Previews --}}
                @foreach ($practice_result->contents['files'] ?? [] as $file)
                  @php
                    $fileUrl = URL::temporarySignedRoute('user.ukk.praktik.file.get', now()->addMinutes(60), [
                        'result_id' => $practice_result->id,
                        'filename' => $file['name'],
                    ]);
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogg']);
                    $isPdf = $ext === 'pdf';
                    $isOffice = in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                  @endphp

                  <div class="preview-card rounded-3 overflow-hidden shadow-sm">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                      <div class="text-truncate">
                        <i class="{{ Helper::getContentIcon($file['name']) }} me-2 fs-5"></i>
                        <span class="fw-medium text-dark">{{ $file['name'] }}</span>
                        <small class="text-muted ms-2">({{ number_format($file['size'] / (1024 * 1024), 2) }} MB)</small>
                      </div>
                      <a href="{{ $fileUrl }}" target="_blank" class="btn btn-xs btn-primary">
                        <i class="fa fa-external-link"></i>
                      </a>
                    </div>

                    <div class="bg-dark d-flex align-items-center justify-content-center" style="min-height: 300px;">
                      @if ($isImage)
                        <img src="{{ $fileUrl }}" class="img-fluid"
                          style="max-height: 1000px; object-fit: contain;">
                      @elseif($isVideo)
                        <video controls class="w-100" style="max-height: 600px;">
                          <source src="{{ $fileUrl }}"
                            type="video/{{ $ext == 'mp4' ? 'mp4' : ($ext == 'webm' ? 'webm' : 'ogg') }}">
                        </video>
                      @elseif($isPdf)
                        <iframe src="{{ $fileUrl }}" width="100%" height="800px" frameborder="0"></iframe>
                      @elseif($isOffice)
                        <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true"
                          width="100%" height="700px" frameborder="0"></iframe>
                      @elseif(in_array($ext, ['kml', 'gpx', 'geojson']))
                        <div class="position-relative w-100">
                          <div id="map-{{ $loop->index }}" class="map-container" data-url="{!! $fileUrl !!}"
                            data-type="{{ $ext }}"></div>
                          <div id="loader-{{ $loop->index }}"
                            class="position-absolute top-50 start-50 translate-middle text-white text-center">
                            <div class="spinner-border" role="status"></div>
                            <p class="mt-2">Memuat data peta...</p>
                          </div>
                        </div>
                      @else
                        <div class="py-5 text-white-50 text-center">
                          <i class="fa fa-file-text-o fs-1 mb-3"></i>
                          <p>Pratinjau tidak tersedia untuk format ini.</p>
                          <a href="{{ $fileUrl }}" class="btn btn-sm btn-light">Unduh File</a>
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach

                {{-- Tautan (Links) --}}
                @foreach ($practice_result->contents['links'] ?? [] as $link)
                  @if ($link)
                    <div class="preview-card rounded-3 p-3 d-flex align-items-center justify-content-between shadow-sm">
                      <div class="d-flex align-items-center text-truncate">
                        <div class="bg-light-info text-info p-2 rounded-2 me-3">
                          <i class="fa fa-link fs-5"></i>
                        </div>
                        <div class="text-truncate">
                          <small class="text-muted d-block">Tautan Eksternal:</small>
                          <a href="{{ $link }}" target="_blank"
                            class="text-primary fw-medium">{{ $link }}</a>
                        </div>
                      </div>
                      <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-primary ms-3">Buka</a>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>

        {{-- Sisi Kanan: Form Penilaian & Info UKK (STICKY AREA) --}}
        <div class="col-lg-4">
          <div>
            {{-- Card Penilaian --}}
            <div class="card rounded-responsive mb-3 shadow border-primary">
              <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">Penilaian</h5>
              </div>
              <div class="card-body">
                <form method="POST" id="form_ukk_evaluation" data-result-id="{{ $practice_result->id }}">
                  @csrf
                  <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Nilai (0-100)</label>
                    <div class="input-group">
                      <input class="form-control form-control-lg text-center fw-bold text-primary" type="number"
                        value="{{ $practice_result->score }}" name="score" step="0.1" min="0"
                        max="100" placeholder="0.0" />
                      <span class="input-group-text bg-primary-subtle text-primary fw-bold">/ 100</span>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Feedback untuk Siswa</label>
                    <textarea class="form-control" rows="5" name="feedback" placeholder="Tulis masukan di sini...">{{ $practice_result->feedback }}</textarea>
                  </div>

                  <button class="btn btn-primary btn-lg w-100 mb-2" type="submit" id="btn-save-score">
                    <i class="fa fa-save me-2"></i> Simpan
                  </button>
                  <a href="{{ route('user.ukk.result.praktik', $ukk->id) }}"
                    class="btn btn-outline-secondary w-100">Kembali</a>
                </form>
              </div>
            </div>

            {{-- Card Info UKK --}}
            <div class="card rounded-responsive shadow-sm">
              <div class="card-header py-2 bg-light" style="border-radius: 0">
                <h6 class="mb-0">Informasi</h6>
              </div>
              <div class="card-body p-3">
                <div class="mb-2">
                  <small class="text-muted d-block">Judul:</small>
                  <span class="fw-medium small">{{ $ukk->title }}</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block">Instruksi:</small>
                  <div class="instruction-box ql-editor p-0 small">
                    {!! $ukk->instructions !!}
                  </div>
                </div>
                @if ($ukk->file_path)
                  <a href="{{ route('user.ukk.file.download', $ukk->id) }}" class="btn btn-outline-secondary w-100">
                    <i class="fa fa-download me-1"></i> Unduh Soal
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize Maps
      $('.map-container').each(function() {
        const containerId = $(this).attr('id');
        const loaderId = containerId.replace('map-', 'loader-');
        const url = $(this).attr('data-url');
        const type = $(this).data('type');

        const map = L.map(containerId).setView([-2.5489, 118.0149], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        fetch(url)
          .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil file: ' + response.statusText);
            return response.text();
          })
          .then(data => {

            // Clean data from leading/trailing whitespace or BOM
            const cleanData = data.trim().replace(/^\uFEFF/, '');

            // Basic XML validation for KML
            if (type === 'kml' || type === 'gpx') {
              try {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(cleanData, "text/xml");
                const parserError = xmlDoc.getElementsByTagName("parsererror");
                if (parserError.length > 0) {
                  throw new Error("Format XML file tidak valid: " + parserError[0].textContent);
                }
              } catch (xmlErr) {
                console.warn(`XML validation warning for ${containerId}:`, xmlErr.message);
              }
            }

            let runLayer;
            try {
              if (type === 'kml') {
                runLayer = omnivore.kml.parse(cleanData);
              } else if (type === 'gpx') {
                runLayer = omnivore.gpx.parse(cleanData);
              } else if (type === 'geojson') {
                runLayer = omnivore.geojson.parse(cleanData);
              }

              if (runLayer) {
                const setupMap = () => {
                  $(`#${loaderId}`).fadeOut();

                  try {
                    // Force map to recalculate size in case container was hidden
                    map.invalidateSize();

                    const bounds = runLayer.getBounds();
                    if (bounds && bounds.isValid()) {
                      map.fitBounds(bounds, {
                        padding: [20, 20]
                      });
                    } else {
                      console.warn(`No valid bounds found for ${containerId}`);
                    }
                  } catch (boundsErr) {
                    console.warn(`Error getting bounds for ${containerId}:`, boundsErr);
                  }

                  runLayer.eachLayer(function(layer) {
                    if (layer.feature && layer.feature.properties) {
                      const props = layer.feature.properties;
                      let content = `<div class="p-1"><b>${props.name || 'Titik'}</b>`;
                      if (props.description) {
                        content += `<hr class="my-1"><small>${props.description}</small>`;
                      }
                      content += `</div>`;
                      layer.bindPopup(content);
                    }
                  });
                };

                runLayer.addTo(map);
                setupMap(); // Call immediately since parse() is synchronous
              }
            } catch (e) {
              throw new Error('Gagal memproses format file: ' + e.message);
            }
          })
          .catch(error => {
            console.error('Map loading error:', error);
            $(`#${loaderId}`).hide();
            $(`#${containerId}`).html(
              `<div class="p-5 text-white text-center"><i class="fa fa-exclamation-triangle fs-1 mb-3"></i><p>${error.message}</p></div>`
              );
          });
      });

      $('#form_ukk_evaluation').on('submit', function(e) {
        e.preventDefault();
        const resultId = $(this).data('result-id');
        const btnSave = $('#btn-save-score');
        const originalHtml = btnSave.html();

        btnSave.prop('disabled', true).html('<i class="fa-solid fa-arrows-rotate fa-spin me-2"></i>Menyimpan...');

        $.ajax({
          url: "{{ route('user.ukk.praktik.updateScore', ['result_id' => ':id']) }}".replace(':id',
            resultId),
          method: 'POST',
          data: $(this).serialize(),
          success: function(res) {
            const toast = new bootstrap.Toast($("#toast-success"));
            $("#toast-success #toast-text").text(res.message);
            toast.show();
            btnSave.prop('disabled', false).html(originalHtml);
          },
          error: function(err) {
            const message = err.responseJSON ? err.responseJSON.message : "Terjadi kesalahan";
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(message);
            toast.show();
            btnSave.prop('disabled', false).html(originalHtml);
          }
        });
      });
    });
  </script>
@endsection
