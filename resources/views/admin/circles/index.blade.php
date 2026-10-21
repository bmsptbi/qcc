@extends('layouts.app')

@section('title', 'Manajemen Gugus QCC')

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
                    <span class="text-xs text-slate-400 font-medium">Total: {{ $stats['total'] }} Gugus</span>
                </div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Kelola Gugus Mutu QCC
                </h1>
                <p class="text-xs text-slate-500 font-medium hidden sm:block">
                    Pusat administrasi gugus inovasi: tambah, edit informasi, jadwalkan presentasi, dan kelola data tim.
                </p>
            </div>
        </div>

        <!-- Action Buttons in Compact Single Row -->
        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            <!-- Unduh Excel (CSV) Button -->
            <a href="{{ route('admin.circles.export', request()->query()) }}" 
                class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 transition inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs active:scale-95" 
                title="Unduh Data Seluruh Gugus ke Format Excel / CSV">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs">Unduh</span>
            </a>

            <!-- Import Excel (CSV) Button -->
            <button type="button" onclick="openImportModal()" 
                class="px-2.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs active:scale-95" 
                title="Import / Bulk Update Data Gugus dari Excel (CSV)">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span class="text-xs">Import</span>
            </button>

            <!-- Daftarkan Gugus Baru Button -->
            <a href="{{ route('admin.circles.create') }}" 
                class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm transition shadow-sm hover:shadow-md shadow-blue-600/20 active:scale-95 inline-flex items-center gap-1.5" 
                title="Daftarkan Gugus Baru">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Gugus Baru</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-200/90 shadow-xs bg-amber-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-amber-600 uppercase tracking-wider block">Submitted</span>
            <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ $stats['submitted'] }}</div>
            <span class="text-[10px] text-amber-600/80 mt-0.5 block">Menunggu verifikasi</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-sky-200/90 shadow-xs bg-sky-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-sky-600 uppercase tracking-wider block">Verified</span>
            <div class="text-2xl sm:text-3xl font-black text-sky-700 mt-1">{{ $stats['verified'] }}</div>
            <span class="text-[10px] text-sky-600/80 mt-0.5 block">Terverifikasi</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-blue-200/90 shadow-xs bg-blue-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-blue-600 uppercase tracking-wider block">Evaluating</span>
            <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">{{ $stats['evaluating'] }}</div>
            <span class="text-[10px] text-blue-600/80 mt-0.5 block">Sedang dinilai juri</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200/90 shadow-xs bg-emerald-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block">Evaluated</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">{{ $stats['evaluated'] }}</div>
            <span class="text-[10px] text-emerald-600/80 mt-0.5 block">Semua tahap selesai</span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6 space-y-4">
        <form method="GET" action="{{ route('admin.circles.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Gugus / Kode / Tema</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: QCC-2026 atau Spider..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Sorting State Preservation -->
            <input type="hidden" name="sort" value="{{ request('sort', 'status') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

            <!-- Filter Tahun Periode -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun Periode</label>
                <div class="relative">
                    <select name="year" class="no-search appearance-none w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none cursor-pointer" data-no-search="true" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ request('year') == (string)$yr ? 'selected' : '' }}>
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
                            <a href="{{ $getSortUrl('code') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan gugus">
                                <span>Gugus & Kode</span>
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
                        <th class="py-3.5 px-4">
                            <a href="{{ $getSortUrl('schedule') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan jadwal">
                                <span>Jadwal & Ruang</span>
                                @if($currentSort === 'schedule')
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
                            <td class="py-3.5 px-4">
                                @if($circle->presentation_schedule)
                                    <div class="font-semibold text-slate-800">
                                        {{ $circle->presentation_schedule->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        📍 {{ $circle->presentation_room ?? 'Ruang Presentasi' }}
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum dijadwalkan</span>
                                @endif
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
                                            title="Ubah status gugus">
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
                                            title="Ubah status gugus">
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
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Edit Gugus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.circles.destroy', $circle) }}" class="inline" 
                                        onsubmit="return confirm('PERINGATAN: Hapus gugus {{ $circle->circle_name }} ({{ $circle->circle_code }})? Seluruh nilai dan riwayat asesmen juri akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Gugus">
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
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada gugus yang sesuai dengan pencarian atau filter yang dipilih.
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
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 border border-slate-200/90 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Import / Bulk Update Gugus</h3>
                    <p class="text-xs text-slate-500 font-medium">Unggah file format Excel (.csv) untuk menambah atau mengedit banyak gugus sekaligus.</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1">
                ✕
            </button>
        </div>

        <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-blue-900 space-y-1.5">
            <div class="font-bold flex items-center gap-1.5">
                <span>💡 Petunjuk Penggunaan Import:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px] text-blue-800">
                <li><strong>Tambah Gugus Baru:</strong> Kosongkan kolom <em>Kode Gugus</em> agar sistem membuat kode otomatis (contoh: QCC-2026-xxx).</li>
                <li><strong>Edit Gugus yang Ada:</strong> Isi kolom <em>Kode Gugus</em> sesuai data di tabel untuk memperbarui gugus tersebut.</li>
                <li><strong>Fasilitator:</strong> Masukkan alamat <strong>Email</strong> fasilitator (contoh: <code>iman.sugih@bekaert.com</code>).</li>
                <li><strong>Susunan Anggota:</strong> Format cukup <code>Nama*NIK</code> dipisahkan titik koma (contoh: <code>Budi Santoso*10001; Siti Aminah*10002; Joko Widodo*10003</code>). Urutan ke-1 = Leader, ke-2 = Secretary, ke-3 dst = Anggota.</li>
            </ul>
            <div class="pt-1.5">
                <a href="{{ route('admin.circles.template') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-blue-700 hover:text-blue-900 underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Template Format Excel (CSV)</span>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.circles.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Pilih File Excel / CSV <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file" accept=".csv,text/csv,text/plain" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                <span class="text-[10px] text-slate-400 mt-1 block">Format file: .csv (Comma / Semicolon separated, UTF-8 BOM kompatibel dengan Microsoft Excel).</span>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/25 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Mulai Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function updateCircleStatus(circleId, newStatus, selectEl) {
        if (!confirm('Ubah status gugus ini menjadi ' + newStatus.toUpperCase() + '?')) {
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
</script>
@endpush
@endsection
