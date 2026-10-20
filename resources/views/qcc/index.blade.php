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

    <!-- Quick Stat Cards (Mobile 2x2 Grid, Desktop 4 Cols) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-8">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Circle QCC</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_circles'] }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Terdaftar</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Jadwal Presentasi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['scheduled_today'] }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Hari Ini</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Selesai Dinilai</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['completed_assessment'] }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Evaluated</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Juri Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ $stats['active_judges'] }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">Evaluator</span>
            </div>
        </div>
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
                <!-- Year Period Filter -->
                <select name="year" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-blue-200 text-xs sm:text-sm font-bold text-blue-900 bg-blue-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (string)$activeYear === (string)$yr ? 'selected' : '' }}>
                            Tahun {{ $yr }}
                        </option>
                    @endforeach
                    <option value="all" {{ (string)$activeYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                </select>

                <!-- Category Filter -->
                <select name="category" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Department Filter -->
                <select name="department" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                    <option value="under_assessment" {{ request('status') == 'under_assessment' ? 'selected' : '' }}>Proses Penilaian</option>
                    <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Selesai Dinilai</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                </select>

                @if(request()->anyFilled(['search', 'category', 'department', 'status']))
                    <a href="{{ route('qcc.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Circles List (Mobile-Optimized Cards) -->
    <div class="space-y-4">
        @forelse($circles as $circle)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-6 shadow-xs hover:shadow-md transition-all">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    
                    <!-- Circle Main Info -->
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800">
                                {{ $circle->circle_code }}
                            </span>

                            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $circle->category->name ?? 'Manufacturing' }}
                            </span>

                            <span class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-50 text-slate-600 border border-slate-200/60">
                                {{ $circle->department->name ?? '-' }}
                            </span>

                            <!-- Status Badge -->
                            @if($circle->status === 'scheduled')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Siap Presentasi
                                </span>
                            @elseif($circle->status === 'under_assessment')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    Sedang Dinilai
                                </span>
                            @elseif($circle->status === 'evaluated')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai Dinilai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">
                                    {{ ucfirst($circle->status) }}
                                </span>
                            @endif

                            @if($circle->award_level)
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-gradient-to-r from-amber-400 to-amber-600 text-white shadow-xs">
                                    ★ {{ $circle->award_level }}
                                </span>
                            @endif
                        </div>

                        <!-- Circle Name & Theme Title -->
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-blue-600 transition">
                                {{ $circle->circle_name }}
                            </h2>
                            <p class="text-sm font-medium text-slate-700 mt-1 leading-snug">
                                "{{ $circle->theme_title }}"
                            </p>
                        </div>

                        <!-- Schedule & Room -->
                        <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 pt-1">
                            @if($circle->presentation_schedule)
                                <div class="flex items-center gap-1 text-slate-600 font-medium">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $circle->presentation_schedule->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            @endif

                            @if($circle->presentation_room)
                                <div class="flex items-center gap-1 text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $circle->presentation_room }}
                                </div>
                            @endif

                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                {{ $circle->members_count ?? $circle->members->count() }} Anggota
                            </div>
                        </div>
                    </div>

                    <!-- Actions & Score Column (Optimized for Mobile Touch) -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end justify-between gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        @if($circle->final_score)
                            <div class="text-right">
                                <span class="text-[11px] font-semibold text-slate-400 block uppercase">Nilai Akhir</span>
                                <span class="text-2xl font-black text-slate-900">{{ number_format($circle->final_score, 2) }}</span>
                                <span class="text-xs text-slate-500 font-medium block">/ 100</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
                            <!-- Detail Button -->
                            <a href="{{ route('qcc.circles.show', $circle) }}" class="px-4 py-2.5 text-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition touch-action-manipulation">
                                Detail Risalah
                            </a>

                            <!-- Admin QR & Link Penilaian Button (Only visible for Admin) -->
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
                                <button type="button" 
                                    onclick="openShareModalForCircle({{ $circle->id }}, '{{ addslashes($circle->circle_name) }}', '{{ $circle->circle_code }}', '{{ addslashes($circle->theme_title) }}')"
                                    class="px-3.5 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5 shadow-2xs"
                                    title="Buat Link & QR Code Penilaian Juri">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <span>QR & Link</span>
                                </button>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-slate-300 p-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-base font-bold text-slate-700">Tidak ada data gugus</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada gugus yang cocok dengan kriteria filter Anda.</p>
            </div>
        @endforelse
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
