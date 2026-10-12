<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Kebersihan CleanTrack</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #999999;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #dc2626;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #b91c1c;
            text-align: center;
        }
        .header-subtitle {
            font-size: 11pt;
            text-align: center;
            color: #4b5563;
        }
        .text-center { text-align: center; }
        .bg-green { background-color: #dcfce7; color: #166534; font-weight: bold; }
        .bg-yellow { background-color: #fef3c7; color: #92400e; font-weight: bold; }
        .bg-red { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="header-title" style="border:none;">CLEANTRACK - SISTEM MONITORING KEBERSIHAN GEDUNG</td>
        </tr>
        <tr>
            <td colspan="10" class="header-subtitle" style="border:none;">REKAPITULASI LAPORAN OPERASIONAL KEBERSIHAN FASILITAS</td>
        </tr>
        <tr>
            <td colspan="10" class="header-subtitle" style="border:none;">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }} | Dicetak: {{ now('Asia/Jakarta')->translatedFormat('d F Y H:i') }} WIB</td>
        </tr>
        <tr><td colspan="10" style="border:none;"></td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="background-color:#991b1b; color:#ffffff; width:35px;">No</th>
                <th style="background-color:#991b1b; color:#ffffff; width:95px;">Tanggal</th>
                <th style="background-color:#991b1b; color:#ffffff; width:80px;">Jam Mulai</th>
                <th style="background-color:#991b1b; color:#ffffff; width:80px;">Jam Selesai</th>
                <th style="background-color:#991b1b; color:#ffffff; width:75px;">Durasi</th>
                <th style="background-color:#991b1b; color:#ffffff; width:160px;">Area / Ruangan</th>
                <th style="background-color:#991b1b; color:#ffffff; width:130px;">Lokasi & Lantai</th>
                <th style="background-color:#991b1b; color:#ffffff; width:130px;">Petugas</th>
                <th style="background-color:#991b1b; color:#ffffff; width:110px;">Kondisi</th>
                <th style="background-color:#991b1b; color:#ffffff; width:130px;">Status</th>
                <th style="background-color:#991b1b; color:#ffffff; width:220px;">Catatan Temuan & Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monitorings as $index => $m)
                @php
                    $condClass = '';
                    if ($m->cleanliness_condition === 'Bersih') $condClass = 'bg-green';
                    elseif ($m->cleanliness_condition === 'Cukup Bersih') $condClass = 'bg-yellow';
                    else $condClass = 'bg-red';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $m->check_date ? $m->check_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $m->formatted_start_time ? $m->formatted_start_time . ' WIB' : '-' }}</td>
                    <td class="text-center">{{ $m->formatted_end_time ? $m->formatted_end_time . ' WIB' : ($m->check_time ? substr($m->check_time, 0, 5) . ' WIB' : '-') }}</td>
                    <td class="text-center">{{ $m->duration_text ?? '-' }}</td>
                    <td><b>{{ $m->area->code ?? '' }}</b> - {{ $m->area->name ?? '-' }}</td>
                    <td>{{ $m->area->location ?? '-' }} (Lt {{ $m->area->floor ?? '1' }})</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td class="{{ $condClass }} text-center">{{ $m->cleanliness_condition }}</td>
                    <td class="text-center">{{ $m->status }}</td>
                    <td>{{ $m->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data monitoring untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
