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
    :root {
      --primary-soft: #eef2ff;
      --success-soft: #ecfdf5;
      --danger-soft: #fef2f2;
      --border-radius-lg: 16px;
      --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    /* Memaksa semua parent untuk mengizinkan sticky */
    .page-wrapper,
    .page-body-wrapper,
    .page-body,
    .container-fluid,
    .e-category {
      overflow: visible !important;
    }

    .modern-card {
      border: none;
      border-radius: var(--border-radius-lg);
      box-shadow: var(--card-shadow);
      background: #ffffff;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
    }

    .modern-card-header {
      background: transparent;
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modern-card-header h5 {
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0;
      font-size: 1.1rem;
    }

    .instruction-box {
      max-height: 250px;
      overflow-y: auto;
      font-size: 0.9rem;
      background: #f8fafc;
      white-space: normal;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      line-height: 1.6;
    }

    /* Custom Radio as Pills */
    .status-pill-group {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
    }

    .status-pill {
      cursor: pointer;
      padding: 0.4rem 0.8rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      transition: all 0.2s;
      border: 1px solid #e2e8f0;
      background: #fff;
      color: #64748b;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .rubric-status-radio {
      display: none;
    }

    /* Kompeten State */
    .rubric-status-radio[value="Kompeten"]:checked+.status-pill {
      background: #dcfce7;
      color: #15803d;
      border-color: #86efac;
      box-shadow: 0 2px 4px rgba(21, 128, 61, 0.1);
    }

    /* Belum Kompeten State */
    .rubric-status-radio[value="Belum Kompeten"]:checked+.status-pill {
      background: #fee2e2;
      color: #b91c1c;
      border-color: #fca5a5;
      box-shadow: 0 2px 4px rgba(185, 28, 28, 0.1);
    }

    .status-pill:hover {
      background: #f1f5f9;
    }

    /* Student Info Bar */
    .student-nav-bar {
      background: #fff;
      padding: 1rem 1.5rem;
      border-radius: 50px;
      box-shadow: var(--card-shadow);
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
    }

    .student-avatar {
      width: 45px;
      height: 45px;
      background: var(--primary-soft);
      color: #4f46e5;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.2rem;
    }

    /* Previews */
    .preview-card {
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #e2e8f0;
      margin-bottom: 2rem;
      background: #fff;
    }

    .preview-header {
      padding: 0.75rem 1rem;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .map-container {
      height: 450px;
      width: 100%;
    }

    /* Table styling */
    .modern-table thead th {
      background: #f8fafc;
      text-transform: uppercase;
      font-size: 0.7rem;
      letter-spacing: 0.05em;
      color: #64748b;
      border-top: none;
      padding: 0.75rem;
    }

    .category-divider {
      background: #f1f5f9 !important;
      font-weight: 700;
      font-size: 0.8rem;
      color: #475569;
      padding: 0.5rem 1rem !important;
    }

    .note-textarea {
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      font-size: 0.8rem;
      padding: .2rem;
      transition: all 0.2s;
    }

    .note-textarea:focus {
      border-color: #4f46e5;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .sticky-panel {
      top: 90px;
      z-index: 1;

    }

    @media (max-width: 991px) {
      .sticky-panel {
        position: static !important;
      }
    }
  </style>
@endsection

@section('main_content')
  <div class="container-fluid p-0">
    <div class="page-title">
      <div class="row p-2 p-sm-0">
        <div class="col-sm-6">
          <h3>Evaluasi Praktik</h3>
        </div>
        <div class="col-sm-6 text-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.home') }}"> <i data-feather="home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.ukk.index') }}">UKK</a></li>
            <li class="breadcrumb-item active">Evaluasi</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="container-fluid e-category p-0">
      {{-- Card Info UKK di Atas --}}
      <div class="modern-card mx-3">
        <div class="modern-card-header">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2 bg-primary-subtle rounded-3">
              <i class="fa fa-info-circle text-primary fs-5"></i>
            </div>
            <div>
              <h5 class="mb-0">{{ $ukk->title }}</h5>
              <p class="text-muted small mb-0">Informasi & Instruksi</p>
            </div>
          </div>
          @if ($ukk->file_path)
            <a href="{{ route('user.ukk.file.download', $ukk->id) }}"
              class="btn btn-outline-primary btn-sm rounded-pill px-3">
              <i class="fa fa-download me-1"></i> Unduh Berkas Soal
            </a>
          @endif
        </div>
        @if ($ukk->instructions)
          <div class="card-body p-4">
            <div class="instruction-box ql-editor">
              {!! $ukk->instructions !!}
            </div>
          </div>
        @endif
      </div>

      {{-- Navigasi Siswa Modern --}}
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
            <p class="f-light mb-0 text-break">{{ $practice_result->student->nisn }}</p>
          </div>
          <a {{ $practice_results->hasMorePages() ? 'href=' . route('user.ukk.praktik.evaluation', ['id' => $ukk->id, 'page' => $practice_results->currentPage() + 1]) : '' }}
            role="button" {{ !$practice_results->hasMorePages() ? 'aria-disabled="true"' : '' }}
            class="btn btn-primary px-3 py-2 d-flex justify-content-center align-items-center {{ !$practice_results->hasMorePages() ? 'disabled' : '' }}">
            <i data-feather="chevron-right" style="width:18px; height: 18px"></i>
          </a>
        </div>
      </div>

      <form method="POST" id="form_ukk_evaluation" data-result-id="{{ $practice_result->id }}">
        @csrf
        <div class="row">
          {{-- Sisi Kiri: Pekerjaan Siswa --}}
          <div class="col-lg-7">
            <div class="modern-card">
              <div class="modern-card-header">
                <h5>Hasil Kerja</h5>
                <span class="badge bg-light text-dark fw-medium">
                  {{ $practice_result->submitted_at->translatedFormat('d M Y, H:i') }}
                </span>
              </div>
              <div class="card-body p-4">
                @if ($practice_result->contents['description'])
                  <div class="p-3 rounded-4 mb-4" style="background: #f8fafc; border-left: 4px solid #4f46e5;">
                    <p class="text-muted small text-uppercase fw-bold mb-2">Catatan Siswa</p>
                    <p class="mb-0 text-dark italic">"{{ $practice_result->contents['description'] }}"</p>
                  </div>
                @endif

                <div class="d-flex flex-column gap-4">
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
                    @endphp

                    <div class="preview-card w-100 mb-0">
                      <div class="preview-header">
                        <div class="d-flex align-items-center gap-2">
                          <div class="p-2 bg-white border rounded">
                            <i class="{{ Helper::getContentIcon($file['name']) }} text-primary"></i>
                          </div>
                          <span class="small fw-bold text-dark text-truncate"
                            style="max-width: 250px;">{{ $file['name'] }}</span>
                        </div>
                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-xs btn-light border">
                          <i class="fa fa-expand"></i>
                        </a>
                      </div>
                      <div class="bg-light d-flex align-items-center justify-content-center" style="min-height: 250px;">
                        @if ($isImage)
                          <img src="{{ $fileUrl }}" class="img-fluid"
                            style="max-height: 800px; width: 100%; object-fit: contain;">
                        @elseif($isVideo)
                          <video controls class="w-100">
                            <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                          </video>
                        @elseif($isPdf)
                          <iframe src="{{ $fileUrl }}" width="100%" height="600px" frameborder="0"></iframe>
                        @elseif(in_array($ext, ['kml', 'gpx', 'geojson']))
                          <div class="w-100">
                            <div id="map-{{ $loop->index }}" class="map-container" data-url="{!! $fileUrl !!}"
                              data-type="{{ $ext }}"></div>
                          </div>
                        @elseif (Helper::isGooglePreviewable($file['name']))
                          <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true"
                            width="100%" height="600px" frameborder="0"></iframe>
                        @else
                          <div class="text-center py-5">
                            <i class="fa fa-file-text-o fs-1 text-muted mb-3 d-block"></i>
                            <a href="{{ $fileUrl }}" class="btn btn-primary btn-sm px-4 rounded-pill">Unduh untuk
                              Melihat</a>
                          </div>
                        @endif
                      </div>
                    </div>
                  @endforeach

                  @foreach ($practice_result->contents['links'] ?? [] as $link)
                    @if ($link)
                      <div
                        class="w-100 p-3 border rounded-4 d-flex align-items-center justify-content-between bg-white hover-bg-light transition">
                        <div class="d-flex align-items-center gap-3">
                          <div class="p-2 bg-info-subtle text-info rounded-circle">
                            <i class="fa fa-link"></i>
                          </div>
                          <a href="{{ $link }}" target="_blank"
                            class="text-primary fw-medium small text-break">{{ $link }}</a>
                        </div>
                        <i class="fa fa-chevron-right text-muted small"></i>
                      </div>
                    @endif
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          {{-- Sisi Kanan: Rubrik Penilaian --}}
          <div class="col-lg-5">
            <div class="sticky-top sticky-panel">
              <div class="modern-card border-top border-primary border-5">
                <div class="modern-card-header">
                  <div class="d-flex align-items-center gap-2">
                    <h5>Penilaian</h5>
                    <button type="button" class="btn btn-xs btn-outline-info rounded-circle p-0"
                      style="width: 20px; height: 20px; line-height: 18px;" data-bs-toggle="modal"
                      data-bs-target="#modalKriteria">
                      <i class="fa fa-info" style="font-size: 10px;"></i>
                    </button>
                  </div>
                  <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" type="submit"
                    id="btn-save-score">
                    <i class="fa fa-check-circle me-1"></i> Simpan
                  </button>
                </div>
                <div class="card-body p-0">
                  @if ($ukk->rubric && isset($ukk->rubric['element']))
                    <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                      <table class="table modern-table align-middle mb-0">
                        <thead>
                          <tr>
                            <th class="text-center" style="width: 40px;">No</th>
                            <th>Elemen</th>
                            <th class="text-center" style="width: 140px;">Capaian</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                            $rubricAssessment = $practice_result->contents['rubric_assessment'] ?? [];
                            $categories = ['Utama', 'Pendukung'];
                          @endphp
                          @foreach ($categories as $cat)
                            @php
                              $filteredIndices = [];
                              foreach ($ukk->rubric['category'] as $idx => $category) {
                                  if ($category === $cat) {
                                      $filteredIndices[] = $idx;
                                  }
                              }
                            @endphp

                            @if (count($filteredIndices) > 0)
                              <tr>
                                <td colspan="3" class="category-divider">Elemen {{ $cat }}</td>
                              </tr>
                              @foreach ($filteredIndices as $i => $idx)
                                @php
                                  $element = $ukk->rubric['element'][$idx];
                                  $assessment = $rubricAssessment[$idx] ?? [];
                                  $status = $assessment['status'] ?? '';
                                  $note = $assessment['note'] ?? '';
                                @endphp
                                <tr>
                                  <td class="text-center small text-muted">{{ $i + 1 }}</td>
                                  <td>
                                    <span class="small fw-medium d-block mb-1">{{ $element }}</span>
                                    <textarea name="rubric_assessment[{{ $idx }}][note]" class="form-control note-textarea" rows="2"
                                      placeholder="Tambah catatan..." style="font-size: 11px;">{{ $note }}</textarea>
                                  </td>
                                  <td>
                                    <div class="status-pill-group">
                                      <label class="mb-0">
                                        <input type="radio" name="rubric_assessment[{{ $idx }}][status]"
                                          value="Kompeten" {{ $status === 'Kompeten' ? 'checked' : '' }}
                                          class="rubric-status-radio" data-category="{{ $cat }}">
                                        <span class="status-pill">KMP</span>
                                      </label>
                                      <label class="mb-0">
                                        <input type="radio" name="rubric_assessment[{{ $idx }}][status]"
                                          value="Belum Kompeten" {{ $status === 'Belum Kompeten' ? 'checked' : '' }}
                                          class="rubric-status-radio" data-category="{{ $cat }}">
                                        <span class="status-pill">BKMP</span>
                                      </label>
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            @endif
                          @endforeach
                        </tbody>
                      </table>
                    </div>

                    <div class="p-4 bg-light border-top"
                      style="border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);">
                      <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Kesimpulan Akhir</label>
                        <select name="final_conclusion" class="form-select border-0 shadow-sm conclusion-select"
                          disabled style="border-radius: 10px; height: 45px;">
                          <option value="">-- Tentukan Hasil Akhir --</option>
                          @foreach (['Sangat Kompeten', 'Kompeten', 'Cukup Kompeten', 'Belum Kompeten'] as $conclusion)
                            <option value="{{ $conclusion }}"
                              {{ ($practice_result->contents['final_conclusion'] ?? '') === $conclusion ? 'selected' : '' }}>
                              {{ $conclusion }}
                            </option>
                          @endforeach
                        </select>
                        <div id="conclusion-hint" class="small text-danger mt-1">
                          Selesaikan semua penilaian elemen rubrik untuk menentukan kesimpulan.
                        </div>
                      </div>

                      <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Feedback</label>
                        <textarea class="form-control border-0 shadow-sm" rows="3" name="feedback"
                          placeholder="Berikan masukan konstruktif untuk siswa..." style="border-radius: 10px;">{{ $practice_result->feedback }}</textarea>
                      </div>

                      <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Nilai Konversi (Otomatis)</label>
                        <input type="text" name="score" id="score_input"
                          class="form-control border-0 shadow-sm bg-white" value="{{ $practice_result->score }}"
                          readonly style="border-radius: 10px; height: 45px;">
                      </div>

                      <div class="d-grid">
                        <button class="btn btn-primary py-2 fw-bold shadow-sm" style="border-radius: 12px;"
                          type="submit">
                          Simpan & Selesaikan Penilaian
                        </button>
                      </div>
                    </div>
                  @else
                    <div class="p-5 text-center">
                      <p class="text-muted small">Rubrik belum diatur untuk UKK ini.</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Kriteria -->
  <div class="modal fade" id="modalKriteria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow" style="border-radius: 20px;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">Kriteria Penentuan Kesimpulan Akhir & Nilai Konversi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="table-responsive rounded-3 overflow-hidden border">
            <table class="table table-bordered mb-0 align-middle">
              <thead class="bg-light">
                <tr>
                  <th class="small fw-bold text-uppercase">Kesimpulan</th>
                  <th class="small fw-bold text-uppercase">Kriteria</th>
                  <th class="small fw-bold text-uppercase text-center">Nilai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="fw-bold text-success">Sangat Kompeten</td>
                  <td class="small">Memenuhi seluruh kriteria elemen kompetensi utama dan pendukung.</td>
                  <td class="text-center fw-bold text-nowrap">91 - 100</td>
                </tr>
                <tr>
                  <td class="fw-bold text-primary">Kompeten</td>
                  <td class="small">Memenuhi seluruh kriteria elemen kompetensi utama dan sebagian besar kriteria elemen
                    kompetensi pendukung.</td>
                  <td class="text-center fw-bold text-nowrap">75 - 90</td>
                </tr>
                <tr>
                  <td class="fw-bold text-warning">Cukup Kompeten</td>
                  <td class="small">Memenuhi seluruh kriteria elemen kompetensi utama dan sebagian kecil kriteria elemen
                    kompetensi pendukung.</td>
                  <td class="text-center fw-bold text-nowrap">61 - 74</td>
                </tr>
                <tr>
                  <td class="fw-bold text-danger">Belum Kompeten</td>
                  <td class="small">Belum memenuhi sebagian kriteria elemen kompetensi utama.</td>
                  <td class="text-center fw-bold text-nowrap">&lt; 61</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
            <ul class="mb-0 ps-3">
              <li><b>Sebagian Besar:</b> >= 50% elemen pendukung kompeten.</li>
              <li><b>Sebagian Kecil:</b>
                < 50% elemen pendukung kompeten.</li>
            </ul>
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
      // Check if all rubric elements are filled
      function checkRubricCompletion() {
        const totalElements = $(".rubric-status-radio").length / 2;
        const filledElements = $(".rubric-status-radio:checked").length;

        const isComplete = filledElements === totalElements;

        $(".conclusion-select").prop("disabled", !isComplete);
        if (isComplete) {
          $("#conclusion-hint").fadeOut();
        } else {
          $("#conclusion-hint").fadeIn();
        }

        return isComplete;
      }

      // Auto-calculate score based on rubric assessment
      function autoCalculateScore() {
        if (!checkRubricCompletion()) return;

        const totalUtama = $(".rubric-status-radio[data-category='Utama']").length / 2;
        const totalPendukung = $(".rubric-status-radio[data-category='Pendukung']").length / 2;

        if (totalUtama === 0) return;

        const kompetenUtama = $(".rubric-status-radio[data-category='Utama'][value='Kompeten']:checked").length;
        const kompetenPendukung = $(".rubric-status-radio[data-category='Pendukung'][value='Kompeten']:checked")
          .length;

        let conclusion = "";
        let scoreStr = "";

        if (kompetenUtama < totalUtama) {
          conclusion = "Belum Kompeten";
          scoreStr = "< 61";
        } else {
          // All Utama met
          if (kompetenPendukung === totalPendukung) {
            conclusion = "Sangat Kompeten";
            scoreStr = "91 - 100";
          } else if (totalPendukung > 0 && kompetenPendukung >= (totalPendukung * 0.5)) {
            conclusion = "Kompeten";
            scoreStr = "75 - 90";
          } else if (totalPendukung > 0) {
            conclusion = "Cukup Kompeten";
            scoreStr = "61 - 74";
          } else {
            // Jika tidak ada elemen pendukung, tapi semua utama terpenuhi
            conclusion = "Sangat Kompeten";
            scoreStr = "91 - 100";
          }
        }

        // Update UI
        if (conclusion) {
          $(`.conclusion-select`).val(conclusion);
          $("#score_input").val(scoreStr);
        }
      }

      // Initial check
      checkRubricCompletion();

      $(".conclusion-select").on("change", function() {
        const val = $(this).val();
        let scoreStr = "";
        if (val === "Sangat Kompeten") scoreStr = "91 - 100";
        else if (val === "Kompeten") scoreStr = "75 - 90";
        else if (val === "Cukup Kompeten") scoreStr = "61 - 74";
        else if (val === "Belum Kompeten") scoreStr = "< 61";

        $("#score_input").val(scoreStr);
      });

      $(".rubric-status-radio").on("change", function() {
        autoCalculateScore();
      });

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
        console.log($(this).serialize());

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
