<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    /**
     * Generate permanent QR code for a single area.
     */
    public function generate(Area $area): RedirectResponse
    {
        $code = $area->generateQrCode();

        return back()->with('success', "QR Code untuk area '{$area->name}' berhasil dibuat: {$code}");
    }

    /**
     * Generate QR codes for all areas that do not have one yet.
     */
    public function generateAll(): RedirectResponse
    {
        $areas = Area::whereNull('qr_code')->orWhere('qr_code', '')->get();
        $count = 0;

        foreach ($areas as $area) {
            $area->generateQrCode();
            $count++;
        }

        return back()->with('success', "Berhasil membuat QR Code untuk {$count} ruangan!");
    }

    /**
     * Display printable door label / sticker for area QR code.
     */
    public function print(Area $area): View
    {
        if (! $area->hasQrCode()) {
            $area->generateQrCode();
        }

        $qrSvg = QrCode::size(280)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($area->qr_code_url);

        return view('admin.areas.qr-print', compact('area', 'qrSvg'));
    }

    /**
     * Download area QR code as an SVG vector image.
     */
    public function download(Area $area): Response
    {
        if (! $area->hasQrCode()) {
            $area->generateQrCode();
        }

        $qrSvg = QrCode::size(600)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($area->qr_code_url);

        $filename = 'QR_'.Str::slug($area->name).'_'.$area->qr_code.'.svg';

        return response($qrSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Show mobile QR scanner interface for Petugas (and Admin).
     */
    public function scanView(): View
    {
        return view('petugas.scan');
    }

    /**
     * Resolve scanned QR code or manual input to the appropriate room page.
     */
    public function resolveScan(Request $request, ?string $code = null): View|RedirectResponse
    {
        $rawCode = $code ?? $request->input('code');

        if (empty($rawCode)) {
            return redirect()->route('petugas.scan')
                ->with('error', 'Silakan masukkan atau pindai kode QR ruangan terlebih dahulu.');
        }

        $cleanCode = trim($rawCode);

        // If a full URL was passed, extract the trailing room code
        if (stripos($cleanCode, '/scan/room/') !== false) {
            $parts = preg_split('/\/scan\/room\//i', $cleanCode);
            $cleanCode = end($parts);
            $cleanCode = trim(explode('?', $cleanCode)[0], '/');
        }

        $cleanCodeUpper = strtoupper($cleanCode);

        // Match by qr_code column or dynamic AR-xxx (case-insensitive)
        $area = Area::whereRaw('UPPER(qr_code) = ?', [$cleanCodeUpper])->first();

        if (! $area) {
            // Also try resolving by ID if code like AR-001 or pure integer was passed
            $numericId = preg_replace('/[^0-9]/', '', $cleanCode);
            if (! empty($numericId)) {
                $area = Area::find((int) $numericId);
            }
        }

        if (! $area) {
            return redirect()->route('petugas.scan')
                ->with('error', "Ruangan dengan kode '{$rawCode}' tidak ditemukan dalam sistem.");
        }

        // Ensure QR code is initialized if resolving via ID for the first time
        if (! $area->hasQrCode()) {
            $area->generateQrCode();
        }

        // Backend authorization check: area must be active
        if (! $area->is_active) {
            return redirect()->route('petugas.scan')
                ->with('error', "Ruangan '{$area->name}' ({$area->code}) saat ini berstatus NONAKTIF. Pemeriksaan tidak dapat dilakukan.");
        }

        return view('petugas.room-detail', compact('area'));
    }
}
