<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR Code - {{ $area->name }} ({{ $area->code }}) - CleanTrack</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts: Poppins & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f5f3f3;
            color: #1b1c1c;
            margin: 0;
            padding: 24px;
        }
        h1, h2, h3, .font-heading {
            font-family: 'Poppins', sans-serif;
        }
        @media print {
            @page {
                size: A5 portrait;
                margin: 8mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-area {
                width: 140mm !important;
                max-width: 140mm !important;
                box-shadow: none !important;
                border: 2px solid #1b1c1c !important;
                margin: 0 auto !important;
                page-break-inside: avoid;
            }

            .brand-logo {
                width: 32px !important;
                height: 32px !important;
                display: block !important;
                opacity: 1 !important;
            }

            .brand-clean {
                color: #1b1c1c !important;
            }

            .brand-track,
            .print-red {
                color: #dc143c !important;
            }

            .print-red-background {
                background: #dc143c !important;
                color: #ffffff !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-md w-full mb-6 flex items-center justify-between gap-3 bg-white p-4 rounded-xl shadow-sm border border-[#eae8e7]">
        <a href="{{ route('admin.areas.show', $area->id) }}" class="btn-outline-neutral text-xs">
            <i class="fa-solid fa-arrow-left mr-1.5"></i>
            <span>Kembali ke Detail</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.areas.qr.download', $area->id) }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-download mr-1.5 text-[#dc143c]"></i>
                <span>Unduh SVG</span>
            </a>
            <button onclick="window.print()" class="btn-primary text-xs">
                <i class="fa-solid fa-print mr-1.5"></i>
                <span>Cetak Label</span>
            </button>
        </div>
    </div>

    <!-- Official Door Sticker / Label Card -->
    <div class="print-area bg-white max-w-[420px] w-full rounded-2xl border-2 border-[#1b1c1c] shadow-lg overflow-hidden p-7 text-center">
        
        <!-- Header Brand CleanTrack -->
        <div class="flex items-center justify-center gap-2.5 pb-4 border-b-2 border-[#1b1c1c]">
            <img src="{{ asset('favicon.svg') }}" alt="Logo CleanTrack" class="brand-logo w-8 h-8 rounded-lg shrink-0 object-contain">
            <div class="text-left">
                <div class="font-heading font-bold text-base tracking-tight leading-none">
                    <span class="brand-clean text-[#1b1c1c]">CLEAN</span><span class="brand-track text-[#dc143c]">TRACK</span>
                </div>
                <p class="text-[9px] font-semibold uppercase tracking-wider text-[#5c3f3f] mt-0.5">
                    Sistem Monitoring Kebersihan
                </p>
            </div>
        </div>

        <!-- Room Identity -->
        <div class="my-5">
            <span class="inline-block px-3 py-1 rounded-full bg-[#f5f3f3] text-[#1b1c1c] font-bold text-xs tracking-wider border border-[#eae8e7] uppercase">
                Kode: {{ $area->code }}
            </span>
            <h1 class="font-heading text-2xl font-bold text-[#1b1c1c] mt-2.5 leading-tight">
                {{ $area->name }}
            </h1>
            <p class="text-xs text-[#5c3f3f] font-medium mt-1">
                {{ $area->location }} &bull; Lantai {{ $area->floor }}
            </p>
        </div>

        <!-- QR Code SVG Wrapper -->
        <div class="bg-white p-3 rounded-xl border border-[#eae8e7] inline-block shadow-xs my-2">
            <div class="w-[240px] h-[240px] flex items-center justify-center">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Scanning Instructions -->
        <div class="mt-4 pt-3 border-t border-dashed border-[#eae8e7]">
            <div class="flex items-center justify-center gap-2 text-[#dc143c] print-red text-xs font-semibold">
                <i class="fa-solid fa-camera"></i>
                <span>Pindai QR untuk Monitoring</span>
            </div>
            <p class="text-[11px] text-[#5c3f3f] mt-1 leading-relaxed">
                Arahkan kamera HP ke QR Code ini untuk membuka data ruangan dan mencatat monitoring kebersihan.
            </p>
            <p class="text-[9px] text-[#8c8282] mt-2 tracking-wide font-mono">
                {{ $area->qr_code_url }}
            </p>
        </div>
    </div>

    <!-- Print Hint (Hidden during print) -->
    <p class="no-print text-xs text-[#5c3f3f] mt-4 text-center">
        Tip: Gunakan ukuran kertas A5 atau Sticker Label saat mencetak untuk hasil terbaik.
    </p>

</body>
</html>
