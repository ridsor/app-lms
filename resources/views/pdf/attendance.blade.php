@php
    use App\Helpers\Helper;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

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
    <div class="header">
        <p><strong>Mata Pelajaran:</strong> {{ strtoupper($subject) }}</p>
        <p><strong>Kelas:</strong> {{ $class }}</p>
        <p><strong>Pengajar:</strong> {{ $teacher }}</p>
        <p><strong>Periode:</strong> {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                @for ($i = 1; $i <= $total_meetings; $i++)
                    <th>{{ $i }}</th>
                @endfor
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Absen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="left">
                        {{ $attendance['student']->name }}<br>
                        <small>{{ $attendance['student']->nis }}</small>
                    </td>
                    @foreach ($attendance['attendances'] as $status)
                        <td>{{ $status }}</td>
                    @endforeach
                    <td>{{ $attendance['total_attendance'] }}</td>
                    <td>{{ $attendance['total_permission'] }}</td>
                    <td>{{ $attendance['total_sick'] }}</td>
                    <td>{{ $attendance['total_absence'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <table style="width: 40%; border: 1px solid #555; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2" style="background: #f3f3f3; text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 20%; border: 1px solid #555; text-align: center;">H</td>
                <td style="border: 1px solid #555;">Hadir</td>
            </tr>
            <tr>
                <td style="border: 1px solid #555; text-align: center;">I</td>
                <td style="border: 1px solid #555;">Izin</td>
            </tr>
            <tr>
                <td style="border: 1px solid #555; text-align: center;">S</td>
                <td style="border: 1px solid #555;">Sakit</td>
            </tr>
            <tr>
                <td style="border: 1px solid #555; text-align: center;">A</td>
                <td style="border: 1px solid #555;">Absen (Tanpa Keterangan)</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
