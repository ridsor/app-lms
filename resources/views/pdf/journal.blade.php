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
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 20px;
        }

        .pertemuan {
            margin-bottom: 15px;
        }

        .pertemuan h3 {
            margin: 5px 0;
        }

        ul {
            margin: 5px 0;
        }

        .meeting-content {
            margin-bottom: 10px;
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
        <p>Guru : {{ $teacher['name'] }} ({{ $teacher['nip'] }})</p>
    </div>

    @foreach ($meetings as $index => $meeting)
        <div class="pertemuan">
            <h3>Pertemuan {{ $index + 1 }} - {{ Helper::getMeetingType($meeting['type']) }}</h3>
            <div style='margin-left: 1rem'>
                @if (!empty($meeting['formatted_started_at']))
                    <div class="meeting-content">
                        <b>Waktu Kelas Dimulai</b>
                        <br />
                        {{ $meeting['formatted_started_at'] }}
                    </div>
                    @if (!empty($meeting['teaching_journal']))
                        <div class="meeting-content">
                            <b>Pokok Pembahasan</b>
                            <br />
                            <div class="ql-editor text-wrap h-auto" style="padding:0; margin:0; white-space: wrap;">
                                {!! $meeting['teaching_journal']['subject_matter'] !!}
                            </div>
                        </div>
                        <div class="meeting-content">
                            <b>Sub Pokok Pembahasan</b>
                            <div class="ql-editor text-wrap h-auto" style="padding:0; margin:0; white-space: wrap;"
                                style="padding:0; margin:0; white-space: wrap;">
                                {!! $meeting['teaching_journal']['sub_subject_matter'] !!}
                            </div>
                        </div>
                        @if (!empty($meeting['teaching_journal']['additional_note']))
                            <div class="meeting-content">
                                <b>Catatan Tambahan</b>
                                <br />
                                <div class="ql-editor text-wrap h-auto" style="padding:0; margin:0; white-space: wrap;">
                                    {!! $meeting['teaching_journal']['additional_note'] !!}
                                </div>
                            </div>
                        @endif
                    @endif
                    @if (count($meeting['materials']) > 0)
                        <div class="meeting-content">
                            <b>Materi</b>
                            <ul>
                                @foreach ($meeting['materials'] as $m)
                                    <li>
                                            <b>{{ $m['title'] }}</b>
                                            <br />
                                        <div class="ql-editor text-wrap h-auto"
                                            style="padding:0; margin:0; white-space: wrap;">
                                            {!! $m['description'] !!}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (count($meeting['tasks']) > 0)
                        <div class="meeting-content">
                            <b>Tugas</b>
                            <ul>
                                @foreach ($meeting['tasks'] as $task)
                                    <li>
                                            <b>{{ $task['title'] }}</b>
                                            <br />
                                        <div class="ql-editor text-wrap h-auto"
                                            style="padding:0; margin:0; white-space: wrap;">
                                            {!! $task['description'] !!}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <p><i>Tidak ada catatan mengajar untuk pertemuan ini.</i></p>
                @endif
            </div>
        </div>
    @endforeach
</body>

</html>
