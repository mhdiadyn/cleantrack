@extends('layouts.app')

@section('title', 'Scan QR Ruangan')
@section('header_title', 'Pindai QR Code Ruangan')
@section('header_subtitle', 'Pindai stiker QR pada pintu ruangan untuk memulai pemeriksaan kebersihan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- QR Camera Scanner Card -->
    <div class="card-clean p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#eae8e7] dark:border-[#2e3030]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div>
                    <h2 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Kamera Pemindai QR</h2>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Arahkan lensa kamera ke kode QR ruangan</p>
                </div>
            </div>
            <span id="scanner-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#f5f3f3] text-[#5c3f3f] dark:bg-[#262727] dark:text-[#c8c6c5] border border-[#eae8e7] dark:border-[#333535]">
                <span class="w-2 h-2 rounded-full bg-[#8c8282]" id="scanner-dot"></span>
                <span id="scanner-status">Kamera Siap</span>
            </span>
        </div>

        <!-- Camera Box Container -->
        <div class="my-5">
            <!-- Camera Device Selector (if multiple cameras detected) -->
            <div id="camera-select-wrapper" class="hidden max-w-sm mx-auto mb-3">
                <div class="flex items-center gap-2">
                    <label for="camera-select" class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] shrink-0">
                        <i class="fa-solid fa-video mr-1"></i>Pilih Kamera:
                    </label>
                    <select id="camera-select" class="input-clean text-xs py-1.5 px-2.5 flex-1 rounded-lg">
                        <option value="">Deteksi otomatis...</option>
                    </select>
                </div>
            </div>

            <div id="reader-wrapper" class="relative max-w-sm mx-auto overflow-hidden rounded-2xl border-2 border-[#eae8e7] dark:border-[#333535] bg-black shadow-inner min-h-[280px]">
                <!-- Html5Qrcode Scanner Target Element -->
                <div id="reader" class="w-full min-h-[280px]"></div>

                <!-- Laser scanning overlay (active when scanning) -->
                <div id="scanner-laser" class="hidden absolute inset-x-8 top-1/2 -translate-y-1/2 pointer-events-none z-10">
                    <div class="h-0.5 w-full bg-[#dc143c] shadow-[0_0_12px_#dc143c] animate-pulse"></div>
                </div>

                <!-- Camera Idle Placeholder -->
                <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 text-[#c8c6c5] z-10 bg-[#121313]">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 mx-auto flex items-center justify-center mb-3 border border-white/10">
                        <i class="fa-solid fa-qrcode text-3xl text-[#dc143c]"></i>
                    </div>
                    <p class="text-xs font-medium text-white mb-1">Kamera Belum Aktif</p>
                    <p class="text-[11px] text-[#8c8282]">Klik tombol <strong>"Aktifkan Kamera"</strong> di bawah untuk memulai pemindaian</p>
                </div>
            </div>

            <!-- Laptop Camera Shutter / Black Screen Helpful Hint -->
            <p class="text-[11px] text-center text-[#5c3f3f] dark:text-[#8c8282] mt-2">
                <i class="fa-solid fa-circle-info mr-1 text-[#dc143c]"></i>
                Jika tampilan kamera laptop gelap/hitam: periksa slider penutup fisik webcam di atas layar laptop Anda, atau pilih kamera lain pada dropdown di atas.
            </p>

            <!-- Camera Feedback & Error Alerts -->
            <div id="camera-error" class="hidden mt-3 p-3.5 rounded-xl bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] text-xs">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-base shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-semibold" id="camera-error-msg">Izin kamera belum aktif atau tidak didukung.</p>
                        <p class="mt-1 text-[11px] leading-relaxed" id="camera-error-hint">
                            Silakan izinkan akses kamera di browser Anda, atau gunakan opsi input kode ruangan manual di bawah.
                        </p>
                        <div class="mt-2.5 flex items-center gap-2">
                            <button type="button" id="btn-retry-camera" class="px-3 py-1 bg-[#ba1a1a] text-white hover:bg-[#93000a] text-[11px] font-semibold rounded-lg transition-colors">
                                <i class="fa-solid fa-rotate-right mr-1"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Controls -->
        <div class="flex flex-wrap items-center justify-center gap-2.5 pt-2">
            <button type="button" id="btn-start-camera" class="btn-primary text-xs">
                <i class="fa-solid fa-play mr-1.5"></i>
                <span id="btn-start-text">Aktifkan Kamera</span>
            </button>
            <button type="button" id="btn-stop-camera" class="btn-outline-neutral text-xs hidden">
                <i class="fa-solid fa-stop mr-1.5 text-[#ba1a1a]"></i>
                <span>Matikan Kamera</span>
            </button>
            <button type="button" id="btn-switch-camera" class="btn-outline-neutral text-xs hidden">
                <i class="fa-solid fa-camera-rotate mr-1.5"></i>
                <span>Ganti Kamera</span>
            </button>
            <button type="button" id="btn-torch" class="btn-outline-neutral text-xs hidden">
                <i class="fa-solid fa-bolt mr-1.5"></i>
                <span id="torch-text">Flash On</span>
            </button>
            <label for="qr-file-input" class="btn-outline-neutral text-xs cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-image text-[#dc143c]"></i>
                <span>Pindai dari Foto QR</span>
            </label>
            <input type="file" id="qr-file-input" accept="image/*" class="hidden">
        </div>
    </div>

    <!-- Manual Code Fallback Card -->
    <div class="card-clean p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-[#f5f3f3] text-[#1b1c1c] dark:bg-[#262727] dark:text-white flex items-center justify-center text-lg shrink-0 border border-[#eae8e7] dark:border-[#333535]">
                <i class="fa-solid fa-keyboard"></i>
            </div>
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Alternatif: Input Kode Ruangan Manual</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Gunakan opsi ini jika kamera Anda terkendala atau tidak dapat membaca QR</p>
            </div>
        </div>

        <form action="{{ route('petugas.room.resolve') }}" method="GET" class="space-y-3">
            <div>
                <label for="code" class="label-clean">Kode Ruangan</label>
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5c3f3f] dark:text-[#8c8282]">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </div>
                        <input type="text" 
                                id="code" 
                                name="code" 
                                required 
                                placeholder="Contoh: AR-001 atau ROOM-001" 
                                value="{{ old('code') }}" 
                                class="input-clean pl-10 text-sm font-semibold uppercase tracking-wider">
                    </div>
                    <button type="submit" class="btn-primary shrink-0 text-xs">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i>
                        <span>Buka Ruangan</span>
                    </button>
                </div>
                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282] mt-1.5">
                    Kode ruangan dapat dilihat pada stiker label yang terpasang di dekat pintu ruangan.
                </p>
            </div>
        </form>
    </div>

    <!-- Petunjuk Singkat -->
    <div class="card-clean p-5 bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
        <h4 class="font-heading text-xs font-semibold text-[#1b1c1c] dark:text-white uppercase tracking-wider mb-2 flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-[#dc143c]"></i>
            <span>Cara Kerja Pemindaian</span>
        </h4>
        <ol class="list-decimal list-inside text-xs text-[#5c3f3f] dark:text-[#c8c6c5] space-y-1.5">
            <li>Klik tombol <strong>"Aktifkan Kamera"</strong> dan berikan izin akses kamera saat diminta browser.</li>
            <li>Arahkan kamera ke stiker QR Code di pintu atau papan nama ruangan.</li>
            <li>Sistem akan otomatis mengenali ruangan dan mengarahkan Anda ke formulir pemeriksaan.</li>
            <li>Jika kamera tidak tersedia atau bermasalah, masukkan kode ruangan secara manual pada kotak di atas.</li>
        </ol>
    </div>

</div>

<style>
    /* Clean up html5-qrcode video container */
    #reader {
        width: 100% !important;
        min-height: 280px !important;
        border: none !important;
        position: relative;
    }
    #reader video {
        width: 100% !important;
        height: 100% !important;
        min-height: 280px !important;
        max-height: 380px !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 0.875rem !important;
    }
    #reader img[alt="Info icon"] {
        display: none !important;
    }
</style>
@endsection

@push('scripts')
<!-- Library html5-qrcode via CDN -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isProcessing = false;
    let isRedirecting = false;
    let availableCameras = [];
    let currentCameraId = null;
    let isTorchOn = false;

    const btnStart = document.getElementById('btn-start-camera');
    const btnStartText = document.getElementById('btn-start-text');
    const btnStop = document.getElementById('btn-stop-camera');
    const btnSwitch = document.getElementById('btn-switch-camera');
    const btnTorch = document.getElementById('btn-torch');
    const torchText = document.getElementById('torch-text');
    const btnRetry = document.getElementById('btn-retry-camera');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const cameraErrorBox = document.getElementById('camera-error');
    const cameraErrorMsg = document.getElementById('camera-error-msg');
    const cameraErrorHint = document.getElementById('camera-error-hint');
    const scannerDot = document.getElementById('scanner-dot');
    const scannerStatus = document.getElementById('scanner-status');
    const scannerLaser = document.getElementById('scanner-laser');
    const cameraSelectWrapper = document.getElementById('camera-select-wrapper');
    const cameraSelect = document.getElementById('camera-select');
    const qrFileInput = document.getElementById('qr-file-input');

    // Helper to detect virtual / software cameras that stream black screens
    function isVirtualCamera(label) {
        if (!label) return false;
        const lower = label.toLowerCase();
        return lower.includes('virtual') ||
               lower.includes('obs') ||
               lower.includes('lsvcam') ||
               lower.includes('vcam') ||
               lower.includes('manycam') ||
               lower.includes('splitcam') ||
               lower.includes('screen') ||
               lower.includes('droidcam') ||
               lower.includes('iriun') ||
               lower.includes('snap camera');
    }

    // Helper to pick the best physical camera for the current device
    function selectOptimalCamera(cameras, preferredId = null) {
        if (!cameras || cameras.length === 0) return null;

        // 1. Explicit preferred ID requested
        if (preferredId) {
            const found = cameras.find(c => c.id === preferredId);
            if (found) return found;
        }

        // 2. Saved preference in localStorage
        const savedPref = localStorage.getItem('cleantrack_preferred_camera');
        if (savedPref) {
            const found = cameras.find(c => c.id === savedPref);
            if (found) return found;
        }

        // Separate real physical cameras from virtual cameras (like LSVCam, OBS)
        const physical = cameras.filter(c => !isVirtualCamera(c.label));
        const pool = physical.length > 0 ? physical : cameras;

        // 3. On smartphone / tablet: prioritize physical rear/back camera
        const back = pool.find(c => /back|rear|belakang|environment/i.test(c.label));
        if (back) return back;

        // 4. Default: first physical camera (on laptops, camera index 0 is the built-in webcam)
        return pool[0];
    }

    // Check scanner active state safely
    function isScannerRunning() {
        if (!html5QrCode) return false;
        try {
            if (typeof html5QrCode.isScanning === 'boolean' && html5QrCode.isScanning) {
                return true;
            }
            if (typeof html5QrCode.getState === 'function') {
                const state = html5QrCode.getState();
                // 2 = SCANNING, 3 = PAUSED
                return state === 2 || state === 3;
            }
        } catch (e) {
            console.warn("isScannerRunning check error:", e);
        }
        return false;
    }

    function setStatus(text, color) {
        scannerStatus.innerText = text;
        if (color === 'green') {
            scannerDot.className = "w-2 h-2 rounded-full bg-[#00822d] animate-pulse";
        } else if (color === 'red') {
            scannerDot.className = "w-2 h-2 rounded-full bg-[#ba1a1a]";
        } else if (color === 'yellow') {
            scannerDot.className = "w-2 h-2 rounded-full bg-[#e6a100] animate-pulse";
        } else {
            scannerDot.className = "w-2 h-2 rounded-full bg-[#8c8282]";
        }
    }

    function hideError() {
        cameraErrorBox.classList.add('hidden');
    }

    function showError(message, hint = null) {
        cameraErrorBox.classList.remove('hidden');
        cameraErrorMsg.innerText = message;
        if (hint) {
            cameraErrorHint.innerText = hint;
        } else {
            cameraErrorHint.innerText = "Pastikan izin akses kamera aktif di pengaturan browser Anda, atau gunakan opsi input kode ruangan manual.";
        }
        setStatus("Kamera Gagal", "red");
    }

    // Populate camera select dropdown with readable labels
    function updateCameraSelectDropdown(cameras, selectedId = null) {
        if (!cameras || cameras.length === 0) {
            cameraSelectWrapper.classList.add('hidden');
            btnSwitch.classList.add('hidden');
            return;
        }

        availableCameras = cameras;
        cameraSelect.innerHTML = '';

        cameras.forEach((cam, index) => {
            const opt = document.createElement('option');
            opt.value = cam.id;

            let label = cam.label || `Kamera ${index + 1}`;
            if (isVirtualCamera(cam.label)) {
                label += ' (Virtual)';
            } else if (/back|rear|belakang|environment/i.test(cam.label)) {
                label += ' (Kamera Belakang)';
            } else if (/front|depan|user|integrated|wide vision|truevision|webcam/i.test(cam.label)) {
                label += ' (Webcam Laptop / Kamera Depan)';
            }

            opt.text = label;
            if (cam.id === selectedId) {
                opt.selected = true;
            }
            cameraSelect.appendChild(opt);
        });

        if (cameras.length > 1) {
            cameraSelectWrapper.classList.remove('hidden');
            btnSwitch.classList.remove('hidden');
        } else {
            cameraSelectWrapper.classList.add('hidden');
            btnSwitch.classList.add('hidden');
        }
    }

    // Format friendly error messages
    function parseCameraError(err) {
        console.error("Camera Error Log:", err);
        const errStr = (err && err.message ? err.message : String(err)).toLowerCase();

        if (errStr.includes("notallowederror") || errStr.includes("permission") || errStr.includes("denied")) {
            return {
                title: "Izin Akses Kamera Ditolak",
                hint: "Browser Anda memblokir kamera. Klik ikon gembok/kamera di samping kiri URL browser (address bar), ubah 'Camera' menjadi 'Allow' (Izinkan), lalu klik Coba Lagi."
            };
        }

        if (errStr.includes("notfounderror") || errStr.includes("devicesnotfound")) {
            return {
                title: "Kamera Tidak Ditemukan",
                hint: "Tidak ada perangkat kamera / webcam yang terdeteksi di perangkat Anda. Silakan hubungkan webcam atau gunakan input kode ruangan manual."
            };
        }

        if (errStr.includes("notreadableerror") || errStr.includes("trackstart") || errStr.includes("could not start video source")) {
            return {
                title: "Kamera Sedang Digunakan Aplikasi Lain",
                hint: "Kamera sedang aktif digunakan oleh aplikasi lain (seperti Zoom, Google Meet, Teams, atau tab browser lain). Harap tutup aplikasi tersebut dan klik Coba Lagi."
            };
        }

        if (errStr.includes("overconstrainederror")) {
            return {
                title: "Kamera Tidak Memenuhi Kriteria",
                hint: "Mode kamera yang diminta tidak tersedia di perangkat ini. Sistem akan mencoba kamera bawaan yang tersedia."
            };
        }

        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            return {
                title: "Kamera Memerlukan Koneksi Aman (HTTPS)",
                hint: "Browser membatasi fitur kamera pada jaringan non-HTTPS jika dibuka dari luar localhost. Gunakan HTTPS atau input kode manual di bawah."
            };
        }

        return {
            title: "Gagal Mengakses Kamera: " + (err.message || err),
            hint: "Silakan periksa izin kamera di browser Anda, tutup aplikasi lain yang sedang menggunakan kamera, atau masukkan kode ruangan manual di bawah."
        };
    }

    // Success handler when QR code is detected
    async function onScanSuccess(decodedText, decodedResult) {
        if (isRedirecting) return;
        isRedirecting = true;

        setStatus("QR Terdeteksi! Membuka Ruangan...", "green");
        scannerLaser.classList.add('hidden');

        // Play subtle sound vibration feedback if available
        if (navigator.vibrate) {
            try { navigator.vibrate(100); } catch (e) {}
        }

        // Clean room code from URL if scanned from full CleanTrack URL
        let codeToResolve = (decodedText || '').trim();
        if (codeToResolve.includes('/scan/room/')) {
            let parts = codeToResolve.split('/scan/room/');
            codeToResolve = parts[parts.length - 1].split('?')[0].replace(/^\/+|\/+$/g, '');
        }

        // Safely stop camera before redirecting
        try {
            await stopScanner();
        } catch (e) {
            console.warn("Stop before redirect notice:", e);
        }

        let redirectUrl = "{{ route('petugas.room.resolve') }}?code=" + encodeURIComponent(codeToResolve);
        window.location.href = redirectUrl;
    }

    function onScanFailure(error) {
        // Continuous per-frame scan attempt - silently ignore non-detections
    }

    // Safely stop and destroy scanner instance without race condition
    async function stopScanner() {
        if (isProcessing) return;
        isProcessing = true;

        try {
            if (html5QrCode) {
                if (isScannerRunning()) {
                    await html5QrCode.stop();
                }
                try {
                    await html5QrCode.clear();
                } catch (e) {
                    // Ignore clear if already empty
                }
                html5QrCode = null;
            }
        } catch (err) {
            console.warn("Error while stopping scanner:", err);
            html5QrCode = null;
        } finally {
            isProcessing = false;
            resetScannerUI();
        }
    }

    function resetScannerUI() {
        cameraPlaceholder.classList.remove('hidden');
        scannerLaser.classList.add('hidden');
        btnStart.classList.remove('hidden');
        btnStart.disabled = false;
        btnStartText.innerText = "Aktifkan Kamera";
        btnStop.classList.add('hidden');
        btnTorch.classList.add('hidden');
        isTorchOn = false;
        setStatus("Kamera Nonaktif", "gray");
    }

    // Primary function to start camera
    async function startScanner(preferredCameraId = null) {
        if (isProcessing) return;
        isProcessing = true;
        hideError();

        // UI Loading state
        btnStart.disabled = true;
        btnStartText.innerText = "Menghubungkan...";
        setStatus("Menghubungkan Kamera...", "yellow");

        try {
            // If already running or leftover instance exists, safely tear down first
            if (html5QrCode) {
                if (isScannerRunning()) {
                    try { await html5QrCode.stop(); } catch (e) {}
                }
                try { await html5QrCode.clear(); } catch (e) {}
                html5QrCode = null;
            }

            // Create fresh instance
            html5QrCode = new Html5Qrcode("reader");

            // 1. Enumerate available cameras if not already populated
            if (availableCameras.length === 0) {
                try {
                    const devices = await Html5Qrcode.getCameras();
                    if (devices && devices.length > 0) {
                        availableCameras = devices;
                    }
                } catch (e) {
                    console.info("Initial getCameras notice:", e);
                }
            }

            // 2. Select optimal camera (physical webcam prioritized over virtual camera like LSVCam)
            const targetId = preferredCameraId || (cameraSelect.value || null);
            const chosenCam = selectOptimalCamera(availableCameras, targetId);
            let cameraConfig;

            if (chosenCam) {
                cameraConfig = chosenCam.id;
                currentCameraId = chosenCam.id;
                updateCameraSelectDropdown(availableCameras, chosenCam.id);
            } else {
                // If cameras couldn't be enumerated yet (permission pending), fallback to facingMode
                cameraConfig = { facingMode: "environment" };
            }

            // Standard QR scanning configuration
            // Note: Omit strict aspectRatio to allow webcam drivers to stream at their native resolution without black-screen constraint failures!
            const qrConfig = {
                fps: 15,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const qrboxEdge = Math.floor(minEdge * 0.72);
                    return { width: Math.max(180, qrboxEdge), height: Math.max(180, qrboxEdge) };
                },
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            // Attempt to start scanning with the chosen camera
            try {
                await html5QrCode.start(
                    cameraConfig,
                    qrConfig,
                    onScanSuccess,
                    onScanFailure
                );
            } catch (initialErr) {
                console.warn("First camera start attempt failed, trying fallback:", initialErr);
                // Fallback attempt: if environment failed on laptop, try user camera
                if (typeof cameraConfig === 'object' && cameraConfig.facingMode === 'environment') {
                    cameraConfig = { facingMode: "user" };
                    await html5QrCode.start(
                        cameraConfig,
                        qrConfig,
                        onScanSuccess,
                        onScanFailure
                    );
                } else if (availableCameras.length > 0 && cameraConfig !== availableCameras[0].id) {
                    // Try the first device in list (physical webcam)
                    cameraConfig = availableCameras[0].id;
                    currentCameraId = cameraConfig;
                    await html5QrCode.start(
                        cameraConfig,
                        qrConfig,
                        onScanSuccess,
                        onScanFailure
                    );
                } else {
                    throw initialErr;
                }
            }

            // Refresh camera list now that browser permission is definitely granted
            try {
                const refreshedDevices = await Html5Qrcode.getCameras();
                if (refreshedDevices && refreshedDevices.length > 0) {
                    availableCameras = refreshedDevices;
                    if (!currentCameraId && chosenCam) {
                        currentCameraId = chosenCam.id;
                    }
                    updateCameraSelectDropdown(refreshedDevices, currentCameraId);
                }
            } catch (e) {
                console.info("Refreshed getCameras error:", e);
            }

            // Check torch / flashlight capability
            checkTorchSupport();

            // Update UI to running state
            cameraPlaceholder.classList.add('hidden');
            scannerLaser.classList.remove('hidden');
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
            setStatus("Kamera Aktif & Memindai", "green");

        } catch (err) {
            console.error("Camera activation completely failed:", err);
            const errInfo = parseCameraError(err);
            showError(errInfo.title, errInfo.hint);

            // Tear down cleanly on failure
            if (html5QrCode) {
                try {
                    if (isScannerRunning()) {
                        await html5QrCode.stop();
                    }
                    await html5QrCode.clear();
                } catch (e) {}
                html5QrCode = null;
            }
            cameraPlaceholder.classList.remove('hidden');
            scannerLaser.classList.add('hidden');
            btnStart.classList.remove('hidden');
            btnStart.disabled = false;
            btnStartText.innerText = "Aktifkan Kamera";
            btnStop.classList.add('hidden');
            btnTorch.classList.add('hidden');
        } finally {
            isProcessing = false;
        }
    }

    // Torch / flashlight support
    function checkTorchSupport() {
        if (!html5QrCode) return;
        try {
            const capabilities = html5QrCode.getRunningTrackCapabilities();
            if (capabilities && capabilities.torch) {
                btnTorch.classList.remove('hidden');
            } else {
                btnTorch.classList.add('hidden');
            }
        } catch (e) {
            btnTorch.classList.add('hidden');
        }
    }

    async function toggleTorch() {
        if (!html5QrCode || !isScannerRunning()) return;
        try {
            isTorchOn = !isTorchOn;
            await html5QrCode.applyVideoConstraints({
                advanced: [{ torch: isTorchOn }]
            });
            torchText.innerText = isTorchOn ? "Flash Off" : "Flash On";
        } catch (e) {
            console.warn("Torch toggle failed:", e);
        }
    }

    // Switch camera
    async function switchCamera() {
        if (availableCameras.length < 2) return;
        const currentIdx = availableCameras.findIndex(c => c.id === currentCameraId);
        const nextIdx = (currentIdx + 1) % availableCameras.length;
        const nextCam = availableCameras[nextIdx];
        
        currentCameraId = nextCam.id;
        cameraSelect.value = nextCam.id;
        localStorage.setItem('cleantrack_preferred_camera', nextCam.id);

        await stopScanner();
        await startScanner(nextCam.id);
    }

    // Handle scan from file / photo upload
    qrFileInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        hideError();
        setStatus("Menganalisis Gambar QR...", "yellow");

        try {
            if (isScannerRunning()) {
                await stopScanner();
            }

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const decodedText = await html5QrCode.scanFile(file, true);
            await onScanSuccess(decodedText, null);
        } catch (err) {
            console.error("File scan failed:", err);
            showError("Kode QR Tidak Terdeteksi di Gambar", "Pastikan gambar yang diunggah memiliki fokus yang jelas ke arah stiker QR code, atau coba foto ulang.");
            setStatus("Gagal Membaca File", "red");
        } finally {
            qrFileInput.value = '';
        }
    });

    // Event Listeners
    btnStart.addEventListener('click', () => startScanner());
    btnStop.addEventListener('click', () => stopScanner());
    btnSwitch.addEventListener('click', () => switchCamera());
    btnRetry.addEventListener('click', () => startScanner());
    btnTorch.addEventListener('click', () => toggleTorch());

    cameraSelect.addEventListener('change', async function() {
        const selectedId = this.value;
        if (!selectedId) return;

        currentCameraId = selectedId;
        localStorage.setItem('cleantrack_preferred_camera', selectedId);

        if (isScannerRunning()) {
            await stopScanner();
            await startScanner(selectedId);
        }
    });

    // Clean up tracks when navigating away
    window.addEventListener('beforeunload', () => {
        if (html5QrCode && isScannerRunning()) {
            try {
                html5QrCode.stop();
            } catch (e) {}
        }
    });
</script>
@endpush
