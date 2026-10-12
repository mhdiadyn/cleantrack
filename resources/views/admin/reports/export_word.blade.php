<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Laporan Kebersihan CleanTrack</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #dc2626;
            font-size: 16pt;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h3 {
            color: #4b5563;
            font-size: 11pt;
            margin: 4px 0;
            font-weight: normal;
        }
        .header p {
            color: #6b7280;
            font-size: 9pt;
            margin: 2px 0;
        }
        .summary-box {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 10px 15px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9pt;
        }
        table.data-table th {
            background-color: #b91c1c;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #991b1b;
        }
        table.data-table td {
            border: 1px solid #d1d5db;
            padding: 6px 6px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .badge {
            font-weight: bold;
            padding: 2px 4px;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .sig-box {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CLEANTRACK FACILITY MANAGEMENT</h1>
        <h3>LAPORAN RESMI MONITORING & REKAPITULASI KEBERSIHAN GEDUNG</h3>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong></p>
    </div>

    <div class="summary-box">
        <strong>Ringkasan Eksekutif:</strong><br>
        - Total Sesi Pemeriksaan: <strong>{{ $totalPemeriksaan }}</strong> area<br>
        - Tingkat Kebersihan Optimal (Bersih): <strong>{{ $totalBersih }}</strong> area<br>
        - Area Kotor / Sangat Kotor: <strong>{{ $totalKotor }}</strong> area<br>
        - Area Perlu Tindakan Lanjutan: <strong>{{ $totalPerluDibersihkan }}</strong> area
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 55px;">Mulai</th>
                <th style="width: 55px;">Selesai</th>
                <th style="width: 50px;">Durasi</th>
                <th style="width: 110px;">Area / Ruangan</th>
                <th style="width: 90px;">Lokasi & Lantai</th>
                <th style="width: 90px;">Petugas</th>
                <th style="width: 70px;">Kondisi</th>
                <th style="width: 80px;">Status</th>
                <th>Catatan Temuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monitorings as $index => $m)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $m->check_date ? $m->check_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $m->formatted_start_time ? $m->formatted_start_time : '-' }}</td>
                    <td class="text-center">{{ $m->formatted_end_time ? $m->formatted_end_time : ($m->check_time ? substr($m->check_time, 0, 5) : '-') }}</td>
                    <td class="text-center">{{ $m->duration_text ?? '-' }}</td>
                    <td><strong>{{ $m->area->code ?? '' }}</strong><br>{{ $m->area->name ?? '-' }}</td>
                    <td>{{ $m->area->location ?? '-' }} (Lt {{ $m->area->floor ?? '1' }})</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td class="text-center"><strong>{{ $m->cleanliness_condition }}</strong></td>
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

    <br><br>
    <table class="signatures" style="border:none;">
        <tr style="border:none;">
            <td class="sig-box" style="border:none;">
                Mengetahui,<br>
                <strong>Supervisor Operasional</strong>
                <br><br><br><br>
                ( __________________________ )
            </td>
            <td style="width: 10%; border:none;"></td>
            <td class="sig-box" style="border:none;">
                Dicetak pada {{ now('Asia/Jakarta')->translatedFormat('d F Y') }},<br>
                <strong>Administrator Sistem</strong>
                <br><br><br><br>
                ( {{ auth()->user()->name ?? 'Administrator CleanTrack' }} )
            </td>
        </tr>
    </table>
</body>
</html>
