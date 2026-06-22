<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Lembar Penilaian UKK - {{ $student->name }}</title>
  <style>
    @page {
      margin: 1cm 1.5cm 2cm 1.5cm;
    }

    body {
      font-family: "Times New Roman", Times, serif;
      font-size: 11pt;
      line-height: 1.15;
      color: #000;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      position: relative;
    }

    .header-box {
      border: 1pt solid #000;
      padding: 4pt 15pt;
      position: absolute;
      top: -10pt;
      left: 0;
      font-weight: bold;
      font-size: 10pt;
      text-transform: uppercase;
    }

    .paket-box {
      border: 1pt solid #000;
      padding: 4pt;
      width: 60pt;
      text-align: center;
      position: absolute;
      top: -15pt;
      right: 0;
      font-weight: bold;
      font-size: 9pt;
      line-height: 1;
    }

    .paket-title {
      text-transform: uppercase;
      padding-bottom: 2pt;
      margin-bottom: 2pt;
      display: block;
    }

    .clear {
      clear: both;
    }

    .title-section {
      text-align: center;
      margin-top: 40pt;
      margin-bottom: 25pt;
    }

    .title-section h2 {
      margin: 0;
      font-size: 12.5pt;
      text-transform: uppercase;
      font-weight: bold;
    }

    .info-table {
      width: 100%;
      margin-bottom: 5pt;
      border-collapse: collapse;
    }

    .info-table td {
      padding: 1.5pt 0;
      vertical-align: top;
    }

    .info-table td.label {
      width: 140pt;
    }

    .info-table td.separator {
      width: 10pt;
    }

    hr.double {
      border: none;
      border-top: 3pt solid #000;
      height: 4pt;
      margin: 10pt 0 15pt 0;
    }

    .student-name-row {
      margin: 10pt 0;
      font-weight: bold;
    }

    table.main-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10pt;
      table-layout: auto;
    }

    table.main-table th,
    table.main-table td {
      border: 0.5pt solid #000;
      padding: 4pt 5pt;
      vertical-align: middle;
    }

    table.main-table th {
      text-align: center;
      font-weight: bold;
      font-size: 10pt;
    }

    thead {
      display: table-header-group;
    }

    tfoot {
      display: table-footer-group;
    }

    .text-center {
      text-align: center;
    }

    .text-bold {
      font-weight: bold;
    }

    .shaded-row th {
      background-color: #fff !important;
      font-size: 9.5pt !important;
      padding: 2pt !important;
      font-weight: bold;
    }

    .footer-wrapper {
      position: fixed;
      bottom: -1cm;
      left: 0;
      right: 0;
      width: 100%;
      border-top: 0.5pt solid #000;
      padding-top: 5pt;
      height: 30pt;
    }

    .footer-left {
      float: left;
      font-weight: bold;
      font-size: 10pt;
    }

    .footer-center {
      text-align: center;
      display: inline-block;
      width: 40%;
      margin-left: 5%;
      font-size: 9pt;
    }

    .footer-right {
      float: right;
      font-weight: bold;
      font-size: 10pt;
    }

    .page-break {
      page-break-after: always;
    }

    /* Page 2 specific */
    .kriteria-title {
      margin-top: 5pt;
      font-weight: bold;
      font-size: 11pt;
    }

    .kriteria-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5pt;
    }

    .kriteria-table th,
    .kriteria-table td {
      border: 0.5pt solid #000;
      padding: 5pt 7pt;
      vertical-align: middle;
    }

    .keterangan-section {
      margin-top: 15pt;
      font-size: 10.5pt;
    }

    .keterangan-list {
      list-style-type: none;
      padding-left: 0;
      margin-top: 5pt;
    }

    .keterangan-list li {
      position: relative;
      padding-left: 15pt;
      margin-bottom: 3pt;
    }

    .keterangan-list li:before {
      content: "•";
      position: absolute;
      left: 0;
    }

    .signature-area {
      margin-top: 20pt;
      width: 100%;
    }

    .signature-box {
      float: right;
      width: 200pt;
      text-align: center;
    }

    .check-mark {
      font-family: "DejaVu Sans", sans-serif;
      font-size: 12pt;
    }

    @font-face {
      font-family: 'Times New Roman';
      src: url('{{ storage_path('fonts/times.ttf') }}') format('truetype');
      font-weight: normal;
      font-style: normal;
    }

    @font-face {
      font-family: 'Times New Roman';
      src: url('{{ storage_path('fonts/timesbd.ttf') }}') format('truetype');
      font-weight: bold;
      font-style: normal;
    }
  </style>
</head>

@php
  $academicYear = $ukk->period->academic_year ?? '2025/2026';
  $yearSuffix = '';
  if (preg_match('/(\d{2})(\d{2})\/(\d{2})(\d{2})/', $academicYear, $matches)) {
      $yearSuffix = $matches[2] . '/' . $matches[4];
  } else {
      $yearSuffix = substr($academicYear, -5);
  }
  $package = $ukk->package_number ?? '4';
  $footerPrefix = 'P' . $package . '-' . $yearSuffix;
@endphp

<body>


  <div class="container">
    <!-- PAGE 1 HEADER -->
    <div class="header-box">DOKUMEN NEGARA</div>
    <div class="paket-box">
      <span class="paket-title">PAKET</span>
      {{ $package }}
    </div>

    <div class="title-section">
      <h2>UJI KOMPETENSI KEAHLIAN<br>TAHUN PELAJARAN {{ $academicYear }}</h2>
      <h2 style="margin-top: 20pt;">LEMBAR PENILAIAN</h2>
    </div>

    <table class="info-table">
      <tr>
        <td class="label">Satuan Pendidikan</td>
        <td class="separator">:</td>
        <td>Sekolah Menengah Kejuruan</td>
      </tr>
      <tr>
        <td class="label">Konsentrasi Keahlian</td>
        <td class="separator">:</td>
        <td>{{ $ukk->major ?? ($student->class->major->name ?? '-') }}</td>
      </tr>
      <tr>
        <td class="label">Kode</td>
        <td class="separator">:</td>
        <td>{{ $ukk->code ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Alokasi Waktu</td>
        <td class="separator">:</td>
        <td>{{ $ukk->duration / 60 }} Jam</td>
      </tr>
      <tr>
        <td class="label">Bentuk Soal</td>
        <td class="separator">:</td>
        <td>{{ $ukk->exam_format ?? 'Penugasan Perorangan' }}</td>
      </tr>
      <tr>
        <td class="label">Judul Tugas</td>
        <td class="separator">:</td>
        <td>{{ $ukk->title }}</td>
      </tr>
    </table>

    <hr class="double">

    <div class="student-name-row">
      Nama Peserta : <span>{{ $student->name }}</span>
    </div>

    <table class="main-table">
      <thead>
        <tr>
          <th rowspan="2" style="width: 30pt;">No</th>
          <th rowspan="2">ELEMEN KOMPETENSI</th>
          <th colspan="2" style="width: 50pt;">Capaian</th>
          <th rowspan="2" style="width: 100pt;">Catatan</th>
        </tr>
        <tr>
          <th style="font-size: 9pt; width: 25pt;">Kompeten</th>
          <th style="font-size: 9pt; width: 25pt;">Belum Kompeten</th>
        </tr>
        <tr class="shaded-row">
          <th>1</th>
          <th>2</th>
          <th>3</th>
          <th>4</th>
          <th>5</th>
        </tr>
      </thead>
      <tbody>
        @php
          $rubricAssessment = $result->contents['rubric_assessment'] ?? [];
          $categories = ['Utama', 'Pendukung'];
          $catRomawi = ['I', 'II'];
        @endphp

        @foreach ($categories as $catIdx => $cat)
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
              <td class="text-center text-bold">{{ $catRomawi[$catIdx] }}</td>
              <td class="text-bold">Kriteria Elemen Kompetensi {{ $cat }}</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @foreach ($filteredIndices as $i => $idx)
              @php
                $element = $ukk->rubric['element'][$idx];
                $assessment = $rubricAssessment[$idx] ?? [];
                $status = $assessment['status'] ?? '';
                $note = $assessment['note'] ?? '';
              @endphp
              <tr>
                <td class="text-center">{{ $i + 1 }}.</td>
                <td>{{ $element }}</td>
                <td class="text-center">
                  @if ($status === 'Kompeten')
                    <span class="check-mark">&#10003;</span>
                  @endif
                </td>
                <td class="text-center">
                  @if ($status === 'Belum Kompeten')
                    <span class="check-mark">&#10003;</span>
                  @endif
                </td>
                <td style="font-size: 9.5pt;">{{ $note }}</td>
              </tr>
            @endforeach
          @endif
        @endforeach
        <tr>
          <td></td>
          <td class="text-center text-bold">Kesimpulan Akhir</td>
          <td colspan="3" class="text-center" style="font-size: 9.5pt; line-height: 1.2;">
            @php
              $conc = $result->contents['final_conclusion'] ?? '';
            @endphp
            {{ $conc ?: 'Belum Kompeten/Cukup Kompeten/Kompeten/Sangat Kompeten*' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="kriteria-title">
    Kriteria penentuan kesimpulan akhir dan nilai konversi:
  </div>

  <table class="kriteria-table">
    <thead>
      <tr>
        <th style="width: 100pt;">Kesimpulan</th>
        <th>Kriteria</th>
        <th style="width: 90pt;">Nilai Konversi</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-bold">Sangat Kompeten</td>
        <td style="font-size: 10pt;">Apabila memenuhi seluruh kriteria elemen kompetensi utama dan pendukung.</td>
        <td class="text-center">91 - 100</td>
      </tr>
      <tr>
        <td class="text-bold">Kompeten</td>
        <td style="font-size: 10pt;">Apabila memenuhi seluruh kriteria elemen kompetensi utama dan sebagian besar
          kriteria elemen kompetensi pendukung.</td>
        <td class="text-center">75 - 90</td>
      </tr>
      <tr>
        <td class="text-bold">Cukup Kompeten</td>
        <td style="font-size: 10pt;">Apabila memenuhi seluruh kriteria elemen kompetensi utama dan sebagian kecil
          kriteria elemen kompetensi pendukung.</td>
        <td class="text-center">61 - 74</td>
      </tr>
      <tr>
        <td class="text-bold">Belum Kompeten</td>
        <td style="font-size: 10pt;">Apabila belum memenuhi sebagian kriteria elemen kompetensi utama.</td>
        <td class="text-center">&lt;61</td>
      </tr>
    </tbody>
  </table>

  <div class="keterangan-section">
    Keterangan :
    <ul class="keterangan-list">
      <li>Capaian kompetensi peserta uji dituliskan dalam bentuk <b>ceklis (<span
            class="check-mark">&#10003;</span>)</b></li>
      <li>Kesimpulan capaian kompetensi peserta uji, dihasilkan dalam bentuk <b>pernyataan* (pilih salah satu)</b>.
      </li>
      <li>Jika peserta uji direkomendasikan belum kompeten, maka peserta uji diberi kesempatan untuk mengulang</li>
      <li>Catatan diberikan sebagai keterangan tambahan yang diperlukan untuk memperkuat kesimpulan</li>
      <li><b>Catatan positif</b> diberikan kepada peserta uji yang mampu menunjukkan inovasi, efisiensi kerja, dan
        pemecahan masalah secara kreatif</li>
      <li><b>Catatan negatif</b> diberikan kepada peserta uji yang mengulangi proses atau elemen kompetensi lainnya
        yang bertentangan dengan kriteria elemen kompetensi</li>
    </ul>
  </div>

  <div class="signature-area">
    <div class="signature-box">
      ...................., ........................... 2026
      <br><br>
      Penilai 1/ Penilai 2 *)
      <br><br><br><br><br>
      ...........................................................
    </div>
  </div>
  <div class="clear"></div>
  </div>

  <script type="text/php">
    if (isset($pdf)) {
        // Ambil data dari Blade ke variabel PHP murni
        $footerPrefix = "{{ $footerPrefix }}"; 
        $centerText = "Hak Cipta pada Kemendikdasmen";

        // Fungsi ini otomatis melakukan "pengulangan" untuk setiap halaman
        $pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($footerPrefix, $centerText) {
            $fontBold = $fontMetrics->getFont("times", "bold");
            $fontNormal = $fontMetrics->getFont("times", "normal");
            
            $size = 10;
            $sizeCenter = 9;
            $y = 790; // Posisi vertikal (A4 memiliki tinggi 842pt)
            $left = 42.5; // Margin kiri 1.5cm
            $right = 595.3 - 42.5; // Margin kanan

            // 1. Teks Kiri (Kode Paket: P4-25/26)
            $canvas->text($left, $y, $footerPrefix, $fontBold, $size);
            
            // 2. Teks Tengah (Hak Cipta)
            $wCenter = $fontMetrics->getTextWidth($centerText, $fontBold, $sizeCenter);
            $canvas->text((595.3 - $wCenter) / 2, $y, $centerText, $fontBold, $sizeCenter);
            
            // 3. Teks Kanan (Halaman: PPsp-1/2)
            $pageText = "PPsp-" . $pageNumber . "/" . $pageCount;
            $wRight = $fontMetrics->getTextWidth($pageText, $fontBold, $size);
            $canvas->text($right - $wRight, $y, $pageText, $fontBold, $size);
        });
    }
</script>
</body>

</html>
