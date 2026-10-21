@extends('layouts.app')

@section('title', 'Detail Risalah - ' . $circle->circle_name)

@section('content')
@php
    $isJudgeUnlocked = (bool) session('admin_unlocked_judges', false);
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

    <!-- Breadcrumb & Top Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Unduh Format Excel (CSV) Button -->
            <a href="{{ route('qcc.circles.export', $circle) }}" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs sm:text-sm border border-emerald-200 transition inline-flex items-center gap-1.5 shadow-2xs" title="Unduh Summary Matrix Penilaian Format Excel / CSV">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh Excel (CSV)</span>
            </a>

            <!-- Share Assessment Link & QR Code Button (Only for Admin) -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <button type="button" onclick="openShareModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm border border-slate-200 transition inline-flex items-center gap-1.5 shadow-2xs" title="Buat Link & QR Code Penilaian">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Bagikan Link & QR</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                        {{ $circle->circle_code }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $circle->circle_name }}
                </h1>
                <p class="text-base sm:text-lg font-medium text-slate-700 leading-snug">
                    "{{ $circle->theme_title }}"
                </p>
            </div>

            <!-- Score Summary Card -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white text-center min-w-[150px] shadow-lg flex-shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 block">Rata-rata Skor</span>
                <span class="text-3xl sm:text-4xl font-black text-amber-400 block my-1">
                    {{ number_format($circle->final_score ?? $calculatedFinalScore, 2) }}
                </span>
                <span class="text-[11px] font-semibold text-slate-300">
                    {{ count($allEvaluatingJudges) > 0 ? count($allEvaluatingJudges) . ' Juri Terlibat' : 'Belum Ada Penilaian' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left: Rekap Penilaian per Tahapan (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Rekap Penilaian per Tahapan (Per Stage Per Judge) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Rekap Penilaian per Tahapan</h2>
                        <p class="text-xs text-slate-500">Penilaian dewan juri dipetakan secara khusus pada masing-masing tahapan</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 self-start sm:self-auto">
                        {{ collect($stagesMatrix)->filter(fn($s) => count($s['judges']) > 0)->count() }} dari {{ count($stagesMatrix) }} Tahap Dinilai
                    </span>
                </div>                <div class="space-y-2.5">
                    @foreach($stagesMatrix as $stageId => $stageData)
                        @php
                            $cat = $stageData['category'];
                            $stageJudges = $stageData['judges'];
                            $hasJudges = count($stageJudges) > 0;
                        @endphp
                        <div class="p-3.5 sm:p-4 rounded-2xl border {{ $hasJudges ? 'border-slate-200/80 bg-slate-50/60' : 'border-dashed border-slate-200 bg-white' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition hover:border-slate-300">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $hasJudges ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-slate-300' }}"></span>
                                <h3 class="font-bold text-sm text-slate-900">{{ $cat->name }}</h3>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                    Bobot {{ number_format($stageData['weight'], 0) }}%
                                </span>
                                @if(!empty($stageData['adjustments']))
                                    <span class="text-[9px] font-bold uppercase text-emerald-800 bg-emerald-100 border border-emerald-300 px-1.5 py-0.5 rounded shadow-2xs" title="Menggunakan nilai {{ end($stageData['adjustments'])['name'] }}">
                                        {{ end($stageData['adjustments'])['name'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 justify-between sm:justify-end">
                                @if($hasJudges)
                                    <div class="text-right">
                                        <span class="text-[10px] font-semibold text-slate-500 uppercase block leading-tight">Rata-rata Tahap</span>
                                        <span class="text-sm font-black text-blue-700">
                                            {{ number_format($stageData['stage_stats']['average'], 2) }}
                                            <span class="text-[10px] font-medium text-slate-400">/ 10</span>
                                        </span>
                                    </div>
                                    <div class="text-right pl-3 border-l border-slate-200">
                                        <span class="text-[10px] font-semibold text-slate-500 uppercase block leading-tight">Kontribusi</span>
                                        <span class="text-sm font-black text-emerald-700">
                                            +{{ number_format($stageData['stage_contribution'], 2) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                                            Belum Dinilai
                                        </span>
                                        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
                                            <a href="{{ route('qcc.circles.assess', [$circle, 'category_id' => $cat->id]) }}" class="text-xs text-blue-600 hover:text-blue-800 font-bold hover:underline inline-flex items-center gap-1">
                                                <span>Beri Nilai</span>
                                                <span>&rarr;</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Circle Members & Team Structure (1 Col) -->
        <div class="space-y-4">
            <!-- Fasilitator Pendamping -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-100 to-indigo-100 text-purple-700 font-black text-sm flex items-center justify-center shadow-2xs">
                        {{ strtoupper(substr($circle->facilitator->name ?? 'F', 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block leading-tight">Fasilitator Pendamping</span>
                        <div class="font-extrabold text-xs sm:text-sm text-slate-900">{{ $circle->facilitator->name ?? 'Belum Ditentukan' }}</div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                    Fasilitator
                </span>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    Susunan Anggota Circle
                </h2>

                <div class="space-y-3">
                    @forelse($circle->members as $member)
                        <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-slate-200 to-slate-300 text-slate-700 font-bold text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-xs sm:text-sm text-slate-800">{{ $member->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">NIK: {{ $member->nik }}</div>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $member->role === 'leader' ? 'bg-amber-100 text-amber-800' : ($member->role === 'secretary' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600') }}">
                                {{ $member->role }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">
                            Belum ada daftar anggota.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- SharePoint Folder & Documents -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span>Dokumen & Risalah SharePoint</span>
                    </h2>
                </div>

                @if($circle->sharepoint_url)
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-50/60 to-indigo-50/40 border border-blue-100 flex flex-col gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-slate-800 block truncate">Folder Dokumen Circle</span>
                                <span class="text-[11px] text-slate-500 leading-tight block mt-0.5">
                                    Akses file risalah 8 langkah, slide presentasi, dan berkas pendukung resmi.
                                </span>
                            </div>
                        </div>

                        <div class="pt-1 flex items-center gap-2">
                            <a href="{{ $circle->sharepoint_url }}" target="_blank" rel="noopener noreferrer"
                                class="flex-1 py-2.5 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-1.5">
                                <span>Buka Folder SharePoint</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <button type="button" onclick="copySharePointUrl('{{ addslashes($circle->sharepoint_url) }}', this)"
                                class="p-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 transition text-xs font-semibold" title="Salin Tautan">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-600">Link Folder SharePoint Belum Ada</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tautan folder dokumen risalah belum ditambahkan untuk circle ini.</p>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.circles.edit', $circle) }}" class="mt-2.5 inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                <span>+ Tambahkan Link Sekarang</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Summary & Matriks Penilaian Dewan Juri per Tahap (Format Excel Mockup) -->
    @auth
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-xs mb-8 space-y-4">
        <!-- Top Row: Title & Nilai Keseluruhan -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    Summary & Matriks Penilaian Dewan Juri per Tahap
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tampilan butir kriteria penilaian, skor masing-masing dewan juri per tahap, statistik deviasi, dan nilai tertimbang.
                </p>
            </div>

            <!-- Nilai Keseluruhan di Header -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 text-white shadow-xs border border-slate-800 self-start sm:self-auto flex-shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Nilai Keseluruhan:</span>
                <span class="px-2.5 py-0.5 bg-yellow-300 border border-yellow-400 font-mono font-black text-slate-950 text-sm sm:text-base rounded shadow-2xs" title="Total Nilai Akhir Keseluruhan Circle">
                    {{ number_format($circle->final_score ?? $calculatedFinalScore, 4) }}
                </span>
            </div>
        </div>

        <!-- Controls Row: Stage Navigation Tabs & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2.5">
            <!-- Stage Navigation Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 text-xs font-bold scrollbar-none">
                <button type="button" onclick="selectMatrixStageTab('all')" data-tab-id="all" 
                    class="matrix-tab-btn px-3 py-1.5 rounded-lg bg-blue-600 text-white transition shadow-2xs whitespace-nowrap">
                    Semua Tahap Penilaian
                </button>
                @foreach($stagesMatrix as $stageId => $stageData)
                    <button type="button" onclick="selectMatrixStageTab('{{ $stageId }}')" data-tab-id="{{ $stageId }}" 
                        class="matrix-tab-btn px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition whitespace-nowrap">
                        {{ $stageData['category']->name }} ({{ number_format($stageData['weight'], 0) }}%)
                    </button>
                @endforeach
            </div>

            <!-- Action Buttons Toolbar (Icon Only for Compact & Mobile Friendly Layout) -->
            <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0 self-start md:self-auto">
                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
                    <!-- Tombol Hapus Penilaian (Hanya untuk Admin) -->
                    <button type="button" onclick="openDeleteAssessmentModal()" id="btn_delete_assessments"
                        class="p-2 sm:p-2.5 rounded-xl bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-300 transition inline-flex items-center justify-center shadow-2xs group"
                        title="Hapus Penilaian Dewan Juri (Verifikasi Password Admin)"
                        aria-label="Hapus Penilaian">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    <!-- Tombol Tampilkan / Sembunyikan Nama Juri (Hanya untuk Admin) -->
                    <button type="button" onclick="handleToggleJudgeNames()" id="btn_toggle_judge_names"
                        class="p-2 sm:p-2.5 rounded-xl {{ $isJudgeUnlocked ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-200' }} border transition inline-flex items-center justify-center shadow-2xs"
                        title="{{ $isJudgeUnlocked ? 'Sembunyikan Nama Juri' : 'Tampilkan Nama Juri (Verifikasi Password Admin)' }}"
                        aria-label="{{ $isJudgeUnlocked ? 'Sembunyikan Nama Juri' : 'Tampilkan Nama Juri' }}">
                        <svg class="w-4 h-4 {{ $isJudgeUnlocked ? 'text-amber-700' : 'text-slate-600' }}" id="icon_toggle_judge_names" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($isJudgeUnlocked)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            @endif
                        </svg>
                    </button>
                @endif

                <!-- Global Toggle Expand/Collapse Semua Detail Butir -->
                <button type="button" onclick="toggleAllCriteriaDetails()" id="btn_toggle_all_details" 
                    class="p-2 sm:p-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 transition inline-flex items-center justify-center shadow-2xs"
                    title="Sembunyikan / Buka Semua Detail Kriteria"
                    aria-label="Toggle Detail Kriteria">
                    <svg class="w-4 h-4 text-slate-600 transition-transform duration-200" id="icon_toggle_all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Unduh Excel (CSV) -->
                <a href="{{ route('qcc.circles.export', $circle) }}" 
                    class="p-2 sm:p-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition inline-flex items-center justify-center"
                    title="Unduh Excel (CSV)"
                    aria-label="Unduh Excel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </a>

                <!-- Print Button -->
                <button type="button" onclick="window.print()" 
                    class="p-2 sm:p-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 transition inline-flex items-center justify-center shadow-2xs"
                    title="Cetak Halaman"
                    aria-label="Cetak Halaman">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Stage Panels Loop -->
        <div class="space-y-8">
            @foreach($stagesMatrix as $stageId => $stageData)
                @php
                    $cat = $stageData['category'];
                    $stageJudges = $stageData['judges'];
                    $hasJudges = count($stageJudges) > 0;
                @endphp
                <div id="stage_matrix_panel_{{ $stageId }}" class="stage-matrix-panel space-y-4" data-stage-id="{{ $stageId }}">
                    
                    <!-- Excel-Like Top Banner -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="text-xs uppercase font-extrabold tracking-wider text-slate-400">Team:</span>
                            <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $circle->circle_name }}</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $cat->name }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500">
                                (Bobot Tahap: {{ number_format($stageData['weight'], 0) }}%)
                            </span>
                        </div>

                        <!-- Right: Toggle Detail Button & Nilai Tertimbang Tahap Tersebut -->
                        <div class="flex items-center gap-2.5 self-start sm:self-auto flex-wrap">
                            @if($hasJudges)
                                <button type="button" onclick="toggleStageCriteria('{{ $stageId }}')" id="btn_toggle_{{ $stageId }}" 
                                    class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 transition inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500 transition-transform duration-200" id="icon_toggle_{{ $stageId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span id="text_toggle_{{ $stageId }}">Sembunyikan Detail</span>
                                </button>
                            @endif

                            <div class="flex items-center gap-2 pl-1">
                                <!-- Teks Nilai Tertimbang & Tanda Adjustment di bawahnya -->
                                <div class="flex flex-col items-end justify-center">
                                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-500 leading-tight">
                                        Nilai Tertimbang:
                                    </span>
                                    @if(!empty($stageData['adjustments']))
                                        <span class="text-[9px] font-black uppercase text-emerald-800 bg-emerald-100 border border-emerald-300 px-1.5 py-0.5 rounded mt-0.5 shadow-2xs leading-none" title="Nilai tahap ini menggunakan hasil {{ end($stageData['adjustments'])['name'] }} karena deviasi awal melebihi {{ number_format($stageData['max_deviation_threshold'], 0) }}%">
                                            {{ end($stageData['adjustments'])['name'] }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Box Skor Tertimbang & Nilai Sesuai Bobot (Font Lebih Kecil) -->
                                <div class="flex flex-col items-center justify-center">
                                    <span class="px-3 py-1 bg-yellow-300 border border-yellow-400 font-mono font-black text-slate-950 text-base sm:text-lg rounded-md shadow-2xs tracking-wide leading-tight" title="Nilai Rata-rata Tertimbang Tahap Ini (Skala 0 - 10)">
                                        {{ $stageData['stage_stats']['average'] !== null ? number_format($stageData['stage_stats']['average'], 2) : '-' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-500 font-mono mt-0.5 leading-none" title="Nilai Sesuai Bobot Tahap ({{ number_format($stageData['weight'], 0) }}%) = {{ number_format($stageData['stage_stats']['average'], 2) }} × {{ number_format($stageData['weight'], 0) }}%">
                                        Bobot: {{ number_format($stageData['stage_contribution'], 4) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($hasJudges)
                        <!-- Table Content (Item | Judges | Max | Min | Range | Average | Deviasi Max | Deviasi Min | Deviasi) -->
                        <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-2xs bg-white">
                            <table class="w-full text-left text-xs border-collapse min-w-[950px]">
                                <thead>
                                    <!-- Table Column Headers -->
                                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 text-[11px] font-bold uppercase tracking-wider">
                                        <th class="p-3 sticky left-0 bg-slate-100 z-10 border-r border-slate-200">
                                            Item Penilaian
                                        </th>
                                        @foreach($stageJudges as $judge)
                                            <th class="p-3 text-center border-r border-slate-200 whitespace-nowrap font-bold">
                                                <span class="judge-anon-name {{ $isJudgeUnlocked ? 'hidden' : '' }}">Juri {{ $loop->iteration }}</span>
                                                <span class="judge-real-name {{ $isJudgeUnlocked ? '' : 'hidden' }}">{{ $judge->name }}</span>
                                            </th>
                                        @endforeach
                                        <th class="p-3 text-center border-r border-slate-200">Max</th>
                                        <th class="p-3 text-center border-r border-slate-200">Min</th>
                                        <th class="p-3 text-center border-r border-slate-200">Range</th>
                                        <th class="p-3 text-center bg-blue-100/80 text-blue-900 border-r border-slate-200">Average</th>
                                        <th class="p-3 text-center border-r border-slate-200">Deviasi Max</th>
                                        <th class="p-3 text-center border-r border-slate-200">Deviasi Min</th>
                                        <th class="p-3 text-center bg-purple-100/80 text-purple-900">Deviasi (%)</th>
                                    </tr>

                                    <!-- Row(s): Adjustment jika Deviasi > Batas Maksimum (Urutan: Final di paling atas, bertahap ke Nilai Tertimbang di bawah) -->
                                    @if(!empty($stageData['adjustments']))
                                        @foreach(array_reverse($stageData['adjustments']) as $adj)
                                            <tr class="{{ $adj['is_final'] ? 'bg-emerald-50/70 border-b-2 border-emerald-200 text-slate-900' : 'bg-amber-50/50 border-b border-slate-200 text-slate-800' }} font-bold">
                                                <th class="p-3 font-black text-xs text-slate-900 sticky left-0 {{ $adj['is_final'] ? 'bg-emerald-50/95' : 'bg-amber-50/95' }} z-10 border-r border-slate-200">
                                                    <div class="flex items-center justify-between gap-1.5">
                                                        <span class="{{ $adj['is_final'] ? 'text-emerald-950' : 'text-slate-900' }}">{{ $adj['name'] }}</span>
                                                        @if($adj['is_final'])
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-600 text-white font-extrabold uppercase tracking-wider shadow-2xs">FINAL</span>
                                                        @else
                                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 font-bold uppercase tracking-wider">&gt; {{ number_format($stageData['max_deviation_threshold'], 0) }}%</span>
                                                        @endif
                                                    </div>
                                                </th>
                                                @foreach($stageJudges as $judge)
                                                    @php
                                                        $isTarget = ($adj['target_judge_id'] == $judge->id);
                                                    @endphp
                                                    <th class="p-2.5 text-center font-black text-sm border-r border-slate-200/80">
                                                        @if($isTarget)
                                                            <div class="inline-flex flex-col items-center justify-center">
                                                                <span class="px-2 py-0.5 bg-amber-200/90 border border-amber-400 text-amber-950 rounded shadow-2xs font-mono font-black" title="Skor juri ini dikalibrasi dari {{ number_format($adj['replaced_score'], 2) }} ke {{ number_format($adj['new_score'], 2) }} (Average sebelumnya)">
                                                                    {{ number_format($adj['judge_scores'][$judge->id], 2) }}
                                                                </span>
                                                                <span class="text-[9px] text-slate-400 font-semibold line-through">
                                                                    {{ number_format($adj['replaced_score'], 2) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-blue-800">
                                                                {{ isset($adj['judge_scores'][$judge->id]) ? number_format($adj['judge_scores'][$judge->id], 2) : '-' }}
                                                            </span>
                                                        @endif
                                                    </th>
                                                @endforeach
                                                <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                                    {{ $adj['stats']['max'] !== null ? number_format($adj['stats']['max'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                                    {{ $adj['stats']['min'] !== null ? number_format($adj['stats']['min'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                                    {{ $adj['stats']['range'] !== null ? number_format($adj['stats']['range'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-black text-sm {{ $adj['is_final'] ? 'text-emerald-950 bg-emerald-200/70' : 'text-blue-900 bg-blue-100/70' }} border-r border-slate-200/80">
                                                    {{ $adj['stats']['average'] !== null ? number_format($adj['stats']['average'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                                    {{ $adj['stats']['deviasi_max'] !== null ? number_format($adj['stats']['deviasi_max'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                                    {{ $adj['stats']['deviasi_min'] !== null ? number_format($adj['stats']['deviasi_min'], 2) : '-' }}
                                                </th>
                                                <th class="p-3 text-center font-black {{ $adj['stats']['deviasi'] > $stageData['max_deviation_threshold'] ? 'text-rose-700 bg-rose-100/80' : 'text-emerald-800 bg-emerald-100/80' }}">
                                                    {{ $adj['stats']['deviasi'] !== null ? number_format($adj['stats']['deviasi'], 2) . '%' : '-' }}
                                                </th>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <!-- Row: Nilai Tertimbang (Summary Row Tahap Ini) -->
                                    @php
                                        $rawStats = $stageData['raw_stage_stats'] ?? $stageData['stage_stats'];
                                        $hasAdj = !empty($stageData['adjustments']);
                                    @endphp
                                    <tr class="{{ $hasAdj ? 'bg-slate-50/80 border-b-2 border-slate-200 text-slate-600' : 'bg-amber-50/80 border-b-2 border-slate-200 text-slate-900' }} font-bold">
                                        <th class="p-3 font-black text-xs text-slate-900 sticky left-0 {{ $hasAdj ? 'bg-slate-50/95' : 'bg-amber-50/95' }} z-10 border-r border-slate-200">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <span>Nilai Tertimbang</span>
                                                @if($hasAdj)
                                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold uppercase">Awal</span>
                                                @else
                                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-200/70 text-amber-900 font-semibold uppercase">Summary</span>
                                                @endif
                                            </div>
                                        </th>
                                        @foreach($stageJudges as $judge)
                                            <th class="p-3 text-center font-black text-sm {{ $hasAdj ? 'text-slate-600' : 'text-blue-800' }} border-r border-slate-200/80">
                                                {{ $stageData['judge_stage_scores'][$judge->id] ?? '-' }}
                                            </th>
                                        @endforeach
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $rawStats['max'] !== null ? number_format($rawStats['max'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $rawStats['min'] !== null ? number_format($rawStats['min'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $rawStats['range'] !== null ? number_format($rawStats['range'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-black text-sm {{ $hasAdj ? 'text-slate-700 bg-slate-100/80' : 'text-blue-900 bg-blue-100/70' }} border-r border-slate-200/80">
                                            {{ $rawStats['average'] !== null ? number_format($rawStats['average'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $rawStats['deviasi_max'] !== null ? number_format($rawStats['deviasi_max'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $rawStats['deviasi_min'] !== null ? number_format($rawStats['deviasi_min'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-black {{ ($rawStats['deviasi'] !== null && $rawStats['deviasi'] > $stageData['max_deviation_threshold']) ? 'text-rose-700 bg-rose-100/80' : 'text-purple-900 bg-purple-100/70' }}" title="{{ ($rawStats['deviasi'] !== null && $rawStats['deviasi'] > $stageData['max_deviation_threshold']) ? 'Deviasi melebihi batas ' . number_format($stageData['max_deviation_threshold'], 0) . '%' : '' }}">
                                            {{ $rawStats['deviasi'] !== null ? number_format($rawStats['deviasi'], 2) . '%' : '-' }}
                                        </th>
                                    </tr>
                                </thead>
                                
                                <!-- Detail Butir Kriteria (Bisa di Expand / Hide) -->
                                <tbody id="criteria_tbody_{{ $stageId }}" class="stage-criteria-tbody divide-y divide-slate-100">
                                    @foreach($stageData['criteria_rows'] as $row)
                                        @php
                                            $crit = $row['criterion'];
                                            $stats = $row['stats'];
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition even:bg-slate-50/30">
                                            <td class="p-3 font-semibold text-slate-900 sticky left-0 bg-white group-hover:bg-slate-50 border-r border-slate-200">
                                                <div class="flex items-center gap-1.5">
                                                    @if($crit->code)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 flex-shrink-0">
                                                            {{ $crit->code }}
                                                        </span>
                                                    @endif
                                                    <span class="line-clamp-2" title="{{ $crit->title }}">{{ $crit->title }}</span>
                                                </div>
                                            </td>
                                            @foreach($stageJudges as $judge)
                                                @php
                                                    $sc = $row['scores_by_judge'][$judge->id] ?? null;
                                                @endphp
                                                <td class="p-3 text-center font-bold text-slate-800 border-r border-slate-100">
                                                    {{ $sc !== null ? number_format($sc, 0) : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="p-3 text-center text-slate-600 font-medium bg-slate-50/50 border-r border-slate-100">
                                                {{ $stats['max'] !== null ? number_format($stats['max'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center text-slate-600 font-medium bg-slate-50/50 border-r border-slate-100">
                                                {{ $stats['min'] !== null ? number_format($stats['min'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center text-slate-600 font-medium bg-slate-50/50 border-r border-slate-100">
                                                {{ $stats['range'] !== null ? number_format($stats['range'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center font-bold text-blue-900 bg-blue-50/60 border-r border-slate-100">
                                                {{ $stats['average'] !== null ? number_format($stats['average'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center text-slate-600 font-medium bg-slate-50/50 border-r border-slate-100">
                                                {{ $stats['deviasi_max'] !== null ? number_format($stats['deviasi_max'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center text-slate-600 font-medium bg-slate-50/50 border-r border-slate-100">
                                                {{ $stats['deviasi_min'] !== null ? number_format($stats['deviasi_min'], 2) : '-' }}
                                            </td>
                                            <td class="p-3 text-center font-semibold text-purple-900 bg-purple-50/60">
                                                {{ $stats['deviasi'] !== null ? number_format($stats['deviasi'], 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Toggle Button di bagian bawah tabel -->
                            <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-center">
                                <button type="button" onclick="toggleStageCriteria('{{ $stageId }}')" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 transition">
                                    <span id="footer_toggle_text_{{ $stageId }}">▲ Sembunyikan Detail (Tampilkan Summary Saja)</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Empty State for this stage -->
                        <div class="p-8 text-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 space-y-2">
                            <span class="text-2xl block">📋</span>
                            <p class="text-sm font-bold text-slate-700">Tahap ini belum dinilai oleh dewan juri</p>
                            <p class="text-xs text-slate-500">Nilai akan otomatis muncul di matriks ini setelah juri mengirimkan penilaian.</p>
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
                                <div class="pt-2">
                                    <a href="{{ route('qcc.circles.assess', [$circle, 'category_id' => $cat->id]) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition">
                                        <span>Buka Form Penilaian Tahap Ini</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Lock State untuk Pengunjung yang Belum Login -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xs mb-8 text-center space-y-3">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto text-xl">
            🔒
        </div>
        <h3 class="text-base font-bold text-slate-800">Summary & Matriks Penilaian Dibatasi</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Rincian matriks butir kriteria per tahap, statistik deviasi, dan skor dewan juri hanya dapat diakses oleh pengguna yang telah login.
        </p>
        <div class="pt-2">
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs inline-flex items-center gap-2 transition shadow-sm">
                <span>Sign In untuk Melihat Matriks</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
    @endauth

</div>

<!-- Modal Verifikasi Password Admin untuk Menampilkan Nama Juri -->
<div id="modal_admin_auth" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base font-bold shadow-2xs">
                    🔒
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Verifikasi Hak Akses Admin</h3>
                    <p class="text-[11px] text-slate-500">Buka nama asli dewan juri penilai</p>
                </div>
            </div>
            <button type="button" onclick="closeAdminAuthModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1 leading-none">&times;</button>
        </div>

        <form id="form_admin_auth" onsubmit="submitAdminAuth(event)" class="space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Untuk menjaga independensi penilaian, nama dewan juri disamarkan sebagai <strong>Juri 1, Juri 2, dst</strong>. Masukkan password akun Administrator untuk membuka nama asli dewan juri.
            </p>

            <div>
                <label for="admin_auth_password" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Password Administrator:
                </label>
                <div class="relative">
                    <input type="password" id="admin_auth_password" name="password" required placeholder="Ketik password admin..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition pr-10">
                    <button type="button" onclick="togglePasswordVisibility('admin_auth_password', 'icon_pwd_toggle')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" id="icon_pwd_toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <div id="admin_auth_error" class="text-xs text-rose-600 font-semibold mt-1.5 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeAdminAuthModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" id="btn_submit_auth" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <span id="btn_submit_auth_text">Buka Nama Juri</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Penilaian Dewan Juri (Verifikasi Password Admin) -->
<div id="deleteAssessmentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-4 max-h-[92vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base font-bold shadow-2xs">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Hapus Penilaian Dewan Juri</h3>
                    <p class="text-xs text-slate-500 font-medium">Circle: <span class="font-bold text-slate-800">{{ $circle->circle_name }}</span> ({{ $circle->circle_code }})</p>
                </div>
            </div>
            <button type="button" onclick="closeDeleteAssessmentModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1 leading-none">&times;</button>
        </div>

        <form id="form_delete_assessment" method="POST" action="{{ route('qcc.circles.delete-assessments', $circle) }}" class="space-y-4">
            @csrf

            <!-- STEP 1: Pilih Tahap & Dewan Juri -->
            <div id="del_step_1" class="space-y-4">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pilih lingkup tahap penilaian dan dewan juri yang ingin Anda hapus. Anda dapat menghapus seluruh tahap sekaligus atau memilih tahap & juri tertentu.
                </p>

                <!-- 1. Pilihan Tahap -->
                <div>
                    <label for="del_stage_select" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                        1. Pilih Tahap Penilaian: <span class="text-rose-500">*</span>
                    </label>
                    <select id="del_stage_select" name="stage_id" onchange="onDeleteStageChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition">
                        <option value="all" selected>Semua Tahap Penilaian (Lengkap Seluruh Tahap)</option>
                        @foreach($criteriaStages as $cat)
                            <option value="{{ $cat->id }}">
                                Tahap: {{ $cat->name }} (Bobot: {{ number_format($cat->weight_percentage, 0) }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Pilihan Dewan Juri -->
                <div>
                    <label for="del_judge_select" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                        2. Pilih Dewan Juri: <span class="text-rose-500">*</span>
                    </label>
                    <select id="del_judge_select" name="judge_id" onchange="updateDeleteSummary()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition">
                        <option value="all" selected>Semua Dewan Juri (Semua Penilai pada Tahap Ini)</option>
                        @foreach($allEvaluatingJudges as $idx => $j)
                            <option value="{{ $j->id }}">
                                Juri {{ $idx + 1 }}{{ $j->name ? ' - ' . $j->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Petunjuk / Help Text -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
                    <div class="font-bold text-slate-700 flex items-center gap-1.5">
                        <span>💡 Opsi yang Tersedia:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                        <li><strong>Semua Tahap & Semua Juri:</strong> Menghapus bersih seluruh skor circle kembali ke status awal.</li>
                        <li><strong>Tahap Tertentu & Semua Juri:</strong> Hanya menghapus penilaian di tahap tersebut dari semua juri.</li>
                        <li><strong>Juri Tertentu:</strong> Hanya menghapus penilaian dari satu juri yang dipilih.</li>
                    </ul>
                </div>

                <!-- Footer Step 1 -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeDeleteAssessmentModal()" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="button" onclick="goToDeleteStep2()" 
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                        <span>Lanjut (Next)</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Konfirmasi Ringkasan & Password Admin -->
            <div id="del_step_2" class="space-y-4 hidden">
                <!-- Ringkasan Target Penghapusan -->
                <div class="p-3.5 rounded-2xl bg-rose-50/80 border border-rose-200/80 text-xs text-rose-950 space-y-2">
                    <div class="font-bold text-rose-900 flex items-center gap-1.5">
                        <span class="text-sm">⚠️</span>
                        <span>Konfirmasi Tindakan Penghapusan:</span>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start justify-between py-0.5 border-b border-rose-100">
                            <span class="text-rose-700 font-medium">Circle Target:</span>
                            <span class="font-bold text-slate-900">{{ $circle->circle_name }} ({{ $circle->circle_code }})</span>
                        </div>
                        <div class="flex items-start justify-between py-0.5 border-b border-rose-100">
                            <span class="text-rose-700 font-medium">Tahap Penilaian:</span>
                            <span id="del_summary_stage" class="font-bold text-rose-800 text-right">Semua Tahap Penilaian</span>
                        </div>
                        <div class="flex items-start justify-between py-0.5">
                            <span class="text-rose-700 font-medium">Dewan Juri:</span>
                            <span id="del_summary_judge" class="font-bold text-rose-800 text-right">Semua Dewan Juri</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-rose-600 italic pt-1">
                        * Nilai yang dihapus tidak dapat dipulihkan. Skor akhir dan status circle akan otomatis dihitung ulang.
                    </p>
                </div>

                <!-- Input Password Admin -->
                <div>
                    <label for="del_admin_password" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                        Masukkan Password Administrator: <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="del_admin_password" name="admin_password" placeholder="Ketik password admin..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition pr-10">
                        <button type="button" onclick="togglePasswordVisibility('del_admin_password', 'icon_del_pwd_toggle')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" id="icon_del_pwd_toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="del_pwd_error" class="text-xs text-rose-600 font-semibold mt-1.5 hidden"></div>
                </div>

                <!-- Footer Step 2 -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="goToDeleteStep1()" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali</span>
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeDeleteAssessmentModal()" 
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" id="btn_submit_delete_assessment"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-sm hover:shadow-md shadow-rose-600/25 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Penilaian Sekarang</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- Modal Bagikan Link & QR Code Penilaian (Hanya untuk Admin) -->
@if(Auth::check() && Auth::user()->role === 'admin')
<div id="shareModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 max-h-[92vh] overflow-y-auto">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                    📱
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">QR Code & Link Penilaian Juri</h3>
                    <p class="text-[11px] text-slate-500">Atur tahap apa yang ingin dibagikan ke juri</p>
                </div>
            </div>
            <button type="button" onclick="closeShareModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <!-- Setup Form: Tahap & Juri -->
        <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
            <!-- 1. Setup Tahap yang Ditampilkan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Tahap yang Ditampilkan:
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
                    Scan menggunakan kamera smartphone juri
                </span>
                <a id="download_qr_btn" href="" target="_blank" download="qr-penilaian-{{ $circle->circle_code }}.png" 
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
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Link Penilaian Siap Dibagikan:</label>
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
@endif
@endsection

@if(Auth::check() && Auth::user()->role === 'admin')
@push('scripts')
<script>
    const baseUrl = @json(route('qcc.circles.assess', $circle));

    function openShareModal() {
        const stageSelect = document.getElementById('share_stage_select');
        if (stageSelect) stageSelect.value = '';

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
        
        let url = new URL(baseUrl);
        if (stageVal && stageVal !== 'all') {
            url.searchParams.set('category_id', stageVal);
        }
        if (checkedJudges.length === 1) {
            url.searchParams.set('judge_id', checkedJudges[0]);
        } else if (checkedJudges.length > 1) {
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

        const text = `*FORM PENILAIAN QCC {{ $circle->year ?? $activeYear ?? date('Y') }}*\n` +
            `Halo ${judgeName},\n` +
            `Berikut link penilaian untuk:\n` +
            `• *Circle:* {{ $circle->circle_name }} ({{ $circle->circle_code }})\n` +
            `• *Tema:* "{{ $circle->theme_title }}"\n` +
            `• *Tahap:* ${stageName}\n\n` +
            `Silakan scan QR Code atau akses link di bawah ini:\n${url}`;

        window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
    }
</script>
@endpush
@endif

@push('scripts')
<script>
    function copySharePointUrl(url, btn) {
        if (!url) return;
        navigator.clipboard.writeText(url).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="text-[10px] font-bold text-emerald-600 px-1">✓ Tersalin!</span>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 2000);
        });
    }

    function selectMatrixStageTab(tabId) {
        document.querySelectorAll('.stage-matrix-panel').forEach(panel => {
            if (tabId === 'all') {
                panel.classList.remove('hidden');
            } else {
                panel.classList.toggle('hidden', panel.dataset.stageId !== String(tabId));
            }
        });

        document.querySelectorAll('.matrix-tab-btn').forEach(btn => {
            const isActive = btn.dataset.tabId === String(tabId);
            if (isActive) {
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-xs');
                btn.classList.remove('bg-slate-100', 'text-slate-700', 'hover:bg-slate-200');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-xs');
                btn.classList.add('bg-slate-100', 'text-slate-700', 'hover:bg-slate-200');
            }
        });
    }

    let allDetailsExpanded = true;

    function toggleStageCriteria(stageId) {
        const tbody = document.getElementById('criteria_tbody_' + stageId);
        const btnText = document.getElementById('text_toggle_' + stageId);
        const btnIcon = document.getElementById('icon_toggle_' + stageId);
        const footerText = document.getElementById('footer_toggle_text_' + stageId);
        if (!tbody) return;

        const isHidden = tbody.classList.toggle('hidden');
        if (btnText) btnText.innerText = isHidden ? 'Tampilkan Detail' : 'Sembunyikan Detail';
        if (btnIcon) btnIcon.style.transform = isHidden ? 'rotate(-90deg)' : 'rotate(0deg)';
        if (footerText) footerText.innerText = isHidden ? '▼ Tampilkan Detail Butir Kriteria' : '▲ Sembunyikan Detail (Tampilkan Summary Saja)';
    }

    function toggleAllCriteriaDetails() {
        allDetailsExpanded = !allDetailsExpanded;
        const tbodies = document.querySelectorAll('.stage-criteria-tbody');
        const icon = document.getElementById('icon_toggle_all');
        const text = document.getElementById('text_toggle_all');

        tbodies.forEach(tbody => {
            const stageId = tbody.id.replace('criteria_tbody_', '');
            const btnText = document.getElementById('text_toggle_' + stageId);
            const btnIcon = document.getElementById('icon_toggle_' + stageId);
            const footerText = document.getElementById('footer_toggle_text_' + stageId);

            if (allDetailsExpanded) {
                tbody.classList.remove('hidden');
                if (btnText) btnText.innerText = 'Sembunyikan Detail';
                if (btnIcon) btnIcon.style.transform = 'rotate(0deg)';
                if (footerText) footerText.innerText = '▲ Sembunyikan Detail (Tampilkan Summary Saja)';
            } else {
                tbody.classList.add('hidden');
                if (btnText) btnText.innerText = 'Tampilkan Detail';
                if (btnIcon) btnIcon.style.transform = 'rotate(-90deg)';
                if (footerText) footerText.innerText = '▼ Tampilkan Detail Butir Kriteria';
            }
        });

        const allBtn = document.getElementById('btn_toggle_all_details');
        if (allBtn) {
            allBtn.title = allDetailsExpanded ? 'Sembunyikan Semua Detail' : 'Buka Semua Detail';
        }
        if (icon) icon.style.transform = allDetailsExpanded ? 'rotate(0deg)' : 'rotate(-90deg)';
    }

    let isJudgeNamesRevealed = {{ $isJudgeUnlocked ? 'true' : 'false' }};

    function handleToggleJudgeNames() {
        if (isJudgeNamesRevealed) {
            setJudgeNamesState(false);
            fetch('{{ route('qcc.circles.hide-judges') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        } else {
            openAdminAuthModal();
        }
    }

    function openAdminAuthModal() {
        const modal = document.getElementById('modal_admin_auth');
        const pwd = document.getElementById('admin_auth_password');
        const err = document.getElementById('admin_auth_error');
        if (err) {
            err.innerText = '';
            err.classList.add('hidden');
        }
        if (pwd) pwd.value = '';
        if (modal) modal.classList.remove('hidden');
        setTimeout(() => {
            if (pwd) pwd.focus();
        }, 100);
    }

    function closeAdminAuthModal() {
        const modal = document.getElementById('modal_admin_auth');
        if (modal) modal.classList.add('hidden');
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }
    }

    async function submitAdminAuth(event) {
        event.preventDefault();
        const pwdInput = document.getElementById('admin_auth_password');
        const errBox = document.getElementById('admin_auth_error');
        const btnSubmit = document.getElementById('btn_submit_auth');
        const btnText = document.getElementById('btn_submit_auth_text');

        if (!pwdInput || !pwdInput.value) return;

        errBox.classList.add('hidden');
        btnSubmit.disabled = true;
        btnText.innerText = 'Memverifikasi...';

        try {
            const response = await fetch('{{ route('qcc.circles.verify-admin-password') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: pwdInput.value })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                closeAdminAuthModal();
                setJudgeNamesState(true);
            } else {
                errBox.innerText = data.message || 'Password Administrator tidak cocok.';
                errBox.classList.remove('hidden');
                pwdInput.select();
            }
        } catch (e) {
            errBox.innerText = 'Terjadi kesalahan sistem saat verifikasi. Silakan coba lagi.';
            errBox.classList.remove('hidden');
        } finally {
            btnSubmit.disabled = false;
            btnText.innerText = 'Buka Nama Juri';
        }
    }

    function setJudgeNamesState(revealed) {
        isJudgeNamesRevealed = revealed;
        const anonSpans = document.querySelectorAll('.judge-anon-name');
        const realSpans = document.querySelectorAll('.judge-real-name');
        const toggleBtn = document.getElementById('btn_toggle_judge_names');
        const toggleIcon = document.getElementById('icon_toggle_judge_names');

        anonSpans.forEach(el => el.classList.toggle('hidden', revealed));
        realSpans.forEach(el => el.classList.toggle('hidden', !revealed));

        if (toggleBtn && toggleIcon) {
            if (revealed) {
                toggleBtn.className = 'p-2 sm:p-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 transition inline-flex items-center justify-center shadow-2xs';
                toggleBtn.title = 'Sembunyikan Nama Juri';
                toggleBtn.setAttribute('aria-label', 'Sembunyikan Nama Juri');
                toggleIcon.className = 'w-4 h-4 text-amber-700';
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            } else {
                toggleBtn.className = 'p-2 sm:p-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 transition inline-flex items-center justify-center shadow-2xs';
                toggleBtn.title = 'Tampilkan Nama Juri (Verifikasi Password Admin)';
                toggleBtn.setAttribute('aria-label', 'Tampilkan Nama Juri');
                toggleIcon.className = 'w-4 h-4 text-slate-600';
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>`;
            }
        }
    }

    // Mapping dewan juri yang menilai pada masing-masing tahap
    const deleteStageJudgesMap = {
        'all': [
            @foreach($allEvaluatingJudges as $idx => $j)
                { id: {{ $j->id }}, name: @json($j->name ?? ('Juri ' . ($idx + 1))) },
            @endforeach
        ],
        @foreach($stagesMatrix as $sId => $sData)
        '{{ $sId }}': [
            @foreach($sData['judges'] as $idx => $j)
                { id: {{ $j->id }}, name: @json($j->name ?? ('Juri ' . ($idx + 1))) },
            @endforeach
        ],
        @endforeach
    };

    function openDeleteAssessmentModal() {
        const modal = document.getElementById('deleteAssessmentModal');
        goToDeleteStep1();
        const stageSelect = document.getElementById('del_stage_select');
        if (stageSelect) stageSelect.value = 'all';
        onDeleteStageChange();
        const pwdInput = document.getElementById('del_admin_password');
        if (pwdInput) pwdInput.value = '';
        const errBox = document.getElementById('del_pwd_error');
        if (errBox) { errBox.innerText = ''; errBox.classList.add('hidden'); }
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteAssessmentModal() {
        const modal = document.getElementById('deleteAssessmentModal');
        if (modal) modal.classList.add('hidden');
    }

    function goToDeleteStep1() {
        const s1 = document.getElementById('del_step_1');
        const s2 = document.getElementById('del_step_2');
        if (s1 && s2) {
            s1.classList.remove('hidden');
            s2.classList.add('hidden');
        }
    }

    function goToDeleteStep2() {
        updateDeleteSummary();
        const s1 = document.getElementById('del_step_1');
        const s2 = document.getElementById('del_step_2');
        if (s1 && s2) {
            s1.classList.add('hidden');
            s2.classList.remove('hidden');
        }
        const pwdInput = document.getElementById('del_admin_password');
        if (pwdInput) {
            setTimeout(() => pwdInput.focus(), 100);
        }
    }

    function onDeleteStageChange() {
        const stageSelect = document.getElementById('del_stage_select');
        const judgeSelect = document.getElementById('del_judge_select');
        if (!stageSelect || !judgeSelect) return;

        const stageId = stageSelect.value;
        const availableJudges = deleteStageJudgesMap[stageId] || [];

        judgeSelect.innerHTML = '';

        const optAll = document.createElement('option');
        optAll.value = 'all';
        optAll.textContent = (stageId === 'all') 
            ? 'Semua Dewan Juri (Semua Tahapan)' 
            : (availableJudges.length > 0 
                ? `Semua Dewan Juri pada Tahap ini (${availableJudges.length} Juri)` 
                : 'Semua Dewan Juri (Belum ada juri yang menilai tahap ini)');
        judgeSelect.appendChild(optAll);

        availableJudges.forEach((j, idx) => {
            const opt = document.createElement('option');
            opt.value = j.id;
            opt.textContent = `Juri ${idx + 1} - ${j.name}`;
            judgeSelect.appendChild(opt);
        });

        updateDeleteSummary();
    }

    function updateDeleteSummary() {
        const stageSelect = document.getElementById('del_stage_select');
        const judgeSelect = document.getElementById('del_judge_select');
        const summaryStage = document.getElementById('del_summary_stage');
        const summaryJudge = document.getElementById('del_summary_judge');

        if (stageSelect && summaryStage) {
            summaryStage.textContent = (stageSelect.selectedIndex >= 0) 
                ? stageSelect.options[stageSelect.selectedIndex].text 
                : 'Semua Tahap Penilaian';
        }
        if (judgeSelect && summaryJudge) {
            summaryJudge.textContent = (judgeSelect.selectedIndex >= 0) 
                ? judgeSelect.options[judgeSelect.selectedIndex].text 
                : 'Semua Dewan Juri';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const formDelete = document.getElementById('form_delete_assessment');
        if (formDelete) {
            formDelete.addEventListener('submit', function(e) {
                const pwdInput = document.getElementById('del_admin_password');
                const errBox = document.getElementById('del_pwd_error');
                if (!pwdInput || !pwdInput.value.trim()) {
                    e.preventDefault();
                    if (errBox) {
                        errBox.textContent = 'Password Administrator wajib diisi untuk verifikasi.';
                        errBox.classList.remove('hidden');
                    }
                    if (pwdInput) pwdInput.focus();
                    return false;
                }
                const btnSubmit = document.getElementById('btn_submit_delete_assessment');
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menghapus...</span>
                    `;
                }
            });
        }
    });
</script>
@endpush

