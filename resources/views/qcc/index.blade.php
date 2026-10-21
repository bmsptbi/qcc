@extends('layouts.app')

@section('title', 'Dashboard Gugus QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

    <!-- Hero / Event Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 shadow-xl shadow-indigo-950/20 mb-8 border border-white/10">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-20 -top-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold uppercase tracking-wider mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ $activePeriod->name ?? 'Konvensi QCC' }}
            </div>
            
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight mb-2">
                Evaluasi & Penilaian Gugus Mutu
            </h1>
            <p class="text-blue-100/90 text-sm sm:text-base max-w-2xl leading-relaxed mb-6 font-normal">
                {{ $activePeriod->theme ?? 'Mendorong inovasi dan operational excellence melalui pemecahan masalah 8 Langkah PDCA.' }}
            </p>

            <!-- Quick Action Buttons for Judge / Participant -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="#daftar-gugus" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition shadow-lg shadow-blue-600/30 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Daftar Gugus Dinilai
                </a>

                <a href="{{ route('qcc.leaderboard') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm transition border border-white/15 backdrop-blur-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Lihat Leaderboard Live
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stat Cards (Total + Berdasarkan Status Gugus) -->
    @php
        $currentStatus = request('status');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <!-- 0. Total (Default) -->
        <a href="{{ route('qcc.index', request()->except(['status', 'page'])) }}" 
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all duration-200 hover:shadow-md block group col-span-2 sm:col-span-1 {{ empty($currentStatus) ? 'border-slate-800 ring-2 ring-slate-800 bg-slate-50/70 shadow-sm' : 'border-slate-200/80 shadow-xs' }}"
            title="Klik untuk menampilkan semua gugus (Total)">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider group-hover:text-slate-900 transition">Total</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ empty($currentStatus) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' }}">Semua</span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium">Gugus</span>
            </div>
        </a>

        <!-- 1. Submitted -->
        <a href="{{ route('qcc.index', array_merge(request()->except(['status', 'page']), $currentStatus === 'submitted' ? [] : ['status' => 'submitted'])) }}" 
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all duration-200 hover:shadow-md block group {{ $currentStatus === 'submitted' ? 'border-amber-500 ring-2 ring-amber-400 bg-amber-50/40 shadow-sm' : 'border-slate-200/80 shadow-xs' }}"
            title="Klik untuk filter gugus status Submitted">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider group-hover:text-amber-700 transition">Submitted</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Terdaftar</span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600">{{ $stats['submitted'] }}</span>
                <span class="text-[11px] text-amber-600/70 font-medium">Gugus</span>
            </div>
        </a>

        <!-- 2. Verified -->
        <a href="{{ route('qcc.index', array_merge(request()->except(['status', 'page']), $currentStatus === 'verified' ? [] : ['status' => 'verified'])) }}" 
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all duration-200 hover:shadow-md block group {{ $currentStatus === 'verified' ? 'border-sky-500 ring-2 ring-sky-400 bg-sky-50/40 shadow-sm' : 'border-slate-200/80 shadow-xs' }}"
            title="Klik untuk filter gugus status Verified">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-wider group-hover:text-sky-700 transition">Verified</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">Terverifikasi</span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-sky-600">{{ $stats['verified'] }}</span>
                <span class="text-[11px] text-sky-600/70 font-medium">Gugus</span>
            </div>
        </a>

        <!-- 3. Evaluating -->
        <a href="{{ route('qcc.index', array_merge(request()->except(['status', 'page']), $currentStatus === 'evaluating' ? [] : ['status' => 'evaluating'])) }}" 
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all duration-200 hover:shadow-md block group {{ $currentStatus === 'evaluating' ? 'border-blue-500 ring-2 ring-blue-400 bg-blue-50/40 shadow-sm' : 'border-slate-200/80 shadow-xs' }}"
            title="Klik untuk filter gugus status Evaluating">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider group-hover:text-blue-700 transition">Evaluating</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Dinilai Juri</span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-blue-600">{{ $stats['evaluating'] }}</span>
                <span class="text-[11px] text-blue-600/70 font-medium">Gugus</span>
            </div>
        </a>

        <!-- 4. Evaluated -->
        <a href="{{ route('qcc.index', array_merge(request()->except(['status', 'page']), $currentStatus === 'evaluated' ? [] : ['status' => 'evaluated'])) }}" 
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all duration-200 hover:shadow-md block group {{ $currentStatus === 'evaluated' ? 'border-emerald-500 ring-2 ring-emerald-400 bg-emerald-50/40 shadow-sm' : 'border-slate-200/80 shadow-xs' }}"
            title="Klik untuk filter gugus status Evaluated">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider group-hover:text-emerald-700 transition">Evaluated</span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Selesai</span>
            </div>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['evaluated'] }}</span>
                <span class="text-[11px] text-emerald-600/70 font-medium">Gugus</span>
            </div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div id="daftar-gugus" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs mb-6">
        <form method="GET" action="{{ route('qcc.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <!-- Search Input -->
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama gugus, kode (QCC-xxx), atau judul tema..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                <!-- Sorting State Preservation (hanya jika ada sort eksplisit) -->
                @if(request()->filled('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                @endif

                <!-- Year Period Filter -->
                <div class="relative inline-block flex-shrink-0">
                    <select name="year" onchange="this.form.submit()" 
                        class="no-search appearance-none pl-3.5 pr-8 py-2 rounded-xl border border-blue-200 text-xs sm:text-sm font-bold text-blue-900 bg-blue-50/70 hover:bg-blue-100/70 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition shadow-2xs" 
                        data-no-search="true"
                        style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (string)$activeYear === (string)$yr ? 'selected' : '' }}>
                                Tahun {{ $yr }}
                            </option>
                        @endforeach
                        <option value="all" {{ (string)$activeYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-blue-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                <!-- Department Filter -->
                <div class="relative inline-block flex-shrink-0">
                    <select name="department" onchange="this.form.submit()" 
                        class="no-search appearance-none pl-3.5 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition shadow-2xs" 
                        data-no-search="true"
                        style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative inline-block flex-shrink-0">
                    <select name="status" onchange="this.form.submit()" 
                        class="no-search appearance-none pl-3.5 pr-8 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition shadow-2xs" 
                        data-no-search="true"
                        style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                        <option value="">Semua Status ({{ $stats['total'] }})</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted ({{ $stats['submitted'] }})</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified ({{ $stats['verified'] }})</option>
                        <option value="evaluating" {{ request('status') == 'evaluating' ? 'selected' : '' }}>Evaluating ({{ $stats['evaluating'] }})</option>
                        <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Evaluated ({{ $stats['evaluated'] }})</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                @if(request()->anyFilled(['search', 'category', 'department', 'status']))
                    <a href="{{ route('qcc.index', request()->only(['sort', 'direction'])) }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Circles List (Compact Table View) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden mb-6">
        <div class="px-4 py-3 sm:px-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/60">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                    Daftar Gugus Inovasi
                </h3>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-200/70 text-slate-700">
                    {{ $circles->count() }} @if($circles->count() !== $stats['total']) dari {{ $stats['total'] }} @endif Gugus
                </span>
            </div>
            @if(request()->filled('status'))
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                        Status aktif: <strong class="text-slate-800 uppercase">{{ request('status') }}</strong>
                    </span>
                    <a href="{{ route('qcc.index', request()->except(['status', 'page'])) }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline">
                        Hapus Filter
                    </a>
                </div>
            @endif
        </div>

        @php
            $currentSort = request('sort', 'status');
            $currentDir = strtolower(request('direction', 'asc'));

            $getSortUrl = function($col) use ($currentSort, $currentDir) {
                $nextDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $col, 'direction' => $nextDir]);
            };
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider select-none">
                        <th class="py-3 px-3.5 text-center w-12">No</th>
                        <th class="py-3 px-3.5">
                            <a href="{{ $getSortUrl('code') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan kode gugus">
                                <span>Kode & Gugus</span>
                                @if($currentSort === 'code')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-3.5">
                            <a href="{{ $getSortUrl('theme') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan tema">
                                <span>Judul Tema Perbaikan</span>
                                @if($currentSort === 'theme')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-3.5">
                            <a href="{{ $getSortUrl('department') }}" class="inline-flex items-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan departemen">
                                <span>Departemen & Tim</span>
                                @if($currentSort === 'department')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-3.5 text-center">
                            <a href="{{ $getSortUrl('status') }}" class="inline-flex items-center justify-center gap-1.5 hover:text-blue-600 transition group/sort" title="Klik untuk mengurutkan status">
                                <span>Status</span>
                                @if($currentSort === 'status')
                                    <span class="text-blue-600 font-black text-xs">{{ $currentDir === 'desc' ? '▼' : '▲' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover/sort:text-slate-400 text-[10px]">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($circles as $circle)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No -->
                            <td class="py-3 px-3.5 text-center text-xs font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Kode & Gugus -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 inline-block mb-1">
                                    {{ $circle->circle_code }}
                                </span>
                                <a href="{{ route('qcc.circles.show', $circle) }}" class="font-bold text-slate-900 group-hover:text-blue-600 transition block text-sm">
                                    {{ $circle->circle_name }}
                                </a>
                                @if($circle->sharepoint_url)
                                    <a href="{{ $circle->sharepoint_url }}" target="_blank" rel="noopener noreferrer" 
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline mt-0.5" title="Buka Folder SharePoint">
                                        <span>📁 SharePoint</span>
                                    </a>
                                @endif
                            </td>

                            <!-- Judul Tema -->
                            <td class="py-3 px-3.5 max-w-xs sm:max-w-sm">
                                <div class="font-medium text-slate-700 line-clamp-2 leading-relaxed" title="{{ $circle->theme_title }}">
                                    "{{ $circle->theme_title }}"
                                </div>
                            </td>

                            <!-- Departemen & Tim -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800 text-xs block">
                                    {{ $circle->department->name ?? '-' }}
                                </span>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $circle->members_count ?? $circle->members->count() }} Anggota</span>
                                </div>
                                @if($circle->facilitator)
                                    <div class="text-[10px] text-slate-400 truncate max-w-[150px]" title="{{ $circle->facilitator->name }}">
                                        Fasilitator: {{ $circle->facilitator->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status & Award -->
                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                @if($circle->status === 'evaluating')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        Evaluating
                                    </span>
                                @elseif($circle->status === 'evaluated')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1 shadow-2xs">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Evaluated
                                    </span>
                                @elseif($circle->status === 'verified')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-200">
                                        Verified
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                        Submitted
                                    </span>
                                @endif

                                @if($circle->award_level && $circle->award_level !== 'Bronze')
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-gradient-to-r from-amber-400 to-amber-600 text-white shadow-2xs">
                                            ★ {{ $circle->award_level }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('qcc.circles.show', $circle) }}" 
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs"
                                        title="Buka Detail Risalah">
                                        <span>Detail</span>
                                    </a>

                                    @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')) && in_array($circle->status, ['verified', 'evaluating', 'evaluated']))
                                        <button type="button" 
                                            onclick="openShareModalForCircle({{ $circle->id }}, '{{ addslashes($circle->circle_name) }}', '{{ $circle->circle_code }}', '{{ addslashes($circle->theme_title) }}')"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs"
                                            title="Buat Link & QR Code Penilaian Juri">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            <span class="hidden md:inline">QR & Link</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 px-4">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-base font-bold text-slate-700">Tidak ada data gugus</h3>
                                <p class="text-xs text-slate-500 mt-1">Belum ada gugus yang cocok dengan kriteria filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
<!-- Modal Admin: Bagikan Link & QR Code Penilaian -->
<div id="shareModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 max-h-[92vh] overflow-y-auto">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base font-bold shadow-2xs">
                    📱
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">QR Code & Link Penilaian Juri</h3>
                    <p class="text-[11px] text-slate-500">Pilih tahap penilaian dan scan QR untuk langsung mengisi</p>
                </div>
            </div>
            <button type="button" onclick="closeShareModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <!-- Target Circle Info -->
        <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-2xl">
            <span class="text-[10px] font-bold text-blue-600 uppercase block tracking-wider">Target Gugus:</span>
            <div id="share_circle_name" class="font-bold text-sm text-slate-900">Nama Gugus</div>
            <div id="share_circle_theme" class="text-xs text-slate-600 italic">"Tema Inovasi"</div>
        </div>

        <!-- Setup Form: Tahap & Juri -->
        <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
            <!-- 1. Setup Tahap yang Ditampilkan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Tahap yang Ditampilkan ke Juri:
                </label>
                <select id="share_stage_select" onchange="updateGeneratedShareUrl()" 
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-semibold text-slate-800 focus:outline-none focus:border-blue-500">
                    <option value="" selected>-- Pilih Tahap Penilaian (Belum Dipilih) --</option>
                    <option value="all">Semua Tahap Penilaian (Lengkap)</option>
                    @foreach($criteriaStages as $cat)
                        <option value="{{ $cat->id }}">
                            Tahap: {{ $cat->name }} (Bobot: {{ number_format($cat->weight_percentage, 0) }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Setup Juri Penerima (Bisa Multiple Juri) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">
                        Ditujukan Khusus Juri (Bisa Pilih >1 Juri):
                    </label>
                    <button type="button" onclick="toggleAllShareJudges()" id="toggle_all_judges_btn" class="text-[10px] font-bold text-blue-600 hover:text-blue-800">
                        Pilih Semua
                    </button>
                </div>
                <div class="max-h-36 overflow-y-auto p-2 bg-white rounded-xl border border-slate-200 divide-y divide-slate-100">
                    @foreach($allJudges as $j)
                        <label class="flex items-center justify-between p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer text-xs">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" value="{{ $j->id }}" class="share-judge-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500" onchange="updateGeneratedShareUrl()">
                                <span class="font-semibold text-slate-800">{{ $j->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $j->nik ?? $j->role }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-500 mt-1">
                    💡 <em>Jika dipilih >1 juri atau dikosongkan, juri wajib memilih namanya sendiri saat membuka link / scan QR form penilaian.</em>
                </p>
            </div>
        </div>

        <!-- QR Code Display Box -->
        <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <!-- Placeholder saat tahap belum dipilih -->
            <div id="qr_placeholder_box" class="w-full py-8 px-4 flex flex-col items-center justify-center text-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/70">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-2.5">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-700">QR Code Belum Dibuat</p>
                <p class="text-[11px] text-slate-400 mt-1 max-w-xs">Silakan pilih tahap penilaian di atas terlebih dahulu untuk membuat QR Code & link.</p>
            </div>

            <!-- Tampilan QR saat tahap sudah dipilih -->
            <div id="qr_ready_box" class="hidden flex-col items-center justify-center w-full">
                <div class="p-2.5 bg-white rounded-2xl border border-slate-100 shadow-md">
                    <img id="share_qr_image" src="" alt="QR Code Penilaian" class="w-44 h-44 sm:w-48 sm:h-48 rounded-xl object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-500 mt-2.5 text-center flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Arahkan kamera smartphone juri untuk scan langsung
                </span>
                <a id="download_qr_btn" href="" target="_blank" download="qr-penilaian-qcc.png" 
                    class="mt-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Buka / Download Gambar QR
                </a>
            </div>
        </div>

        <!-- Link Preview Box -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Link URL Penilaian:</label>
            <div class="flex items-center gap-2">
                <input type="text" id="share_url_display" readonly 
                    placeholder="Pilih tahap penilaian di atas terlebih dahulu..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-mono text-slate-800 select-all focus:outline-none">
                <button type="button" onclick="copyShareUrl()" id="copy_btn" 
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex-shrink-0 shadow-sm flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <span id="copy_btn_text">Salin Link</span>
                </button>
            </div>
        </div>

        <!-- Quick Share to WhatsApp -->
        <div class="pt-2 flex items-center gap-2">
            <button type="button" id="whatsapp_share_btn" onclick="shareToWhatsApp()" 
                class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.152.569 4.174 1.564 5.922l-1.564 5.706 5.86-1.538c1.705.932 3.659 1.472 5.74 1.472 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
                </svg>
                <span>Bagikan ke WhatsApp</span>
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let currentCircle = null;

    function openShareModalForCircle(id, name, code, theme) {
        currentCircle = { id, name, code, theme };
        document.getElementById('share_circle_name').innerText = name + ' (' + code + ')';
        document.getElementById('share_circle_theme').innerText = '"' + theme + '"';
        
        // Reset stage select to blank by default
        const stageSelect = document.getElementById('share_stage_select');
        if (stageSelect) stageSelect.value = '';

        // Reset checkboxes
        document.querySelectorAll('.share-judge-cb').forEach(cb => cb.checked = false);
        const toggleBtn = document.getElementById('toggle_all_judges_btn');
        if (toggleBtn) toggleBtn.innerText = 'Pilih Semua';

        updateGeneratedShareUrl();
        const modal = document.getElementById('shareModal');
        modal.classList.remove('hidden');
        if (window.initSearchableSelects) window.initSearchableSelects(modal);
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
    }

    function toggleAllShareJudges() {
        const cbs = document.querySelectorAll('.share-judge-cb');
        const anyUnchecked = Array.from(cbs).some(cb => !cb.checked);
        cbs.forEach(cb => cb.checked = anyUnchecked);
        const toggleBtn = document.getElementById('toggle_all_judges_btn');
        if (toggleBtn) toggleBtn.innerText = anyUnchecked ? 'Kosongkan Semua' : 'Pilih Semua';
        updateGeneratedShareUrl();
    }

    function updateGeneratedShareUrl() {
        if (!currentCircle) return;
        const stageVal = document.getElementById('share_stage_select').value;
        const qrPlaceholderBox = document.getElementById('qr_placeholder_box');
        const qrReadyBox = document.getElementById('qr_ready_box');
        const shareUrlInput = document.getElementById('share_url_display');
        const copyBtn = document.getElementById('copy_btn');
        const waBtn = document.getElementById('whatsapp_share_btn');
        const qrImg = document.getElementById('share_qr_image');
        const downloadQrBtn = document.getElementById('download_qr_btn');

        if (!stageVal) {
            // Tahap belum dipilih: sembunyikan QR & link
            if (qrPlaceholderBox) qrPlaceholderBox.classList.remove('hidden');
            if (qrReadyBox) {
                qrReadyBox.classList.add('hidden');
                qrReadyBox.classList.remove('flex');
            }
            if (shareUrlInput) {
                shareUrlInput.value = '';
                shareUrlInput.placeholder = 'Pilih tahap penilaian di atas terlebih dahulu...';
            }
            if (copyBtn) copyBtn.disabled = true;
            if (waBtn) waBtn.disabled = true;
            if (qrImg) qrImg.src = '';
            if (downloadQrBtn) downloadQrBtn.href = '#';
            return;
        }

        // Tahap sudah dipilih: buat link & QR
        if (qrPlaceholderBox) qrPlaceholderBox.classList.add('hidden');
        if (qrReadyBox) {
            qrReadyBox.classList.remove('hidden');
            qrReadyBox.classList.add('flex');
        }
        if (copyBtn) copyBtn.disabled = false;
        if (waBtn) waBtn.disabled = false;

        const checkedJudges = Array.from(document.querySelectorAll('.share-judge-cb:checked')).map(cb => cb.value);
        
        let url = new URL(window.location.origin + '/circles/' + currentCircle.id + '/assess');
        if (stageVal && stageVal !== 'all') {
            url.searchParams.set('category_id', stageVal);
        }
        if (checkedJudges.length > 0) {
            url.searchParams.set('judges', checkedJudges.join(','));
        }

        const fullUrl = url.toString();
        if (shareUrlInput) {
            shareUrlInput.value = fullUrl;
            shareUrlInput.placeholder = '';
        }
        
        // Update QR Code Image
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=10&data=' + encodeURIComponent(fullUrl);
        if (qrImg) qrImg.src = qrUrl;
        if (downloadQrBtn) downloadQrBtn.href = qrUrl;

        document.getElementById('copy_btn_text').innerText = 'Salin Link';
    }

    function copyShareUrl() {
        const input = document.getElementById('share_url_display');
        if (!input.value) return;
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            document.getElementById('copy_btn_text').innerText = '✓ Tersalin!';
            setTimeout(() => {
                document.getElementById('copy_btn_text').innerText = 'Salin Link';
            }, 2500);
        });
    }

    function shareToWhatsApp() {
        if (!currentCircle) return;
        const url = document.getElementById('share_url_display').value;
        const stageSelect = document.getElementById('share_stage_select');
        if (!stageSelect || !stageSelect.value || !url) {
            alert('Silakan pilih tahap penilaian di atas terlebih dahulu!');
            return;
        }

        const stageName = (stageSelect.selectedIndex >= 0)
            ? stageSelect.options[stageSelect.selectedIndex].text
            : 'Semua Tahap Penilaian';
        
        const checkedJudges = Array.from(document.querySelectorAll('.share-judge-cb:checked'));
        let judgeName = 'Bapak/Ibu Dewan Juri';
        if (checkedJudges.length === 1) {
            judgeName = checkedJudges[0].closest('label').querySelector('.font-semibold').innerText;
        } else if (checkedJudges.length > 1) {
            judgeName = 'Bapak/Ibu Tim Juri (' + checkedJudges.length + ' Juri)';
        }

        const text = `*FORM PENILAIAN QCC {{ $activeYear ?? date('Y') }}*\n` +
            `Halo ${judgeName},\n` +
            `Berikut link penilaian untuk:\n` +
            `• *Gugus:* ${currentCircle.name} (${currentCircle.code})\n` +
            `• *Tema:* ${currentCircle.theme}\n` +
            `• *Tahap:* ${stageName}\n\n` +
            `Silakan scan QR Code atau akses link di bawah ini:\n${url}`;

        window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
    }
</script>
@endpush
@endif
@endsection
