<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QCC Assessment') - Konvensi Mutu & Kaizen</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN fallback + modern styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        corporate: {
                            50: '#f0f7ff',
                            500: '#0055b8',
                            600: '#004399',
                            900: '#0b2545',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom touch friendly slider styling */
        input[type="range"] {
            accent-color: #0055b8;
        }
        .touch-action-manipulation {
            touch-action: manipulation;
        }

        /* Desktop Expandable Rail Navigation Styles */
        .sidebar-text {
            opacity: 0;
            max-width: 0;
            white-space: nowrap;
            overflow: hidden;
            pointer-events: none;
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #desktopSidebar:hover .sidebar-text,
        #desktopSidebar.is-pinned .sidebar-text {
            opacity: 1;
            max-width: 190px;
            pointer-events: auto;
        }
        #desktopSidebar.is-pinned {
            width: 16rem !important; /* 256px */
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.1);
        }
        #mainContentWrapper.is-pinned {
            padding-left: 16rem !important;
        }
        #desktopSidebar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        /* TomSelect Modern Styling */
        .ts-wrapper.single .ts-control {
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.45rem 0.75rem !important;
            font-size: 0.8125rem !important;
            background-color: #ffffff !important;
            min-height: 38px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.15s ease-in-out !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .ts-dropdown {
            border-radius: 0.875rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.15) !important;
            padding: 0.35rem !important;
            font-size: 0.8125rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            z-index: 9999 !important;
            background-color: #ffffff !important;
        }
        .ts-dropdown .option {
            border-radius: 0.5rem !important;
            padding: 0.45rem 0.75rem !important;
            color: #334155 !important;
            cursor: pointer !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            font-weight: 600 !important;
        }
        .ts-dropdown .option.selected {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            font-weight: 700 !important;
        }
    </style>
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.default.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white bg-slate-50">

    <!-- DESKTOP EXPANDABLE SIDEBAR (Rail minimized w-20, expands to w-64 on cursor hover) -->
    <aside id="desktopSidebar"
        class="hidden md:flex fixed top-0 bottom-0 left-0 z-50 flex-col bg-white border-r border-slate-200/90 shadow-sm hover:shadow-2xl transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] group/sidebar w-20 hover:w-64 overflow-x-hidden overflow-y-auto select-none"
        aria-label="Sidebar Navigasi Utama">
        
        <!-- Sidebar Brand Header -->
        <div class="h-20 flex-shrink-0 flex items-center px-4.5 border-b border-slate-100/90 justify-between">
            <a href="{{ route('qcc.index') }}" class="flex items-center gap-3 w-full overflow-hidden">
                <!-- Logo Badge QC -->
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-sky-400 flex items-center justify-center text-white shadow-md shadow-blue-500/25 font-black text-lg tracking-tight flex-shrink-0">
                    QC
                </div>
                <!-- Brand Title (Appears on Hover / Expand) -->
                <div class="sidebar-text flex flex-col justify-center">
                    <div class="font-extrabold text-slate-900 tracking-tight text-base leading-tight flex items-center gap-1.5">
                        <span>QCC System</span>
                        <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-700">{{ $activeYear === 'all' ? 'SEMUA' : $activeYear }}</span>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 truncate">PT Bekaert Indonesia</span>
                </div>
            </a>

            <!-- Pin / Lock Sidebar Toggle Button (Only visible on hover) -->
            <button type="button" id="pinSidebarBtn" onclick="togglePinSidebar()" 
                class="sidebar-text p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition ml-auto flex-shrink-0"
                title="Kunci Sidebar Terbuka / Minimize">
                <svg id="pinIcon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links Container -->
        <div class="flex-1 py-4 px-3 space-y-1.5 overflow-y-auto overflow-x-hidden">
            
            <!-- Section: Navigasi Utama -->
            <div class="px-2 pt-1 pb-1">
                <span class="sidebar-text text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                    Menu Utama
                </span>
                <div class="w-8 h-0.5 bg-slate-200 rounded mx-auto group-hover/sidebar:hidden"></div>
            </div>

            <!-- Item 1: Dashboard Circle -->
            <a href="{{ route('qcc.index') }}" 
                class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('qcc.index') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                title="Dashboard Circle">
                <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div class="sidebar-text">
                    <span class="text-sm block leading-tight">Dashboard Circle</span>
                    <span class="text-[10px] opacity-75 block font-normal">Daftar Circle Peserta</span>
                </div>
            </a>

            <!-- Item 2: Leaderboard & Hasil -->
            <a href="{{ route('qcc.leaderboard') }}" 
                class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('qcc.leaderboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                title="Leaderboard & Hasil">
                <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="sidebar-text">
                    <span class="text-sm block leading-tight">Leaderboard & Hasil</span>
                    <span class="text-[10px] opacity-75 block font-normal">Peringkat & Medali</span>
                </div>
            </a>

            <!-- Section: Panel Admin (Only for Admin) -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="pt-4 px-2 pb-1 border-t border-slate-100 my-2">
                    <span class="sidebar-text text-[10px] font-bold uppercase tracking-wider text-indigo-500 block mb-1">
                        Panel Administrator
                    </span>
                    <div class="w-8 h-0.5 bg-indigo-200 rounded mx-auto group-hover/sidebar:hidden"></div>
                </div>

                <!-- Item 3: Kelola User & Role -->
                <a href="{{ route('admin.users.index') }}" 
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-blue-700 to-indigo-600 text-white shadow-md shadow-blue-600/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                    title="Manajemen Pengguna & Multi-Role">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <span class="text-sm block leading-tight">Kelola User & Role</span>
                        <span class="text-[10px] opacity-75 block font-normal">Semua Pengguna, Juri & Akses</span>
                    </div>
                </a>

                <!-- Item 4: Penugasan Juri -->
                <a href="{{ route('admin.judges.index') }}" 
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('admin.judges.*') ? 'bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-600/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                    title="Penugasan Kategori Juri">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <span class="text-sm block leading-tight">Penugasan Juri</span>
                        <span class="text-[10px] opacity-75 block font-normal">Plotting Kategori Konvensi</span>
                    </div>
                </a>

                <!-- Item 4: Kriteria Nilai -->
                <a href="{{ route('admin.criteria.index') }}" 
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('admin.criteria.*') ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                    title="Kriteria & Rubrik Penilaian">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <span class="text-sm block leading-tight">Kriteria Nilai</span>
                        <span class="text-[10px] opacity-75 block font-normal">Bobot & Rubrik PDCA</span>
                    </div>
                </a>

                <!-- Item 5: Kelola Circle QCC -->
                <a href="{{ route('admin.circles.index') }}" 
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('admin.circles.*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-md shadow-teal-600/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                    title="Manajemen Circle QCC">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <span class="text-sm block leading-tight">Kelola Circle QCC</span>
                        <span class="text-[10px] opacity-75 block font-normal">Data Tim & Jadwal</span>
                    </div>
                </a>

                <!-- Item 6: Pengaturan Sistem -->
                <a href="{{ route('admin.settings.index') }}" 
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-2xl transition duration-150 {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-slate-800 to-indigo-900 text-white shadow-md shadow-slate-900/25 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}"
                    title="Pengaturan Sistem & Batas Deviasi">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="sidebar-text">
                        <span class="text-sm block leading-tight">Pengaturan</span>
                        <span class="text-[10px] opacity-75 block font-normal">Batas Deviasi & Sistem</span>
                    </div>
                </a>
            @endif

        </div>

        <!-- Sidebar Bottom Footer: Profile & Authentication -->
        <div class="p-3 border-t border-slate-200/80 bg-slate-50/80 flex-shrink-0">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="flex items-center justify-between gap-2 overflow-hidden">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-700 to-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs flex-shrink-0 relative">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-white absolute -top-0.5 -right-0.5"></span>
                        </div>
                        <div class="sidebar-text min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate leading-tight">{{ Auth::user()->name }}</span>
                            <span class="text-[9px] uppercase font-black px-1.5 py-0.2 rounded bg-indigo-100 text-indigo-700 inline-block mt-0.5">Admin</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="sidebar-text flex-shrink-0">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Sign Out Administrator">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-600 hover:from-blue-600 hover:to-indigo-500 text-white font-bold text-xs shadow-sm transition"
                    title="Sign In Administrator">
                    <div class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <span class="sidebar-text">Sign In Admin</span>
                </a>
            @endif
        </div>

    </aside>

    <!-- MOBILE TOP BAR (For touch devices & screens < md) -->
    <header class="md:hidden sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 h-16 flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2.5">
            <!-- Mobile Menu Trigger -->
            <button type="button" onclick="openMobileSidebar()" class="p-2 -ml-1 rounded-xl text-slate-700 hover:bg-slate-100 transition active:scale-95" aria-label="Buka Menu Navigasi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Brand Logo -->
            <a href="{{ route('qcc.index') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-sky-400 flex items-center justify-center text-white shadow-xs font-black text-sm">
                    QC
                </div>
                <div>
                    <span class="font-extrabold text-slate-900 text-sm tracking-tight block leading-none">QCC System</span>
                    <span class="text-[9px] font-bold text-slate-400 block mt-0.5">Konvensi {{ $activeYear === 'all' ? 'Semua' : $activeYear }}</span>
                </div>
            </a>
        </div>

        <!-- Right Quick Action (Year Selector & Profile / Sign In) -->
        <div class="flex items-center gap-2">
            <!-- Switch Year Selector Mobile -->
            <form method="POST" action="{{ route('qcc.switch-year') }}" class="inline-block">
                @csrf
                <div class="relative">
                    <select name="year" onchange="this.form.submit()" 
                        class="no-search appearance-none bg-slate-100 text-slate-800 text-[11px] font-bold py-1 pl-2 pr-6 rounded-lg border border-slate-200 focus:outline-none">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (string)$activeYear === (string)$yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                        <option value="all" {{ (string)$activeYear === 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-slate-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </form>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="flex items-center gap-1.5 px-2 py-1 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="max-w-[70px] truncate text-[11px]">{{ Auth::user()->name }}</span>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-2.5 py-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-xs transition">
                    Sign In
                </a>
            @endif
        </div>
    </header>

    <!-- MOBILE DRAWER BACKDROP & SLIDE-OVER -->
    <div id="mobileBackdrop" onclick="closeMobileSidebar()" 
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300 hidden opacity-0 md:hidden" aria-hidden="true"></div>

    <div id="mobileDrawerPanel" 
        class="fixed top-0 bottom-0 left-0 z-50 w-72 max-w-[85vw] bg-white shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-out md:hidden">
        
        <!-- Mobile Drawer Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-sky-400 flex items-center justify-center text-white shadow-sm font-black text-base">
                    QC
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm leading-tight">QCC Assessment</h3>
                    <p class="text-[10px] text-slate-400 font-semibold">PT Bekaert Indonesia</p>
                </div>
            </div>
            <button type="button" onclick="closeMobileSidebar()" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition text-lg leading-none">
                &times;
            </button>
        </div>

        <!-- Mobile Drawer Navigation Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block px-2 mb-1">Menu Utama</span>
            
            <a href="{{ route('qcc.index') }}" onclick="closeMobileSidebar()" 
                class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('qcc.index') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard Circle</span>
            </a>

            <a href="{{ route('qcc.leaderboard') }}" onclick="closeMobileSidebar()" 
                class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('qcc.leaderboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Leaderboard & Hasil</span>
            </a>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="pt-4 border-t border-slate-100 my-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 block px-2 mb-1">Administrasi</span>
                    
                    <a href="{{ route('admin.users.index') }}" onclick="closeMobileSidebar()" 
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Kelola User & Role</span>
                    </a>

                    <a href="{{ route('admin.judges.index') }}" onclick="closeMobileSidebar()" 
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('admin.judges.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Penugasan Juri</span>
                    </a>

                    <a href="{{ route('admin.criteria.index') }}" onclick="closeMobileSidebar()" 
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('admin.criteria.*') ? 'bg-amber-50 text-amber-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Kriteria Nilai</span>
                    </a>

                    <a href="{{ route('admin.circles.index') }}" onclick="closeMobileSidebar()" 
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('admin.circles.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Kelola Circle QCC</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" onclick="closeMobileSidebar()" 
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Pengaturan Sistem</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Mobile Drawer Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/80">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-indigo-600 font-semibold block">Administrator QCC</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition">
                            Sign Out
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs">
                    Sign In Admin
                </a>
            @endif
        </div>

    </div>

    <!-- MAIN CONTENT WRAPPER (Left offset by w-20 on desktop) -->
    <div id="mainContentWrapper" class="flex-1 min-h-screen flex flex-col md:pl-20 transition-[padding] duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]">
        
        <!-- Desktop Sticky Global Topbar -->
        <header class="hidden md:flex bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-6 lg:px-8 py-3 items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700">QCC Assessment PT Bekaert Indonesia</span>
                <span class="text-slate-300">•</span>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Periode Aktif: {{ $activeYear === 'all' ? 'Semua Periode' : 'Tahun ' . $activeYear }}</span>
                </div>
            </div>

            <!-- Global Actions: Year Dropdown Switcher & Admin State -->
            <div class="flex items-center gap-3">
                <!-- Dropdown Form Switch Year -->
                <form method="POST" action="{{ route('qcc.switch-year') }}" class="flex items-center gap-2">
                    @csrf
                    <label for="desktopGlobalYearSelect" class="text-xs font-bold text-slate-500">Pilih Periode:</label>
                    <div class="relative">
                        <select id="desktopGlobalYearSelect" name="year" onchange="this.form.submit()" 
                            class="no-search appearance-none bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold py-1.5 pl-3 pr-8 rounded-xl border border-slate-300/80 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition shadow-2xs">
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ (string)$activeYear === (string)$yr ? 'selected' : '' }}>
                                    Tahun {{ $yr }}
                                </option>
                            @endforeach
                            <option value="all" {{ (string)$activeYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </form>

                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="flex items-center gap-2 pl-3 border-l border-slate-200">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-extrabold">
                            A
                        </div>
                        <span class="text-xs font-bold text-slate-700">{{ Auth::user()->name }}</span>
                    </div>
                @else
                    <div class="pl-3 border-l border-slate-200">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                            Sign In Admin &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </header>

        <!-- Flash Alerts -->
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-lg leading-none">&times;</button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center space-x-2 text-sm shadow-xs">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Body Content -->
        <main class="flex-1 pb-24 md:pb-12">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 hidden md:block">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; {{ date('Y') }} PT Bekaert Indonesia - QCC Assessment & Kaizen Convention System.</p>
            </div>
        </footer>

    </div>

    <!-- Mobile Bottom Navigation Bar (Fast 1-Thumb Reach) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-1.5 flex justify-around items-center shadow-lg">
        <a href="{{ route('qcc.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('qcc.index') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] mt-0.5">Beranda</span>
        </a>

        <a href="{{ route('qcc.leaderboard') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('qcc.leaderboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-[10px] mt-0.5">Ranking</span>
        </a>

        @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('admin.circles.create') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.circles.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    +
                </div>
                <span class="text-[10px] mt-0.5 font-bold text-blue-700">Circle</span>
            </a>

            <a href="{{ route('admin.judges.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.judges.*') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-[10px] mt-0.5">Juri</span>
            </a>

            <a href="{{ route('admin.criteria.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.criteria.*') ? 'text-amber-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-[10px] mt-0.5">Kriteria</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center py-1 text-blue-600 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="text-[10px] mt-0.5">Sign In</span>
            </a>
        @endif
    </nav>

    <!-- Navigation Interactive Scripts -->
    <script>
        // Mobile Sidebar Drawer
        function openMobileSidebar() {
            const drawer = document.getElementById('mobileDrawerPanel');
            const backdrop = document.getElementById('mobileBackdrop');
            backdrop.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                drawer.classList.remove('-translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            const drawer = document.getElementById('mobileDrawerPanel');
            const backdrop = document.getElementById('mobileBackdrop');
            drawer.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0');
            setTimeout(() => {
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        // Desktop Pin Sidebar Toggle
        function togglePinSidebar() {
            const sidebar = document.getElementById('desktopSidebar');
            const wrapper = document.getElementById('mainContentWrapper');
            const pinIcon = document.getElementById('pinIcon');
            const isCurrentlyPinned = sidebar.classList.contains('is-pinned');

            if (isCurrentlyPinned) {
                sidebar.classList.remove('is-pinned');
                wrapper.classList.remove('is-pinned');
                if (pinIcon) pinIcon.classList.remove('rotate-180');
                localStorage.setItem('qcc_sidebar_pinned', 'false');
            } else {
                sidebar.classList.add('is-pinned');
                wrapper.classList.add('is-pinned');
                if (pinIcon) pinIcon.classList.add('rotate-180');
                localStorage.setItem('qcc_sidebar_pinned', 'true');
            }
        }

        // Restore pinned state on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('qcc_sidebar_pinned') === 'true') {
                const sidebar = document.getElementById('desktopSidebar');
                const wrapper = document.getElementById('mainContentWrapper');
                const pinIcon = document.getElementById('pinIcon');
                if (sidebar && wrapper) {
                    sidebar.classList.add('is-pinned');
                    wrapper.classList.add('is-pinned');
                    if (pinIcon) pinIcon.classList.add('rotate-180');
                }
            }
        });
    </script>

    <!-- Tom Select JS Library -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        /**
         * Global Searchable Selects Initializer (TomSelect)
         * Memberikan fitur live search pada semua dropdown <select> di aplikasi
         */
        window.initSearchableSelects = function(root = document) {
            if (typeof TomSelect === 'undefined') return;

            const selects = root.querySelectorAll('select:not(.no-search):not([data-no-search]):not(.tomselected)');
            selects.forEach(select => {
                // Jangan inisialisasi pada template kloning anggota tim
                if (select.closest('#memberRowTemplate') || select.closest('.template-row')) {
                    return;
                }

                // Kecualikan dropdown peran anggota jika berukuran kecil di baris tabel
                if (select.name && select.name.includes('[role]')) {
                    return;
                }

                try {
                    const placeholder = select.getAttribute('placeholder') || 
                                       (select.options.length > 0 && select.options[0].value === '' ? select.options[0].text : 'Cari / pilih...');

                    const ts = new TomSelect(select, {
                        create: false,
                        maxOptions: null,
                        placeholder: placeholder,
                        allowEmptyOption: true,
                        plugins: ['clear_button'],
                        onChange: function(val) {
                            if (typeof select.onchange === 'function') {
                                select.onchange();
                            }
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                } catch(err) {
                    console.debug('TomSelect init skipped:', err);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.initSearchableSelects();
        });
    </script>

    @stack('scripts')
</body>
</html>
