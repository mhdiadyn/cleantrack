<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CleanTrack | Sistem Monitoring Kebersihan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Theme Detection (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: Poppins (Headline & SemiBold) & Inter (UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* ================================================================
         * CleanTrack Login Page — Scoped Styles
         * Supports both Light Mode and Dark Mode consistently.
         * ================================================================ */

        /* ---------- Base ---------- */
        .ct-login-body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .font-heading {
            font-family: 'Poppins', sans-serif;
        }

        /* ================================================================
         * LIGHT MODE — Default
         * ================================================================ */

        /* Background: warm neutral off-white */
        .ct-login-body {
            background-color: #f4f2f1;
        }

        /* Abstract Sphere — Primary (top-right): softer, lower opacity */
        .sphere-primary {
            position: absolute;
            width: 780px;
            height: 780px;
            top: -220px;
            right: -180px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(255, 43, 84, 0.55) 0%, rgba(220, 20, 60, 0.38) 28%, rgba(138, 0, 26, 0.22) 60%, rgba(48, 0, 9, 0.08) 82%, transparent 100%);
            box-shadow: 0 0 100px 20px rgba(220, 20, 60, 0.18);
            pointer-events: none;
            z-index: 1;
            transition: background 0.4s ease, box-shadow 0.4s ease;
        }

        /* Abstract Sphere — Secondary (bottom-left): softer */
        .sphere-secondary {
            position: absolute;
            width: 680px;
            height: 680px;
            bottom: -240px;
            left: -180px;
            border-radius: 50%;
            background: radial-gradient(circle at 65% 35%, rgba(220, 20, 60, 0.42) 0%, rgba(160, 6, 36, 0.28) 35%, rgba(66, 2, 13, 0.14) 70%, transparent 100%);
            box-shadow: 0 0 80px 15px rgba(220, 20, 60, 0.12);
            pointer-events: none;
            z-index: 1;
            transition: background 0.4s ease, box-shadow 0.4s ease;
        }

        /* Ambient center glow */
        .ambient-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220, 20, 60, 0.10) 0%, rgba(139, 0, 29, 0.05) 50%, transparent 75%);
            filter: blur(60px);
            pointer-events: none;
            z-index: 1;
            transition: background 0.4s ease;
        }

        /* Vignette overlay — light mode: very subtle */
        .ct-vignette {
            background: radial-gradient(ellipse at center, transparent 50%, rgba(220, 20, 60, 0.04) 100%);
        }

        /* Main glassmorphism outer card — Light: white/translucent */
        .glass-panel {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.82) 0%, rgba(255, 255, 255, 0.65) 100%);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.90);
            box-shadow:
                0 30px 70px -15px rgba(0, 0, 0, 0.14),
                0 0 30px rgba(220, 20, 60, 0.07),
                inset 0 1px 1px 0 rgba(255, 255, 255, 1);
            transition: background 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        /* Inner form container — Light: clean white surface */
        .glass-subpanel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(220, 20, 60, 0.12);
            box-shadow:
                0 8px 28px -8px rgba(0, 0, 0, 0.09),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            transition: background 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        /* Glass inputs — Light: white bg, dark text, visible border */
        .glass-input {
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #e2d6d8;
            border-radius: 12px;
            color: #1b1c1c;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .glass-input::placeholder {
            color: #9d8b90;
        }

        .glass-input:focus {
            outline: none;
            background: #ffffff;
            border-color: #dc143c;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.15);
        }

        /* Theme toggle button — Light */
        .ct-theme-btn {
            background: rgba(255, 255, 255, 0.80);
            border: 1px solid rgba(0, 0, 0, 0.12);
            color: #1b1c1c;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .ct-theme-btn:hover {
            background: rgba(255, 255, 255, 0.95);
        }

        /* Label & metadata text — Light */
        .ct-label          { color: #1b1c1c; }
        .ct-text-primary   { color: #1b1c1c; }
        .ct-text-secondary { color: #4a3540; }
        .ct-text-muted     { color: #7a6570; }
        .ct-text-faint     { color: #9d8b90; }

        /* Decorative top bar text */
        .ct-topbar-brand   { color: #2c1a20; }
        .ct-topbar-label   { color: #7a6570; }

        /* Section footer divider */
        .ct-footer-border  { border-color: rgba(0, 0, 0, 0.10); }

        /* Feature badge — Light: subtle warm surface */
        .ct-badge {
            background: rgba(220, 20, 60, 0.07);
            border: 1px solid rgba(220, 20, 60, 0.18);
        }
        .ct-badge span { color: #2c1a20; }

        /* Password toggle icon */
        .ct-pw-toggle { color: #9d8b90; }
        .ct-pw-toggle:hover { color: #1b1c1c; }

        /* Remember me label */
        .ct-remember { color: #4a3540; }

        /* Alert — Error: Light */
        .ct-alert-error {
            background: #ffeef0;
            border: 1px solid rgba(186, 26, 26, 0.35);
            color: #7c0000;
        }
        .ct-alert-error .ct-alert-icon { color: #ba1a1a; }

        /* Alert — Success: Light */
        .ct-alert-success {
            background: #edfff5;
            border: 1px solid rgba(0, 102, 34, 0.30);
            color: #004d1a;
        }
        .ct-alert-success .ct-alert-icon { color: #006622; }

        /* Validation error text */
        .ct-field-error { color: #ba1a1a; }

        /* Specular highlight edge */
        .ct-specular { background: rgba(220, 20, 60, 0.08); }

        /* ================================================================
         * DARK MODE — Activated by html.dark
         * ================================================================ */

        html.dark .ct-login-body {
            background-color: #050204;
        }

        html.dark .sphere-primary {
            background: radial-gradient(circle at 35% 35%, #ff2b54 0%, #e1143c 28%, #8a001a 60%, #300009 82%, transparent 100%);
            box-shadow: 0 0 120px 30px rgba(225, 20, 60, 0.45);
        }

        html.dark .sphere-secondary {
            background: radial-gradient(circle at 65% 35%, #e1143c 0%, #a00624 35%, #42020d 70%, #150104 90%, transparent 100%);
            box-shadow: 0 0 100px 20px rgba(220, 20, 60, 0.3);
        }

        html.dark .ambient-glow {
            background: radial-gradient(circle, rgba(220, 20, 60, 0.32) 0%, rgba(139, 0, 29, 0.15) 50%, transparent 75%);
        }

        html.dark .ct-vignette {
            background: radial-gradient(ellipse at center, transparent 40%, rgba(5, 2, 4, 0.60) 100%);
        }

        /* Outer glass panel — Dark: subtle dark glass */
        html.dark .glass-panel {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.04) 100%);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow:
                0 30px 70px -15px rgba(0, 0, 0, 0.85),
                0 0 40px rgba(220, 20, 60, 0.2),
                inset 0 1px 1px 0 rgba(255, 255, 255, 0.35);
        }

        /* Inner form panel — Dark: dark maroon-tinted */
        html.dark .glass-subpanel {
            background: rgba(10, 4, 6, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow:
                0 10px 30px -10px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        /* Inputs — Dark */
        html.dark .glass-input {
            background: rgba(255, 255, 255, 0.07);
            border: 1.5px solid rgba(255, 255, 255, 0.20);
            color: #ffffff;
        }

        html.dark .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.40);
        }

        html.dark .glass-input:focus {
            background: rgba(255, 255, 255, 0.11);
            border-color: #ff3b5c;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.35);
        }

        /* Theme toggle — Dark */
        html.dark .ct-theme-btn {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }
        html.dark .ct-theme-btn:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        /* Text tokens — Dark */
        html.dark .ct-label          { color: rgba(255, 255, 255, 0.92); }
        html.dark .ct-text-primary   { color: #ffffff; }
        html.dark .ct-text-secondary { color: rgba(255, 255, 255, 0.80); }
        html.dark .ct-text-muted     { color: rgba(255, 255, 255, 0.60); }
        html.dark .ct-text-faint     { color: rgba(255, 255, 255, 0.45); }

        html.dark .ct-topbar-brand   { color: rgba(255, 255, 255, 0.88); }
        html.dark .ct-topbar-label   { color: rgba(255, 255, 255, 0.50); }

        html.dark .ct-footer-border  { border-color: rgba(255, 255, 255, 0.10); }

        /* Badge — Dark */
        html.dark .ct-badge {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.10);
        }
        html.dark .ct-badge span { color: rgba(255, 255, 255, 0.80); }

        /* Password toggle — Dark */
        html.dark .ct-pw-toggle { color: rgba(255, 255, 255, 0.50); }
        html.dark .ct-pw-toggle:hover { color: #ffffff; }

        /* Remember me — Dark */
        html.dark .ct-remember { color: rgba(255, 255, 255, 0.80); }

        /* Alert — Error: Dark */
        html.dark .ct-alert-error {
            background: rgba(139, 0, 29, 0.35);
            border: 1px solid rgba(255, 60, 90, 0.40);
            color: #ffb3b3;
        }
        html.dark .ct-alert-error .ct-alert-icon { color: #ff7070; }

        /* Alert — Success: Dark */
        html.dark .ct-alert-success {
            background: rgba(0, 90, 30, 0.35);
            border: 1px solid rgba(52, 211, 153, 0.40);
            color: #a7f3d0;
        }
        html.dark .ct-alert-success .ct-alert-icon { color: #6ee7b7; }

        /* Validation error — Dark */
        html.dark .ct-field-error { color: #ff9494; }

        /* Specular — Dark */
        html.dark .ct-specular { background: rgba(255, 255, 255, 0.12); }

        /* Glowing Crimson Button (same in both modes) */
        .btn-crimson-glow {
            background: linear-gradient(135deg, #e1143c 0%, #b80026 100%);
            box-shadow: 0 8px 24px -4px rgba(225, 20, 60, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.3);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-crimson-glow:hover {
            background: linear-gradient(135deg, #ff234b 0%, #c9002b 100%);
            box-shadow: 0 12px 30px -2px rgba(225, 20, 60, 0.75), inset 0 1px 1px rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-crimson-glow:active {
            transform: translateY(0);
            box-shadow: 0 4px 14px rgba(225, 20, 60, 0.4);
        }

        /* Checkbox accent */
        .ct-checkbox {
            accent-color: #dc143c;
        }

        /* Icon color in input — shared */
        .ct-input-icon { color: #9d8b90; }
        html.dark .ct-input-icon { color: rgba(255, 255, 255, 0.45); }
    </style>
</head>
<body class="ct-login-body h-full antialiased flex items-start lg:items-center justify-center p-4 sm:p-6 lg:p-8 relative min-h-screen overflow-x-hidden selection:bg-[#dc143c] selection:text-white">

    <!-- Abstract 3D Spheres & Glowing Crimson Ambient Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <!-- Top Right Glowing 3D Sphere -->
        <div class="sphere-primary"></div>
        <!-- Bottom Left Curved 3D Sphere -->
        <div class="sphere-secondary"></div>
        <!-- Diffuse Ambient Center Bloom Behind Glass Panel -->
        <div class="ambient-glow"></div>
        <!-- Vignette Overlay -->
        <div class="ct-vignette absolute inset-0 pointer-events-none"></div>
    </div>

    <!-- Theme Toggle Floating in Top Right -->
    <div class="absolute top-5 right-5 z-30">
        <button type="button" 
                onclick="toggleTheme()" 
                class="ct-theme-btn h-9 px-3.5 rounded-xl inline-flex items-center gap-2 shadow-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#dc143c]/40"
                title="Ganti tampilan terang / gelap"
                aria-label="Ganti tampilan terang dan gelap">
            <!-- Shown in Light Mode -->
            <span class="inline-flex items-center gap-1.5 dark:hidden">
                <i class="fa-solid fa-sun text-sm text-amber-500"></i>
                <span class="text-xs font-medium text-slate-700">Light</span>
            </span>
            <!-- Shown in Dark Mode -->
            <span class="hidden dark:inline-flex items-center gap-1.5">
                <i class="fa-solid fa-moon text-sm text-amber-300"></i>
                <span class="text-xs font-medium text-white/90">Dark</span>
            </span>
        </button>
    </div>

    <!-- Main Glassmorphic Card Container -->
    <div class="w-full max-w-xl lg:max-w-4xl relative z-20 my-6">
        <div class="glass-panel rounded-[28px] sm:rounded-[34px] p-6 sm:p-9 lg:p-12 relative overflow-hidden">
            
            <!-- Specular Edge Accent -->
            <div class="ct-specular absolute -top-24 -left-24 w-48 h-48 rounded-full blur-2xl pointer-events-none"></div>

            <div class="grid grid-cols-1 grid-rows-[auto_1fr_auto] lg:grid-cols-12 lg:grid-rows-1 gap-8 lg:gap-10 items-center">
                
                <!-- Left Section: Brand & Headline Typography -->
                <div class="contents text-left lg:col-span-6 lg:flex lg:flex-col lg:justify-between lg:space-y-6">
                    
                    <div class="order-1 space-y-6 lg:order-none">
                        <!-- Decorative Top Metadata Tags -->
                        <div class="flex items-center justify-between text-xs tracking-wider font-medium">
                            <div class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#ff2a55] animate-pulse"></span>
                                <span class="ct-topbar-brand uppercase font-semibold tracking-wider">CLEANTRACK</span>
                            </div>
                            <span class="ct-topbar-label text-[11px] uppercase tracking-widest">Monitoring Kebersihan</span>
                        </div>

                        <!-- CleanTrack Brand Icon Badge -->
                        <div class="inline-flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#8a001a] via-[#dc143c] to-[#ff4d6d] p-0.5 shadow-[0_0_20px_rgba(220,20,60,0.5)]">
                                <div class="w-full h-full bg-[#120206]/60 backdrop-blur-xs rounded-[14px] flex items-center justify-center">
                                    <i class="fa-solid fa-broom-ball text-xl text-white"></i>
                                </div>
                            </div>
                            <div>
                                <span class="font-heading text-xl font-bold tracking-tight block leading-tight ct-text-primary">
                                    CLEAN<span class="text-[#ff3b5c]">TRACK</span>
                                </span>
                                <span class="text-[11px] ct-text-muted font-medium tracking-wide">Enterprise Hygiene System</span>
                            </div>
                        </div>

                        <!-- Main Bold Headline -->
                        <div class="pt-2">
                            <h1 class="font-heading text-3xl sm:text-4xl lg:text-[42px] font-extrabold tracking-tight leading-[1.12] ct-text-primary">
                                Clean Spaces,<br>
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#ff4d6d] via-[#dc143c] to-[#c9002b]">Better Standards.</span>
                            </h1>
                            <p class="text-xs sm:text-sm ct-text-secondary mt-3.5 leading-relaxed font-normal max-w-md">
                                Sistem Monitoring Kebersihan untuk pemantauan area yang lebih teratur, terukur, dan efisien.
                            </p>
                        </div>
                    </div>

                    <div class="order-3 space-y-6 lg:order-none">
                        <!-- Key Feature Highlights (Badges) -->
                        <div class="pt-1 flex flex-wrap gap-2 text-[11px] font-medium">
                            <span class="ct-badge inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full backdrop-blur-sm">
                                <i class="fa-solid fa-qrcode text-[#dc143c]"></i>
                                <span>Audit QR Code</span>
                            </span>
                            <span class="ct-badge inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full backdrop-blur-sm">
                                <i class="fa-solid fa-clock-rotate-left text-[#dc143c]"></i>
                                <span>Log Real-Time</span>
                            </span>
                            <span class="ct-badge inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full backdrop-blur-sm">
                                <i class="fa-solid fa-vr-cardboard text-[#dc143c]"></i>
                                <span>Galeri Foto Ruangan</span>
                            </span>
                        </div>

                        <!-- Footer Tag -->
                        <div class="pt-2 ct-footer-border border-t flex items-center justify-between text-[11px] ct-text-faint">
                            <span>&copy; {{ date('Y') }} CleanTrack Mhd.</span>
                            <span class="tracking-wide">Standard Compliance</span>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Login Form Panel -->
                <div class="order-2 self-center lg:order-none lg:col-span-6">
                    <div class="glass-subpanel rounded-2xl sm:rounded-3xl p-6 sm:p-7 relative">
                        
                        <div class="mb-5">
                            <h2 class="font-heading text-xl font-bold tracking-tight ct-text-primary">Masuk ke Sistem</h2>
                            <p class="text-xs ct-text-muted mt-1">Masukkan kredensial akun Anda untuk mengakses portal</p>
                        </div>

                        <!-- Flash Error Notification -->
                        @if(session('error'))
                            <div class="ct-alert-error mb-4 p-3 rounded-xl backdrop-blur-md flex items-center gap-2.5 text-xs shadow-md">
                                <i class="ct-alert-icon fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- Flash Success Notification -->
                        @if(session('success'))
                            <div class="ct-alert-success mb-4 p-3 rounded-xl backdrop-blur-md flex items-center gap-2.5 text-xs shadow-md">
                                <i class="ct-alert-icon fa-solid fa-circle-check text-sm shrink-0"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Email Input -->
                            <div>
                                <label for="email" class="block text-xs font-semibold ct-label mb-1.5 tracking-wide">
                                    Alamat Email
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none ct-input-icon">
                                        <i class="fa-regular fa-envelope text-sm"></i>
                                    </div>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email"
                                           placeholder="Masukkan alamat email"
                                           class="glass-input pl-10 pr-3.5 py-2.5 @error('email') !border-red-500 ring-1 ring-red-400 @enderror">
                                </div>
                                @error('email')
                                    <p class="text-[11px] ct-field-error mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="password" class="block text-xs font-semibold ct-label mb-1.5 tracking-wide">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none ct-input-icon">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Masukkan kata sandi"
                                           class="glass-input pl-10 pr-10 py-2.5 @error('password') !border-red-500 ring-1 ring-red-400 @enderror">
                                    <button type="button" 
                                            onclick="togglePasswordVisibility()" 
                                            class="ct-pw-toggle absolute inset-y-0 right-0 pr-3.5 flex items-center transition-colors cursor-pointer"
                                            aria-label="Lihat kata sandi">
                                        <i class="fa-regular fa-eye text-sm" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-[11px] ct-field-error mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" 
                                           name="remember" 
                                           class="ct-checkbox w-4 h-4 rounded border focus:ring-[#dc143c] focus:ring-offset-0 cursor-pointer">
                                    <span class="text-xs ct-remember">Ingat saya di perangkat ini</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    class="w-full btn-crimson-glow text-white font-semibold text-sm rounded-xl py-3 px-4 flex items-center justify-center gap-2 cursor-pointer mt-2">
                                <span>Masuk ke Sistem</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
