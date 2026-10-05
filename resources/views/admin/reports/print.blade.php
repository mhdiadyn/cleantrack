<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Monitoring Kebersihan - CleanTrack</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            padding: 24px;
            font-size: 12px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f766e;
            letter-spacing: 0.5px;
        }
        .brand p {
            font-size: 11px;
            color: #64748b;
        }
        .report-meta {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }
        .title {
            text-align: center;
            margin: 16px 0;
        }
        .title h2 {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .title p {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .summary-box .label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
        }
        .summary-box .val {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-clean { background: #dcfce7; color: #166534; }
        .badge-dirty { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #075985; }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            font-size: 11px;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .signature-line {
            border-bottom: 1px solid #0f172a;
            font-weight: 700;
            padding-bottom: 4px;
        }

        .no-print {
            margin-bottom: 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn {
            background: #0f766e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Print Controls -->
        <div class="no-print">
            <button onclick="window.print()" class="btn">🖨️ Cetak Dokumen</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup Halaman</button>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="brand">
                <div>
                    <h1>CLEANTRACK</h1>
                    <p>Sistem Monitoring & Evaluasi Kebersihan Fasilitas</p>
                </div>
            </div>
            <div class="report-meta">
                <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y (H:i)') }}</p>
                <p><strong>Dicetak Oleh:</strong> {{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            <h2>Laporan Rekapitulasi Monitoring Kebersihan</h2>
            <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
            @if($selectedArea)
                <p style="font-size: 11px; color: #0f766e;">Filter Area: {{ $selectedArea->name }}</p>
            @endif
            @if($selectedPetugas)
                <p style="font-size: 11px; color: #0f766e;">Filter Petugas: {{ $selectedPetugas->name }}</p>
            @endif
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Pemeriksaan</div>
                <div class="val">{{ $totalPemeriksaan }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Area Bersih</div>
                <div class="val" style="color: #166534;">{{ $totalBersih }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Area Kotor</div>
                <div class="val" style="color: #991b1b;">{{ $totalKotor }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Perlu Tindakan</div>
                <div class="val" style="color: #c2410c;">{{ $totalPerluDibersihkan }}</div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th>Nama Area</th>
                    <th>Lokasi</th>
                    <th>Petugas</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Tanggal & Jam</th>
                    <th>Catatan Temuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monitorings as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $item->area->name ?? '-' }}</strong></td>
                        <td>{{ $item->area->location ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>
                            @php
                                $condClass = match($item->cleanliness_condition) {
                                    'Bersih' => 'badge-clean',
                                    'Cukup Bersih' => 'badge-info',
                                    default => 'badge-dirty'
                                };
                            @endphp
                            <span class="badge {{ $condClass }}">{{ $item->cleanliness_condition }}</span>
                        </td>
                        <td>
                            @php
                                $stClass = match($item->status) {
                                    'Sudah Dibersihkan' => 'badge-clean',
                                    'Sedang Dibersihkan' => 'badge-warning',
                                    'Perlu Dibersihkan' => 'badge-dirty',
                                    default => 'badge-info'
                                };
                            @endphp
                            <span class="badge {{ $stClass }}">{{ $item->status }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}<br><small style="color: #64748b;">{{ substr($item->check_time, 0, 5) }} WIB</small></td>
                        <td>{{ $item->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                            Tidak ada data pemeriksaan pada periode yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <p>Dibuat Oleh,</p>
                <div class="signature-space"></div>
                <p class="signature-line">{{ Auth::user()->name }}</p>
                <p style="color: #64748b; font-size: 10px; margin-top: 2px;">{{ ucfirst(Auth::user()->role) }} CleanTrack</p>
            </div>

            <div class="signature-box">
                <p>Menyetujui / Mengetahui,</p>
                <div class="signature-space"></div>
                <p class="signature-line">Kepala Bagian Fasilitas & Umum</p>
                <p style="color: #64748b; font-size: 10px; margin-top: 2px;">Penanggung Jawab Kebersihan</p>
            </div>
        </div>

    </div>

</body>
</html>
