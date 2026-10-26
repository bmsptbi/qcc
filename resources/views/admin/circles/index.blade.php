@extends('layouts.app')

@section('title', 'Manajemen Circle QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-20">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Banner -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-sm mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-blue-500/20 flex-shrink-0">
                🏢
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/60">
                        Admin Master Data
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Total: {{ $stats['total'] }} Circle</span>
                </div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Kelola Circle Mutu QCC
                </h1>
                <p class="text-xs text-slate-500 font-medium hidden sm:block">
                    Pusat administrasi circle inovasi: tambah, edit informasi, jadwalkan presentasi, dan kelola data tim.
                </p>
            </div>
        </div>

        <!-- Action Buttons in Compact Single Row -->
        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            <!-- Unduh Excel (CSV) Dropdown Button -->
            <div class="relative inline-block text-left" id="exportDropdownContainer">
                <button type="button" onclick="toggleExportDropdown(event)" 
                    class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 transition inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs active:scale-95" 
                    title="Pilihan Unduh Data Excel / CSV">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-xs">Unduh</span>
                    <svg class="w-3 h-3 text-slate-400 -mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="exportDropdownMenu" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-2 z-50">
                    <div class="px-3.5 py-1.5 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pilihan Unduh Data</span>
                        <span class="text-[10px] font-bold text-slate-400">{{ $selectedYear !== 'all' ? 'Tahun '.$selectedYear : 'Semua Tahun' }}</span>
                    </div>

                    <!-- Opsi 1: Format Per Anggota (Rekomendasi untuk Update Tim) -->
                    <a href="{{ route('admin.circles.export-members', array_merge(['year' => $selectedYear], request()->query())) }}" 
                        class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-emerald-50/70 transition group">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 flex items-center gap-1.5">
                                <span>Format Per Anggota</span>
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Detail Tim</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">1 baris per anggota tim. Cocok untuk update, tambah, atau hapus anggota.</p>
                        </div>
                    </a>

                    <!-- Opsi 2: Format Per Circle (Ringkasan Standar) -->
                    <a href="{{ route('admin.circles.export', array_merge(['year' => $selectedYear], request()->query())) }}" 
                        class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-slate-50 transition group border-t border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900 group-hover:text-blue-600 flex items-center gap-1.5">
                                <span>Format Ringkasan Circle</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">Per Circle</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight mt-0.5">1 baris per circle (dengan kolom susunan anggota gabungan).</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Import Excel (CSV) Button -->
            <button type="button" onclick="openImportModal('members')" 
                class="px-2.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs active:scale-95" 
                title="Import / Update Data Circle & Anggota dari Excel (CSV)">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span class="text-xs">Import</span>
            </button>

            <!-- Daftarkan Circle Baru Button -->
            <a href="{{ route('admin.circles.create') }}" 
                class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm transition shadow-sm hover:shadow-md shadow-blue-600/20 active:scale-95 inline-flex items-center gap-1.5" 
                title="Daftarkan Circle Baru">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Circle Baru</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards (Total + Status) -->
    @php
        $currentAdminStatus = request('status');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <!-- Total (Default) -->
        <a href="{{ route('admin.circles.index', request()->except(['status', 'page'])) }}" 
            class="bg-white p-4 sm:p-5 rounded-2xl border transition-all duration-200 hover:shadow-md block group col-span-2 sm:col-span-1 {{ empty($currentAdminStatus) ? 'border-slate-800 ring-2 ring-slate-800 bg-slate-50/70 shadow-sm' : 'border-slate-200/90 shadow-xs' }}"
            title="Klik untuk melihat semua circle (Total)">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-slate-800 uppercase tracking-wider block">Total</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ empty($currentAdminStatus) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' }}">Semua</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Semua circle terdaftar</span>
        </a>

        <!-- Submitted -->
        <a href="{{ route('admin.circles.index', array_merge(request()->except(['status', 'page']), $currentAdminStatus === 'submitted' ? [] : ['status' => 'submitted'])) }}" 
            class="bg-white p-4 sm:p-5 rounded-2xl border transition-all duration-200 hover:shadow-md block group {{ $currentAdminStatus === 'submitted' ? 'border-amber-500 ring-2 ring-amber-400 bg-amber-50/40 shadow-sm' : 'border-amber-200/90 bg-amber-50/20 shadow-xs' }}"
            title="Klik untuk filter circle Submitted">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-amber-600 uppercase tracking-wider block">Submitted</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Terdaftar</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ $stats['submitted'] }}</div>
            <span class="text-[10px] text-amber-600/80 mt-0.5 block">Menunggu verifikasi</span>
        </a>

        <!-- Verified -->
        <a href="{{ route('admin.circles.index', array_merge(request()->except(['status', 'page']), $currentAdminStatus === 'verified' ? [] : ['status' => 'verified'])) }}" 
            class="bg-white p-4 sm:p-5 rounded-2xl border transition-all duration-200 hover:shadow-md block group {{ $currentAdminStatus === 'verified' ? 'border-sky-500 ring-2 ring-sky-400 bg-sky-50/40 shadow-sm' : 'border-sky-200/90 bg-sky-50/20 shadow-xs' }}"
            title="Klik untuk filter circle Verified">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-sky-600 uppercase tracking-wider block">Verified</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">Terverifikasi</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-sky-700 mt-1">{{ $stats['verified'] }}</div>
            <span class="text-[10px] text-sky-600/80 mt-0.5 block">Siap dinilai</span>
        </a>

        <!-- Evaluating -->
        <a href="{{ route('admin.circles.index', array_merge(request()->except(['status', 'page']), $currentAdminStatus === 'evaluating' ? [] : ['status' => 'evaluating'])) }}" 
            class="bg-white p-4 sm:p-5 rounded-2xl border transition-all duration-200 hover:shadow-md block group {{ $currentAdminStatus === 'evaluating' ? 'border-blue-500 ring-2 ring-blue-400 bg-blue-50/40 shadow-sm' : 'border-blue-200/90 bg-blue-50/20 shadow-xs' }}"
            title="Klik untuk filter circle Evaluating">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-blue-600 uppercase tracking-wider block">Evaluating</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Dinilai Juri</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">{{ $stats['evaluating'] }}</div>
            <span class="text-[10px] text-blue-600/80 mt-0.5 block">Sedang dinilai juri</span>
        </a>

        <!-- Evaluated -->
        <a href="{{ route('admin.circles.index', array_merge(request()->except(['status', 'page']), $currentAdminStatus === 'evaluated' ? [] : ['status' => 'evaluated'])) }}" 
            class="bg-white p-4 sm:p-5 rounded-2xl border transition-all duration-200 hover:shadow-md block group {{ $currentAdminStatus === 'evaluated' ? 'border-emerald-500 ring-2 ring-emerald-400 bg-emerald-50/40 shadow-sm' : 'border-emerald-200/90 bg-emerald-50/20 shadow-xs' }}"
            title="Klik untuk filter circle Evaluated">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block">Evaluated</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Selesai</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">{{ $stats['evaluated'] }}</div>
            <span class="text-[10px] text-emerald-600/80 mt-0.5 block">Semua tahap selesai</span>
        </a>
    </div>

    <!-- Toggle Visualisasi Grafik (Default Hide) -->
    <div class="flex items-center justify-center -mt-2 mb-6">
        <button type="button" onclick="toggleCharts()" id="toggleChartsBtn"
            class="px-4 py-2 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200/90 shadow-2xs hover:shadow-xs transition-all duration-200 inline-flex items-center gap-2 group active:scale-95 cursor-pointer">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            <svg class="w-4 h-4 text-blue-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
            </svg>
            <span id="chartToggleText">Tampilkan Grafik Statistik Circle Mutu</span>
            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/60">
                Donut & Bar
            </span>
            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-300" id="chartChevronIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <!-- Visualisasi Grafik Container (Default Selalu Hide) -->
    <div id="chartsContainer" class="hidden mb-6 transition-all duration-300">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5">
            <!-- 1. Donut Chart by Status -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-500 text-white flex items-center justify-center font-bold text-sm shadow-xs shadow-amber-500/20">
                                🍩
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Status Circle Mutu</h3>
                                <p class="text-[11px] text-slate-400 font-medium">Distribusi status {{ $selectedYear !== 'all' ? 'Tahun '.$selectedYear : 'Semua Periode' }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-slate-800 bg-slate-100 px-2.5 py-1 rounded-full">
                            Total: {{ $stats['total'] }}
                        </span>
                    </div>

                    <!-- Donut Canvas with Center Counter -->
                    <div class="relative my-4 flex items-center justify-center" style="height: 220px;">
                        <canvas id="statusDonutChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-3xl font-black text-slate-900 leading-none">{{ $stats['total'] }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Circle</span>
                        </div>
                    </div>
                </div>

                <!-- Donut Legend Grid -->
                <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                    <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                            <span class="text-xs font-bold text-slate-700">Submitted</span>
                        </div>
                        <span class="text-xs font-black text-amber-700">{{ $stats['submitted'] }} <span class="text-[10px] font-semibold text-slate-400">({{ $stats['total'] > 0 ? round(($stats['submitted'] / $stats['total']) * 100) : 0 }}%)</span></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-sky-50/60 border border-sky-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500 flex-shrink-0"></span>
                            <span class="text-xs font-bold text-slate-700">Verified</span>
                        </div>
                        <span class="text-xs font-black text-sky-700">{{ $stats['verified'] }} <span class="text-[10px] font-semibold text-slate-400">({{ $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0 }}%)</span></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 flex-shrink-0"></span>
                            <span class="text-xs font-bold text-slate-700">Evaluating</span>
                        </div>
                        <span class="text-xs font-black text-blue-700">{{ $stats['evaluating'] }} <span class="text-[10px] font-semibold text-slate-400">({{ $stats['total'] > 0 ? round(($stats['evaluating'] / $stats['total']) * 100) : 0 }}%)</span></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 flex-shrink-0"></span>
                            <span class="text-xs font-bold text-slate-700">Evaluated</span>
                        </div>
                        <span class="text-xs font-black text-emerald-700">{{ $stats['evaluated'] }} <span class="text-[10px] font-semibold text-slate-400">({{ $stats['total'] > 0 ? round(($stats['evaluated'] / $stats['total']) * 100) : 0 }}%)</span></span>
                    </div>
                </div>
            </div>

            <!-- 2. Bar Chart by Department -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-xs shadow-blue-500/20">
                                📊
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Circle per Departemen</h3>
                                <p class="text-[11px] text-slate-400 font-medium">Partisipasi circle inovasi pada masing-masing departemen</p>
                            </div>
                        </div>
                        @php
                            $activeDeptsWithCircles = $departmentStats->filter(fn($d) => $d['count'] > 0)->count();
                        @endphp
                        <span class="text-xs font-black text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200/50">
                            {{ $activeDeptsWithCircles }} Departemen Aktif
                        </span>
                    </div>

                    <!-- Bar Chart Canvas -->
                    <div class="relative my-4" style="height: 250px;">
                        <canvas id="deptBarChart"></canvas>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                    <span>💡 Arahkan kursor pada batang grafik untuk melihat detail departemen.</span>
                    <button type="button" onclick="toggleCharts()" class="text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1 cursor-pointer">
                        <span>Tutup Grafik</span>
                        <span>▲</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6 space-y-4">
        <form method="GET" action="{{ route('admin.circles.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Circle / Kode / Tema</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: QCC-2026 atau Spider..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Sorting State Preservation (hanya jika ada sort eksplisit) -->
            @if(request()->filled('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
            @endif

            <!-- Filter Tahun Periode -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun Periode</label>
                <div class="relative">
                    <select name="year" class="no-search appearance-none w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none cursor-pointer" data-no-search="true" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        <option value="all" {{ (string)$selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (string)$selectedYear === (string)$yr ? 'selected' : '' }}>
                                Tahun {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter Departemen -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Departemen</label>
                <div class="relative">
                    <select name="department_id" class="no-search appearance-none w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none cursor-pointer" data-no-search="true" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter Status & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status</label>
                    <div class="relative">
                        <select name="status" class="no-search appearance-none w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none cursor-pointer" data-no-search="true" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                            <option value="">Semua Status</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="evaluating" {{ request('status') === 'evaluating' ? 'selected' : '' }}>Evaluating</option>
                            <option value="evaluated" {{ request('status') === 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition flex-shrink-0">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'year', 'department_id', 'status', 'sort']))
                    <a href="{{ route('admin.circles.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs sm:text-sm transition flex-shrink-0" title="Reset filter & sorting ke default">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @php
        $currentSort = request('sort', 'status');
        $currentDir = strtolower(request('direction', 'asc'));

        $getSortUrl = function($col) use ($currentSort, $currentDir) {
            $nextDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $col, 'direction' => $nextDir]);
        };
    @endphp

    <!-- Table of Circles -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider select-none">
                        <th class="py-3.5 px-4">
                            <a href="{{ $getSortUrl('code') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan circle">
                                <span>Circle & Kode</span>
                                @if($currentSort === 'code')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">
                            <a href="{{ $getSortUrl('theme') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan tema">
                                <span>Tema Perbaikan Mutu</span>
                                @if($currentSort === 'theme')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">
                            <a href="{{ $getSortUrl('department') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan departemen">
                                <span>Departemen & Dokumen</span>
                                @if($currentSort === 'department')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4">
                            <a href="{{ $getSortUrl('members') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan anggota">
                                <span>Tim & Fasilitator</span>
                                @if($currentSort === 'members')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>

                        <th class="py-3.5 px-4 text-center">
                            <a href="{{ $getSortUrl('status') }}" class="inline-flex items-center justify-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan status">
                                <span>Status</span>
                                @if($currentSort === 'status')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($circles as $circle)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 inline-block mb-1">
                                    {{ $circle->circle_code }}
                                </span>
                                <a href="{{ route('qcc.circles.show', $circle) }}" class="font-bold text-slate-900 hover:text-blue-600 block text-sm">
                                    {{ $circle->circle_name }}
                                </a>
                                <span class="text-[10px] font-semibold text-slate-500">Tahun {{ $circle->year ?? $circle->period?->year ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-medium text-slate-700 line-clamp-2" title="{{ $circle->theme_title }}">
                                    "{{ $circle->theme_title }}"
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $circle->department->name ?? '-' }}</div>
                                @if($circle->sharepoint_url)
                                    <a href="{{ $circle->sharepoint_url }}" target="_blank" rel="noopener noreferrer" 
                                        class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 mt-0.5" title="Buka Folder SharePoint">
                                        <span>📁 SharePoint</span>
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-xs font-bold text-slate-700">
                                    {{ $circle->members_count }} Anggota
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    Fasilitator: {{ $circle->facilitator->name ?? '-' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($circle->status === 'evaluating')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1 shadow-2xs" title="Sedang dalam proses penilaian tahap berjalan">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        Evaluating
                                    </span>
                                @elseif($circle->status === 'evaluated')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1 shadow-2xs" title="Semua tahap penilaian telah selesai">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Evaluated
                                    </span>
                                @elseif($circle->status === 'verified')
                                    <div class="inline-block relative">
                                        <select onchange="updateCircleStatus({{ $circle->id }}, this.value, this)" 
                                            class="no-search appearance-none pl-2.5 pr-6 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-100 hover:bg-sky-200 text-sky-800 border border-sky-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-2xs transition"
                                            data-no-search="true"
                                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
                                            title="Ubah status circle">
                                            <option value="verified" selected>Verified</option>
                                            <option value="submitted">Submitted</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1.5 text-sky-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="inline-block relative">
                                        <select onchange="updateCircleStatus({{ $circle->id }}, this.value, this)" 
                                            class="no-search appearance-none pl-2.5 pr-6 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-2xs transition"
                                            data-no-search="true"
                                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
                                            title="Ubah status circle">
                                            <option value="submitted" selected>Submitted</option>
                                            <option value="verified">Verified</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1.5 text-amber-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('qcc.circles.show', $circle) }}" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.circles.edit', $circle) }}" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Edit Circle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.circles.destroy', $circle) }}" class="inline" 
                                        onsubmit="return confirm('PERINGATAN: Hapus circle {{ $circle->circle_name }} ({{ $circle->circle_code }})? Seluruh nilai dan riwayat asesmen juri akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Circle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada circle yang sesuai dengan pencarian atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($circles->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $circles->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Import Excel (CSV) -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 border border-slate-200/90 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Import Data Circle & Anggota</h3>
                    <p class="text-xs text-slate-500 font-medium">Unggah file format Excel (.csv) untuk menambah, mengedit, atau sinkronisasi anggota.</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1">
                ✕
            </button>
        </div>

        <!-- Tab Selection (Per Anggota vs Per Circle) -->
        <div class="flex p-1 bg-slate-100 rounded-2xl">
            <button type="button" id="tabBtnMembers" onclick="switchImportTab('members')"
                class="flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Template Per Anggota</span>
                <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Update Tim</span>
            </button>
            <button type="button" id="tabBtnCircles" onclick="switchImportTab('circles')"
                class="flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Template Ringkasan Circle</span>
            </button>
        </div>

        <!-- TAB 1: CONTENT IMPORT PER ANGGOTA -->
        <div id="tabContentMembers" class="space-y-4">
            <div class="p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 text-xs text-emerald-950 space-y-2">
                <div class="font-extrabold flex items-center gap-1.5 text-emerald-900">
                    <span>💡 Cara Update Anggota Sekaligus (Tambah / Hapus):</span>
                </div>
                <ul class="space-y-1.5 text-[11px] text-emerald-900/90 pl-1">
                    <li class="flex items-start gap-1.5">
                        <span class="font-bold text-emerald-700">➕ Tambah Anggota:</span>
                        <span>Cukup buat baris baru di file dengan <strong>Kode Circle</strong> yang sama, lalu isi nama, NIK, dan perannya.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="font-bold text-rose-700">➖ Hapus Anggota:</span>
                        <span>Hapus baris anggota tersebut dari file CSV. Anggota yang barisnya dihapus akan otomatis dikeluarkan dari circle saat file diimpor.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="font-bold text-blue-700">✏️ Edit Data:</span>
                        <span>Ubah Nama, NIK, Peran (<em>Leader, Secretary, Facilitator, Member</em>), atau Email langsung pada baris anggota.</span>
                    </li>
                    <li class="flex items-start gap-1.5 text-slate-600">
                        <span class="font-bold">🛡️ Aman:</span>
                        <span>Hanya circle yang tercantum di file yang susunan anggotanya akan disinkronisasi. Circle lain tetap aman dan tidak berubah.</span>
                    </li>
                </ul>
                <div class="pt-2 border-t border-emerald-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <a href="{{ route('admin.circles.template-members') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-800 hover:text-emerald-950 underline">
                        <svg class="w-4 h-4 text-emerald-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Template Per Anggota (.csv)</span>
                    </a>
                    <a href="{{ route('admin.circles.export-members', array_merge(['year' => $selectedYear], request()->query())) }}" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 underline inline-flex items-center gap-1">
                        <span>Unduh data saat ini</span> &rarr;
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.circles.import-members') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Pilih File CSV Per Anggota <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="file" accept=".csv,text/csv,text/plain" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format file: .csv (Kolom: Kode Circle, Nama Circle, Nama Anggota, NIK, Peran, Email, Departemen, Tahun).</span>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/25 transition inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Mulai Sinkronisasi Anggota</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: CONTENT IMPORT PER CIRCLE (FORMAT LAMA) -->
        <div id="tabContentCircles" class="hidden space-y-4">
            <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-blue-900 space-y-1.5">
                <div class="font-bold flex items-center gap-1.5">
                    <span>💡 Petunjuk Template Per Circle (1 Baris per Circle):</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-blue-800">
                    <li><strong>Tambah Circle Baru:</strong> Kosongkan kolom <em>Kode Circle</em> agar sistem membuat kode otomatis (contoh: QCC-2026-xxx).</li>
                    <li><strong>Edit Circle yang Ada:</strong> Isi kolom <em>Kode Circle</em> sesuai data di tabel untuk memperbarui circle tersebut.</li>
                    <li><strong>Fasilitator:</strong> Masukkan alamat <strong>Email</strong> fasilitator (contoh: <code>iman.sugih@bekaert.com</code>).</li>
                    <li><strong>Susunan Anggota:</strong> Format <code>Nama*NIK</code> dipisahkan titik koma (contoh: <code>Budi Santoso*10001; Siti Aminah*10002</code>).</li>
                </ul>
                <div class="pt-1.5">
                    <a href="{{ route('admin.circles.template') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-blue-700 hover:text-blue-900 underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Template Ringkasan Circle (.csv)</span>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.circles.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Pilih File CSV Circle <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="file" accept=".csv,text/csv,text/plain" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format file: .csv (1 baris per circle dengan kolom Susunan Anggota gabungan).</span>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/25 transition inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Mulai Import Circle</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleExportDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('exportDropdownMenu');
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportDropdownMenu');
        if (dropdown && !dropdown.contains(event.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });

    function openImportModal(initialTab = 'members') {
        switchImportTab(initialTab);
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function switchImportTab(tab) {
        const tabBtnMembers = document.getElementById('tabBtnMembers');
        const tabBtnCircles = document.getElementById('tabBtnCircles');
        const tabContentMembers = document.getElementById('tabContentMembers');
        const tabContentCircles = document.getElementById('tabContentCircles');

        if (tab === 'members') {
            tabBtnMembers.className = 'flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs';
            tabBtnCircles.className = 'flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900';
            tabContentMembers.classList.remove('hidden');
            tabContentCircles.classList.add('hidden');
        } else {
            tabBtnCircles.className = 'flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 bg-white text-blue-800 shadow-xs';
            tabBtnMembers.className = 'flex-1 py-2 px-3 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900';
            tabContentCircles.classList.remove('hidden');
            tabContentMembers.classList.add('hidden');
        }
    }

    function updateCircleStatus(circleId, newStatus, selectEl) {
        if (!confirm('Ubah status circle ini menjadi ' + newStatus.toUpperCase() + '?')) {
            selectEl.value = (newStatus === 'verified') ? 'submitted' : 'verified';
            return;
        }

        const previousValue = (newStatus === 'verified') ? 'submitted' : 'verified';
        selectEl.disabled = true;

        fetch('/circles/' + circleId + '/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            selectEl.disabled = false;
            if (data.success) {
                window.location.reload();
            } else {
                alert('Gagal mengubah status: ' + (data.message || 'Terjadi kesalahan'));
                selectEl.value = previousValue;
            }
        })
        .catch(err => {
            selectEl.disabled = false;
            alert('Gagal menghubungi server.');
            console.error(err);
        });
    }

    let chartsInitialized = false;
    let statusDonutChart = null;
    let deptBarChart = null;

    function toggleCharts() {
        const container = document.getElementById('chartsContainer');
        const btn = document.getElementById('toggleChartsBtn');
        const text = document.getElementById('chartToggleText');
        const chevron = document.getElementById('chartChevronIcon');

        if (!container) return;

        const isHidden = container.classList.contains('hidden');

        if (isHidden) {
            container.classList.remove('hidden');
            text.innerText = 'Sembunyikan Grafik Statistik';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            btn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-300');

            if (!chartsInitialized) {
                initCharts();
                chartsInitialized = true;
            } else {
                setTimeout(() => {
                    if (statusDonutChart) statusDonutChart.resize();
                    if (deptBarChart) deptBarChart.resize();
                }, 50);
            }
        } else {
            container.classList.add('hidden');
            text.innerText = 'Tampilkan Grafik Statistik Circle Mutu';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            btn.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-300');
        }
    }

    function initCharts() {
        const donutCtx = document.getElementById('statusDonutChart');
        const barCtx = document.getElementById('deptBarChart');

        if (donutCtx && typeof Chart !== 'undefined') {
            const statusData = [
                {{ $stats['submitted'] }},
                {{ $stats['verified'] }},
                {{ $stats['evaluating'] }},
                {{ $stats['evaluated'] }}
            ];

            statusDonutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Submitted', 'Verified', 'Evaluating', 'Evaluated'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: [
                            '#f59e0b',
                            '#0284c7',
                            '#2563eb',
                            '#10b981'
                        ],
                        hoverBackgroundColor: [
                            '#d97706',
                            '#0369a1',
                            '#1d4ed8',
                            '#059669'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                            bodyFont: { size: 12, family: "'Plus Jakarta Sans', sans-serif" },
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    const total = {{ $stats['total'] }};
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ' ' + context.label + ': ' + val + ' Circle (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        if (barCtx && typeof Chart !== 'undefined') {
            const deptLabels = {!! json_encode($departmentStats->pluck('name')) !!};
            const deptCodes = {!! json_encode($departmentStats->pluck('code')) !!};
            const deptCounts = {!! json_encode($departmentStats->pluck('count')) !!};

            deptBarChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: deptCodes,
                    datasets: [{
                        label: 'Jumlah Circle',
                        data: deptCounts,
                        backgroundColor: function(context) {
                            const val = context.raw || 0;
                            return val > 0 ? '#0055b8' : '#e2e8f0';
                        },
                        hoverBackgroundColor: function(context) {
                            const val = context.raw || 0;
                            return val > 0 ? '#004399' : '#cbd5e1';
                        },
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 38
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '600',
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 11,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                },
                                color: '#94a3b8'
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                            bodyFont: { size: 12, family: "'Plus Jakarta Sans', sans-serif" },
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                title: function(items) {
                                    const idx = items[0].dataIndex;
                                    return deptLabels[idx] + ' (' + deptCodes[idx] + ')';
                                },
                                label: function(context) {
                                    return ' ' + context.raw + ' Circle Mutu Terdaftar';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
