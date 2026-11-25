<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CleanTrack | Sistem Monitoring Kebersihan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Theme Detection (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: Poppins (SemiBold/Bold) for Headlines, Inter for Body & UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 (Line Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #fbf9f8;
            color: #1b1c1c;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        [x-cloak] { display: none !important; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body class="h-full antialiased bg-[#fbf9f8] text-[#1b1c1c] dark:bg-[#161717] dark:text-[#f2f0f0] selection:bg-[#dc143c] selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#1b1c1c]/50 z-40 lg:hidden">
        </div>

        <!-- Sidebar Navigation (Technolife Corporate Clean) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-[#1f2020] text-[#1b1c1c] dark:text-[#f2f0f0] flex flex-col transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 border-r border-[#eae8e7] dark:border-[#2e3030] shadow-sm lg:shadow-none no-print">
            
            <!-- Brand Header -->
            <div class="h-16 px-5 flex items-center justify-between border-b border-[#eae8e7] dark:border-[#2e3030]">
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#dc143c] text-white flex items-center justify-center text-base shrink-0 shadow-xs">
                        <i class="fa-solid fa-broom-ball"></i>
                    </div>
                    <div>
                        <div class="font-heading font-semibold text-base tracking-tight text-[#1b1c1c] dark:text-white">
                            <span class="text-[#1b1c1c] dark:text-white">CLEAN</span><span class="text-[#dc143c]">TRACK</span>
                        </div>
                        <p class="text-[10px] text-[#5c3f3f] dark:text-[#c8c6c5] font-medium tracking-wide uppercase">
                            Monitoring Kebersihan
                        </p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-[#5c3f3f] hover:text-[#1b1c1c] dark:hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1 text-sm">
                @if(Auth::user()->role === 'admin')
                    <div class="px-3 pb-1.5 pt-2 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Menu Utama</div>
                    <!-- ADMIN MENU -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('facility.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('facility.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot w-4 text-center {{ request()->routeIs('facility.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span class="flex-1">Denah & Galeri Ruangan</span>
                    </a>

                    <a href="{{ route('monitoring.create') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('monitoring.create') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-check w-4 text-center {{ request()->routeIs('monitoring.create') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Pemeriksaan Baru</span>
                    </a>

                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Master Data</div>

                    <a href="{{ route('admin.areas.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('admin.areas.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-layer-group w-4 text-center {{ request()->routeIs('admin.areas.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Data Area Ruangan</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('admin.users.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Data Pengguna</span>
                    </a>

                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Monitoring & Laporan</div>

                    <a href="{{ route('admin.monitoring.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('admin.monitoring.index') || request()->routeIs('monitoring.index') || request()->routeIs('monitoring.show') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('admin.monitoring.index') || request()->routeIs('monitoring.index') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Riwayat Monitoring</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-file-lines w-4 text-center {{ request()->routeIs('admin.reports.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Laporan Kebersihan</span>
                    </a>

                @else
                    <!-- PETUGAS MENU -->
                    <div class="px-3 pb-1.5 pt-2 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Menu Utama</div>

                    <a href="{{ route('petugas.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('petugas.dashboard') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-4 text-center {{ request()->routeIs('petugas.dashboard') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('facility.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('facility.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot w-4 text-center {{ request()->routeIs('facility.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span class="flex-1">Denah & Galeri Ruangan</span>
                    </a>

                    <a href="{{ route('petugas.scan') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('petugas.scan') || request()->routeIs('scan.*') || request()->routeIs('petugas.room.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-qrcode w-4 text-center {{ request()->routeIs('petugas.scan') || request()->routeIs('scan.*') || request()->routeIs('petugas.room.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span class="flex-1">Scan QR Ruangan</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#dc143c] text-white font-semibold uppercase tracking-wider">Scan</span>
                    </a>

                    <a href="{{ route('monitoring.create') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('monitoring.create') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-circle-plus w-4 text-center {{ request()->routeIs('monitoring.create') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Pemeriksaan Kebersihan</span>
                    </a>

                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Monitoring &amp; Laporan</div>

                    <a href="{{ route('petugas.my-monitoring') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('petugas.my-monitoring') || (request()->routeIs('monitoring.show') && auth()->user()->role === 'petugas') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-list-check w-4 text-center {{ request()->routeIs('petugas.my-monitoring') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Riwayat Saya</span>
                    </a>

                    <a href="{{ route('petugas.reports.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('petugas.reports.*') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                        <i class="fa-solid fa-file-lines w-4 text-center {{ request()->routeIs('petugas.reports.*') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                        <span>Laporan Kebersihan</span>
                    </a>
                @endif

                <div class="pt-4 pb-1.5 px-3 text-[11px] font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Pengaturan Akun</div>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-colors duration-150 {{ request()->routeIs('profile.edit') ? 'bg-[#fff1f0] text-[#dc143c] font-semibold border-l-2 border-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#1b1c1c] dark:hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.edit') ? 'text-[#dc143c] dark:text-[#ffb3b3]' : 'text-[#5c3f3f] dark:text-[#c8c6c5]' }}"></i>
                    <span>Profil Saya</span>
                </a>
            </div>

            <!-- Footer: User Profile Dropdown & Logout -->
            <div class="p-3 border-t border-[#eae8e7] dark:border-[#2e3030] space-y-2 shrink-0">
                <!-- User Profile Dropdown / Dropup -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" 
                            type="button"
                            class="w-full flex items-center justify-between p-2 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] hover:bg-[#eae8e7] dark:hover:bg-[#333535] border border-[#eae8e7] dark:border-[#333535] transition-colors text-left cursor-pointer group"
                            title="Menu Profil"
                            aria-label="Menu Profil">
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if (Auth::user()->profile_photo_url)
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto profil {{ Auth::user()->name }}"
                                     class="h-9 w-9 shrink-0 rounded-lg object-cover shadow-xs">
                            @else
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#dc143c] text-xs font-bold text-white shadow-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold text-[#1b1c1c] dark:text-white leading-tight truncate">{{ Auth::user()->name }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ Auth::user()->role === 'admin' ? 'bg-[#dc143c]' : 'bg-[#00822d]' }}"></span>
                                    <span class="text-[11px] font-medium text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider truncate">
                                        {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Petugas' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-up text-[10px] text-[#5c3f3f] dark:text-[#c8c6c5] group-hover:text-[#1b1c1c] dark:group-hover:text-white ml-2 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropup Menu Content -->
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="transform opacity-0 translate-y-2 scale-95"
                         class="absolute bottom-full left-0 right-0 mb-2 rounded-xl bg-white dark:bg-[#1f2020] shadow-xl border border-[#eae8e7] dark:border-[#2e3030] py-1.5 z-50">
                        <div class="px-4 py-2 border-b border-[#eae8e7] dark:border-[#2e3030]">
                            <p class="text-xs font-semibold text-[#1b1c1c] dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] truncate">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-[#1b1c1c] dark:text-[#f2f0f0] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] hover:text-[#dc143c] transition-colors">
                            <i class="fa-solid fa-user-pen text-[#5c3f3f] dark:text-[#c8c6c5] w-4"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#ba1a1a] dark:text-[#ffb4ab] hover:bg-[#ffdad6]/40 dark:hover:bg-[#410002]/40 border border-[#eae8e7] dark:border-[#2e3030] transition-colors cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar (Technolife Corporate Clean) -->
            <header class="h-16 bg-white dark:bg-[#1f2020] border-b border-[#eae8e7] dark:border-[#2e3030] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 transition-colors duration-150 no-print">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-[#5c3f3f] hover:text-[#1b1c1c] dark:text-[#c8c6c5] dark:hover:text-white hover:bg-[#f5f3f3] dark:hover:bg-[#262727]">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h1 class="font-heading text-base sm:text-lg font-semibold text-[#1b1c1c] dark:text-white leading-tight">
                            @yield('header_title', 'CleanTrack')
                        </h1>
                        <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] hidden sm:block">
                            @yield('header_subtitle', 'Sistem Monitoring Kebersihan Gedung & Fasilitas')
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Date info -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] text-xs font-medium border border-[#eae8e7] dark:border-[#333535]">
                        <i class="fa-regular fa-calendar-check text-[#dc143c]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <!-- Dark / Light Mode Toggle Button -->
                    <button type="button" 
                            onclick="toggleTheme()" 
                            class="h-9 px-3 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] hover:bg-[#eae8e7] dark:hover:bg-[#333535] border border-[#eae8e7] dark:border-[#333535] inline-flex items-center gap-2 transition-colors focus:outline-none cursor-pointer shrink-0"
                            title="Ganti tampilan terang / gelap"
                            aria-label="Ganti tampilan terang dan gelap">
                        <span class="inline-flex items-center gap-1.5 dark:hidden">
                            <i class="fa-solid fa-sun text-sm text-amber-500"></i>
                            <span class="text-xs font-medium text-[#1b1c1c]">Light</span>
                        </span>
                        <span class="hidden dark:inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-moon text-sm text-amber-300"></i>
                            <span class="text-xs font-medium text-[#f2f0f0]">Dark</span>
                        </span>
                    </button>
                </div>
            </header>

            <!-- Main Page Content (Contained within 1280px / 8-point grid) -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div class="max-w-[1280px] mx-auto space-y-6">

                    <!-- Flash Messages (Semantic Colors) -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-[#daffd5] dark:bg-[#003912] border border-[#b2e5be] dark:border-[#00531a] p-4 flex items-center justify-between no-print">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#00822d] text-white flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[#006622] dark:text-[#83fc8e]">Berhasil</h4>
                                    <p class="text-xs text-[#006622] dark:text-[#83fc8e] mt-0.5">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-[#006622] dark:text-[#83fc8e] hover:opacity-75 p-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-[#ffdad6] dark:bg-[#410002] border border-[#ffb4ab] dark:border-[#93000a] p-4 flex items-center justify-between no-print">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#ba1a1a] text-white flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-[#ba1a1a] dark:text-[#ffb4ab]">Perhatian</h4>
                                    <p class="text-xs text-[#ba1a1a] dark:text-[#ffb4ab] mt-0.5">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-[#ba1a1a] dark:text-[#ffb4ab] hover:opacity-75 p-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-[#ffdad6] dark:bg-[#410002] border border-[#ffb4ab] dark:border-[#93000a] p-4 no-print">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-[#ba1a1a] dark:text-[#ffb4ab]">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>Terdapat kesalahan pengisian data:</span>
                                </div>
                                <button @click="show = false" class="text-[#ba1a1a] dark:text-[#ffb4ab] hover:opacity-75 p-1">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <ul class="mt-2 list-disc list-inside text-xs text-[#ba1a1a] dark:text-[#ffb4ab] space-y-0.5 ml-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Image Modal for Fullscreen Preview -->
    <div x-data="{ open: false, src: '', title: '' }"
         @open-image-modal.window="open = true; src = $event.detail.src; title = $event.detail.title"
         x-show="open"
         x-cloak
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1b1c1c]/70 dark:bg-black/80 no-print">
        <div @click.outside="open = false" class="relative max-w-4xl w-full bg-white dark:bg-[#1f2020] rounded-xl overflow-hidden shadow-xl border border-[#eae8e7] dark:border-[#2e3030]">
            <div class="flex items-center justify-between px-5 py-3 border-b border-[#eae8e7] dark:border-[#2e3030] bg-[#f5f3f3] dark:bg-[#262727]">
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white" x-text="title">Preview Foto</h3>
                <button @click="open = false" class="text-[#5c3f3f] hover:text-[#1b1c1c] dark:hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-4 bg-[#1b1c1c] flex items-center justify-center max-h-[75vh] overflow-auto">
                <img :src="src" :alt="title" class="max-h-[70vh] w-auto object-contain rounded-lg">
            </div>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
