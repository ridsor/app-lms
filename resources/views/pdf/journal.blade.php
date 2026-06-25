@php
    use App\Helpers\Helper;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>JURNAL MENGAJAR</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/quill.snow.css') }}">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h2,
        h4 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin-bottom: 10px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #555;
            padding: 4px 6px;
            text-align: center;
        }

        table th {
            background: #f3f3f3;
        }

        .left {
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>JURNAL MENGAJAR</h2>
    <div class="info">
        <p>
            Periode : {{ $period['semester'] == 'odd' ? 'Ganjil' : 'Genap' }} TA
            {{ $period['academic_year'] }}
        </p>
        <p>Mata Pelajaran : {{ $subject['code'] }} - {{ strtoupper($subject['name']) }}</p>
        <p>
            Kelas : {{ $class['major'] ? $class['major']['name'] . ' - ' : '' }}
            {{ $class['name'] }} -
            {{ $class['level'] }}
        </p>
        <p>Pengjar : {{ $teacher['name'] }} ({{ $teacher['nip'] }})</p>
    </div>
    <br/>
    <table>
        <thead>
            <tr>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Pokok Pembahasan</th>
                <th rowspan="2">Sub Pokok Pembahasan</th>
                <th rowspan="2">Materi</th>
                <th rowspan="2">Tugas</th>
                <th colspan="4">Absensi</th>
            </tr>
            <tr>
                <th>H</th>
                <th>I</th>
                <th>S</th>
                <th>A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meetings as $index => $meeting)
                <tr>
                    <td style="text-align: center;">
                        {{ $meeting['formatted_date'] }}
                    </td>
                    <td style="text-align: left; padding: 8px;">
                        @if (!empty($meeting['teaching_journal']))
                            <div class="meeting-content">
                                <div class="ql-editor text-wrap h-auto" style="padding:0; margin:0; white-space: wrap;">
                                    {!! $meeting['teaching_journal']['subject_matter'] !!}
                                </div>
                            </div>
                        @else
                            <i>Tidak ada catatan mengajar untuk pertemuan ini.</i>
                        @endif
                    </td>
                    <td style="text-align: left; padding: 8px;">
                        @if (!empty($meeting['teaching_journal']))
                            <div class="meeting-content">
                                <div class="ql-editor text-wrap h-auto" style="padding:0; margin:0; white-space: wrap;">
                                    {!! $meeting['teaching_journal']['sub_subject_matter'] !!}
                                </div>
                            </div>
                        @endif
                    </td>
                    <td style="text-align: left; padding: 8px;">
                        @if (!empty($meeting['materials']))
                            <ul style="padding-left: 16px; margin: 0;">
                                @foreach ($meeting['materials'] as $material)
                                    <li>{{ $material['title'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <i>Tidak ada materi untuk pertemuan ini.</i>
                        @endif
                    </td>
                    <td style="text-align: left; padding: 8px;">
                        @if (!empty($meeting['tasks']))
                            <ul style="padding-left: 16px; margin: 0;">
                                @foreach ($meeting['tasks'] as $task)
                                    <li>{{ $task['title'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <i>Tidak ada tugas untuk pertemuan ini.</i>
                        @endif
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <span>{{ $meeting_summaries[$index]['total_attendance'] ?? '-' }}</span>
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <span>{{ $meeting_summaries[$index]['total_permission'] ?? '-' }}</span>
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <span>{{ $meeting_summaries[$index]['total_sick'] ?? '-' }}</span>
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <span>{{ $meeting_summaries[$index]['total_absence'] ?? '-' }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
