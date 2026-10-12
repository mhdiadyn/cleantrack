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

    <!-- Google Fonts Inter / Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
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
<body class="h-full antialiased text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 selection:bg-red-600 selection:text-white transition-colors duration-200" x-data="{ sidebarOpen: false, userDropdown: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden">
        </div>

        <!-- Sidebar Navigation (Merah Putih Responsive Light/Dark Mode) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-slate-200 dark:border-slate-800 shadow-xl lg:shadow-none no-print">
            
            <!-- Brand Header -->
            <div class="h-18 px-6 flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-950/50">
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('petugas.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 flex items-center justify-center text-white shadow-lg shadow-red-600/30 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-broom-ball text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white">CLEAN<span class="text-red-600 dark:text-red-500">TRACK</span></span>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold tracking-wider uppercase flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Monitoring Kebersihan
                        </p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- User Badge Box -->
            <div class="p-4 mx-4 my-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</h4>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full {{ Auth::user()->role === 'admin' ? 'bg-red-500 dark:bg-red-400' : 'bg-emerald-500 dark:bg-emerald-400' }}"></span>
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Petugas Kebersihan' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-2 space-y-1">
                <div class="px-3 pb-2 pt-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Menu Utama</div>

                @if(Auth::user()->role === 'admin')
                    <!-- ADMIN MENU -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('floorplan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('floorplan.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot w-5 text-center {{ request()->routeIs('floorplan.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span class="flex-1">Denah Fasilitas</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-300 font-bold uppercase">Interaktif</span>
                    </a>

                    <a href="{{ route('gallery.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('gallery.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-images w-5 text-center {{ request()->routeIs('gallery.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span class="flex-1">Galeri & 360°</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 font-bold uppercase">360°</span>
                    </a>

                    <a href="{{ route('monitoring.create') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('monitoring.create') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-check w-5 text-center {{ request()->routeIs('monitoring.create') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Pemeriksaan Baru</span>
                    </a>

                    <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Master Data</div>

                    <a href="{{ route('admin.areas.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.areas.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-layer-group w-5 text-center {{ request()->routeIs('admin.areas.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Data Area Ruangan</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Data Pengguna</span>
                    </a>

                    <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Monitoring & Laporan</div>

                    <a href="{{ route('admin.monitoring.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.monitoring.index') || request()->routeIs('monitoring.index') || request()->routeIs('monitoring.show') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center {{ request()->routeIs('admin.monitoring.index') || request()->routeIs('monitoring.index') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Riwayat Monitoring</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-file-lines w-5 text-center {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Laporan Kebersihan</span>
                    </a>

                @else
                    <!-- PETUGAS MENU -->
                    <a href="{{ route('petugas.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('petugas.dashboard') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('petugas.dashboard') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('floorplan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('floorplan.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-map-location-dot w-5 text-center {{ request()->routeIs('floorplan.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span class="flex-1">Denah Fasilitas</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-300 font-bold uppercase">Interaktif</span>
                    </a>

                    <a href="{{ route('gallery.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('gallery.*') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-images w-5 text-center {{ request()->routeIs('gallery.*') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span class="flex-1">Galeri & 360°</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 font-bold uppercase">360°</span>
                    </a>

                    <a href="{{ route('monitoring.create') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('monitoring.create') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-circle-plus w-5 text-center {{ request()->routeIs('monitoring.create') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Pemeriksaan Kebersihan</span>
                    </a>

                    <a href="{{ route('petugas.my-monitoring') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('petugas.my-monitoring') || request()->routeIs('monitoring.show') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                        <i class="fa-solid fa-list-check w-5 text-center {{ request()->routeIs('petugas.my-monitoring') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                        <span>Riwayat Saya</span>
                    </a>
                @endif

                <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pengaturan Akun</div>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-slate-400 dark:text-red-400' }}"></i>
                    <span>Profil Saya</span>
                </a>
            </div>

            <!-- Footer / Logout -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white border border-rose-200 dark:border-rose-500/20 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar Merah Putih -->
            <header class="h-18 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs transition-colors duration-200 no-print">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">@yield('header_title', 'CleanTrack')</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">@yield('header_subtitle', 'Sistem Monitoring Kebersihan Gedung & Fasilitas')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Date info -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-medium border border-slate-200/60 dark:border-slate-700/60">
                        <i class="fa-regular fa-calendar-check text-red-600 dark:text-red-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <!-- Dark / Light Mode Toggle Button -->
                    <button type="button" 
                            onclick="toggleTheme()" 
                            class="p-2 w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-amber-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 flex items-center justify-center transition-all duration-200 focus:outline-none shadow-2xs"
                            title="Ganti Mode Terang / Gelap"
                            aria-label="Toggle Dark / Light Mode">
                        <i class="fa-solid fa-sun text-base text-amber-400 hidden dark:block transition-transform hover:rotate-45"></i>
                        <i class="fa-solid fa-moon text-base text-slate-700 block dark:hidden transition-transform hover:-rotate-12"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-red-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ Auth::user()->name }}</span>
                                <span class="block text-[11px] text-red-600 dark:text-red-400 font-semibold">{{ ucfirst(Auth::user()->role) }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dark:text-slate-500 hidden sm:block"></i>
                        </button>

                        <div x-show="open" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <button type="button" 
                                    onclick="toggleTheme()" 
                                    class="w-full text-left flex items-center justify-between px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-half-stroke text-slate-400 dark:text-slate-400 w-4"></i>
                                    <span>Ganti Tema</span>
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                    <span class="dark:hidden">Light</span>
                                    <span class="hidden dark:inline">Dark</span>
                                </span>
                            </button>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <i class="fa-solid fa-user-pen text-slate-400 w-4"></i> Edit Profil
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div class="max-w-7xl mx-auto space-y-6">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 p-4 shadow-xs flex items-center justify-between no-print">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-300">Berhasil</h4>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-200 p-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 p-4 shadow-xs flex items-center justify-between no-print">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-rose-900 dark:text-rose-300">Perhatian</h4>
                                    <p class="text-xs text-rose-700 dark:text-rose-400 mt-0.5">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-500 hover:text-rose-800 dark:hover:text-rose-200 p-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 p-4 shadow-xs no-print">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-rose-900 dark:text-rose-300">
                                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    <span>Terdapat kesalahan pengisian data:</span>
                                </div>
                                <button @click="show = false" class="text-rose-500 hover:text-rose-800 dark:hover:text-rose-200 p-1">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <ul class="mt-2 list-disc list-inside text-xs text-rose-700 dark:text-rose-400 space-y-0.5 ml-1">
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
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 dark:bg-black/85 backdrop-blur-sm no-print">
        <div @click.outside="open = false" class="relative max-w-4xl w-full bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-transparent dark:border-slate-800">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100" x-text="title">Preview Foto</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-4 bg-slate-950 flex items-center justify-center max-h-[75vh] overflow-auto">
                <img :src="src" :alt="title" class="max-h-[70vh] w-auto object-contain rounded-lg">
            </div>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
