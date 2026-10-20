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

    <!-- Google Fonts: Poppins (SemiBold) for Headlines, Inter for Body & UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; font-weight: 600; }
    </style>
</head>
<body class="h-full antialiased bg-[#fbf9f8] text-[#1b1c1c] dark:bg-[#161717] dark:text-[#f2f0f0] flex items-center justify-center p-4 relative transition-colors duration-150">
    
    <!-- Theme Toggle Floating in Top Right -->
    <div class="absolute top-5 right-5 z-20">
        <button type="button" 
                onclick="toggleTheme()" 
                class="p-2 w-9 h-9 rounded-xl bg-white dark:bg-[#1f2020] text-[#5c3f3f] dark:text-[#ffb3b3] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] border border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-center transition-colors shadow-xs cursor-pointer"
                title="Ganti Mode Terang / Gelap"
                aria-label="Toggle Theme">
            <i class="fa-solid fa-sun text-sm text-amber-500 hidden dark:block"></i>
            <i class="fa-solid fa-moon text-sm text-[#5c3f3f] block dark:hidden"></i>
        </button>
    </div>

    <div class="w-full max-w-md relative z-10 my-8">
        
        <!-- Brand Logo & Title (Technolife Corporate Clean) -->
        <div class="text-center mb-8 flex flex-col items-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#dc143c] text-white shadow-xs mb-3">
                <i class="fa-solid fa-broom-ball text-xl"></i>
            </div>
            <h1 class="font-heading text-2xl font-semibold tracking-tight text-[#1b1c1c] dark:text-white">
                CLEAN<span class="text-[#dc143c]">TRACK</span>
            </h1>
            <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-1 font-medium tracking-wider uppercase">
                Sistem Monitoring Kebersihan
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-[#1f2020] rounded-xl shadow-[0px_4px_12px_rgba(0,0,0,0.05)] p-6 sm:p-8 border border-[#eae8e7] dark:border-[#2e3030]">
            <div class="mb-6">
                <h2 class="font-heading text-lg font-semibold text-[#1b1c1c] dark:text-white">Masuk ke Akun</h2>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-1">Masukkan email dan kata sandi Anda untuk mengakses sistem</p>
            </div>

            <!-- Flash Error Notification -->
            @if(session('error'))
                <div class="mb-5 p-3 rounded-xl bg-[#ffdad6] dark:bg-[#410002] border border-[#ffb4ab] dark:border-[#93000a] flex items-center gap-2.5 text-[#ba1a1a] dark:text-[#ffb4ab] text-xs">
                    <i class="fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3 rounded-xl bg-[#daffd5] dark:bg-[#003912] border border-[#b2e5be] dark:border-[#00531a] flex items-center gap-2.5 text-[#006622] dark:text-[#83fc8e] text-xs">
                    <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="label-clean">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5c3f3f] dark:text-[#8c8282]">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="nama@cleantrack.test"
                               class="input-clean pl-10 @error('email') border-[#ba1a1a] @enderror">
                    </div>
                    @error('email')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="label-clean">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5c3f3f] dark:text-[#8c8282]">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Masukkan Password"
                               class="input-clean pl-10 pr-10 @error('password') border-[#ba1a1a] @enderror">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#5c3f3f] hover:text-[#1b1c1c] dark:hover:text-white cursor-pointer"
                                aria-label="Lihat kata sandi">
                            <i class="fa-regular fa-eye text-sm" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#dc143c] focus:ring-[#dc143c] border-[#eae8e7] dark:border-[#333535] dark:bg-[#1f2020]">
                        <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button (Solid Crimson, No Gradient) -->
                <button type="submit" 
                        class="w-full btn-primary btn-primary-lg mt-2">
                    <span>Masuk ke Sistem</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-[#5c3f3f] dark:text-[#8c8282] mt-6">
            &copy; {{ date('Y') }} CleanTrack &bull; Sistem Monitoring Kebersihan
        </p>
    </div>

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
