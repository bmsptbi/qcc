@extends('layouts.app')

@section('title', 'Detail Risalah - ' . $circle->circle_name)

@section('content')
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

            <!-- Quick Assess Button -->
            <a href="{{ route('qcc.circles.assess', $circle) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Form Nilai Juri</span>
            </a>
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
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $circle->category->name ?? 'Manufacturing' }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200">
                        {{ $circle->department->name ?? '-' }}
                    </span>
                    @if($circle->award_level)
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-gradient-to-r from-amber-400 to-amber-600 text-white shadow-xs">
                            ★ {{ $circle->award_level }}
                        </span>
                    @endif
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

        <!-- Schedule & Room Bar -->
        <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Jadwal Presentasi</span>
                <span class="text-slate-800 font-bold text-sm">
                    {{ $circle->presentation_schedule ? $circle->presentation_schedule->translatedFormat('d M Y, H:i') . ' WIB' : 'Belum Dijadwalkan' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Lokasi / Ruangan</span>
                <span class="text-slate-800 font-bold text-sm">
                    {{ $circle->presentation_room ?? 'Ruang Konvensi Utama' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Fasilitator Pendamping</span>
                <span class="text-slate-800 font-bold text-sm">
                    {{ $circle->facilitator->name ?? 'Agus Gunawan' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left: Project Details & Background (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Problem Statement -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    1. Latar Belakang & Identifikasi Masalah
                </h2>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 text-slate-700 text-xs sm:text-sm leading-relaxed">
                    {{ $circle->problem_statement ?? 'Belum ada deskripsi masalah.' }}
                </div>

                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2 pt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    2. Sasaran Mutu & Target Perbaikan
                </h2>
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/60 text-emerald-900 text-xs sm:text-sm leading-relaxed">
                    {{ $circle->target_statement ?? 'Belum ada target perbaikan.' }}
                </div>
            </div>

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
                </div>

                <div class="space-y-4">
                    @foreach($stagesMatrix as $stageId => $stageData)
                        @php
                            $cat = $stageData['category'];
                            $stageJudges = $stageData['judges'];
                            $hasJudges = count($stageJudges) > 0;
                        @endphp
                        <div class="p-4 rounded-2xl border {{ $hasJudges ? 'border-slate-200/80 bg-slate-50/50' : 'border-dashed border-slate-200 bg-white' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 pb-2.5 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $hasJudges ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $cat->name }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                        Bobot {{ number_format($stageData['weight'], 0) }}%
                                    </span>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($hasJudges)
                                        <div class="text-right">
                                            <span class="text-[10px] font-semibold text-slate-500 uppercase block">Rata-rata Tahap</span>
                                            <span class="text-sm font-black text-blue-700">
                                                {{ number_format($stageData['stage_stats']['average'], 2) }}
                                                <span class="text-[10px] font-medium text-slate-500">/ 10</span>
                                            </span>
                                        </div>
                                        <div class="text-right pl-3 border-l border-slate-200">
                                            <span class="text-[10px] font-semibold text-slate-500 uppercase block">Kontribusi</span>
                                            <span class="text-sm font-black text-emerald-700">
                                                +{{ number_format($stageData['stage_contribution'], 2) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                            Belum Dinilai
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($hasJudges)
                                <div class="space-y-2">
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">
                                        Dewan Juri Penilai Tahap Ini ({{ count($stageJudges) }} Juri):
                                    </span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach($stageJudges as $judge)
                                            @php
                                                $jScore = $stageData['judge_stage_scores'][$judge->id] ?? null;
                                                $jFb = $stageData['judge_feedback'][$judge->id] ?? null;
                                            @endphp
                                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <div class="font-bold text-xs sm:text-sm text-slate-800">{{ $judge->name }}</div>
                                                        <div class="text-[10px] text-slate-400">{{ $judge->department->name ?? ($judge->nik ?? 'Dewan Juri') }}</div>
                                                    </div>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ ($jFb['status'] ?? '') === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                        {{ ucfirst($jFb['status'] ?? 'Draft') }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                                                    <span class="text-[11px] text-slate-500 font-medium">Skor Tahap:</span>
                                                    <span class="text-sm font-black text-slate-900">
                                                        {{ $jScore !== null ? number_format($jScore, 2) : '-' }}
                                                    </span>
                                                </div>
                                                @if(!empty($jFb['strengths']) || !empty($jFb['improvements']))
                                                    <div class="mt-1.5 pt-1 text-[10px] text-slate-500 space-y-0.5">
                                                        @if(!empty($jFb['strengths']))
                                                            <div class="line-clamp-1"><strong class="text-emerald-700">Kelebihan:</strong> {{ $jFb['strengths'] }}</div>
                                                        @endif
                                                        @if(!empty($jFb['improvements']))
                                                            <div class="line-clamp-1"><strong class="text-amber-700">Perbaikan:</strong> {{ $jFb['improvements'] }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-slate-400 py-1">
                                    <span>Belum ada dewan juri yang memberikan penilaian untuk tahap ini.</span>
                                    <a href="{{ route('qcc.circles.assess', [$circle, 'category_id' => $cat->id]) }}" class="text-blue-600 hover:text-blue-800 font-bold hover:underline inline-flex items-center gap-1">
                                        <span>Beri Nilai Tahap Ini</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Circle Members & Team Structure (1 Col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    Susunan Anggota Gugus
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

            <!-- Documents & Presentation Files -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Dokumen Pendukung</h2>
                
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Risalah_Lengkap_{{ $circle->circle_code }}.pdf</span>
                            <span class="text-[10px] text-slate-400">PDF Dokumentasi 8 Langkah</span>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 hover:underline cursor-pointer">Preview</span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm0-4h1V5h-1v2zM5 5v2H4V5h1zm0 4v2H4V9h1zm0 4v2H4v-2h1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Slide_Presentasi_{{ $circle->circle_code }}.pptx</span>
                            <span class="text-[10px] text-slate-400">PPT Konvensi Mutu</span>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 hover:underline cursor-pointer">Unduh</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Summary & Matriks Penilaian Dewan Juri per Tahap (Format Excel Mockup) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs mb-8 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    Summary & Matriks Penilaian Dewan Juri per Tahap
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Tampilan butir kriteria penilaian, skor masing-masing dewan juri per tahap, statistik deviasi, dan nilai tertimbang.
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Nilai Keseluruhan di Header -->
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white shadow-sm border border-slate-800">
                    <div class="text-right">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-300 block">Nilai Keseluruhan</span>
                    </div>
                    <span class="px-2.5 py-0.5 bg-yellow-300 border border-yellow-400 font-mono font-black text-slate-950 text-base rounded shadow-2xs" title="Total Nilai Akhir Keseluruhan Gugus">
                        {{ number_format($circle->final_score ?? $calculatedFinalScore, 4) }}
                    </span>
                </div>

                <!-- Global Toggle Expand/Collapse Semua Detail Butir -->
                <button type="button" onclick="toggleAllCriteriaDetails()" id="btn_toggle_all_details" 
                    class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-200 transition inline-flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-600 transition-transform duration-200" id="icon_toggle_all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span id="text_toggle_all">Sembunyikan Semua Detail</span>
                </button>

                <!-- Unduh Excel (CSV) -->
                <a href="{{ route('qcc.circles.export', $circle) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Unduh Excel (CSV)</span>
                </a>

                <!-- Print Button -->
                <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Stage Navigation Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100 text-xs font-bold">
            <button type="button" onclick="selectMatrixStageTab('all')" data-tab-id="all" 
                class="matrix-tab-btn px-4 py-2 rounded-xl bg-blue-600 text-white transition shadow-xs whitespace-nowrap">
                Semua Tahap Penilaian
            </button>
            @foreach($stagesMatrix as $stageId => $stageData)
                <button type="button" onclick="selectMatrixStageTab('{{ $stageId }}')" data-tab-id="{{ $stageId }}" 
                    class="matrix-tab-btn px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition whitespace-nowrap">
                    {{ $stageData['category']->name }} ({{ number_format($stageData['weight'], 0) }}%)
                </button>
            @endforeach
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

                            <div class="flex items-center gap-1.5 pl-1">
                                <span class="text-xs font-black uppercase tracking-wider text-slate-500">Nilai Tertimbang:</span>
                                <span class="px-3.5 py-1.5 bg-yellow-300 border border-yellow-400 font-mono font-black text-slate-950 text-base sm:text-lg rounded-md shadow-2xs tracking-wide" title="Nilai Rata-rata Tertimbang Tahap Ini (Skala 0 - 10)">
                                    {{ $stageData['stage_stats']['average'] !== null ? number_format($stageData['stage_stats']['average'], 2) : '-' }}
                                </span>
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
                                            <th class="p-3 text-center border-r border-slate-200 whitespace-nowrap">
                                                {{ $judge->name }}
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

                                    <!-- Row: Nilai Tertimbang (Summary Row Tahap Ini) -->
                                    <tr class="bg-amber-50/80 border-b-2 border-slate-200 text-slate-900 font-bold">
                                        <th class="p-3 font-black text-xs text-slate-900 sticky left-0 bg-amber-50/95 z-10 border-r border-slate-200">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <span>Nilai Tertimbang</span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-200/70 text-amber-900 font-semibold uppercase">Summary</span>
                                            </div>
                                        </th>
                                        @foreach($stageJudges as $judge)
                                            <th class="p-3 text-center font-black text-sm text-blue-800 border-r border-slate-200/80">
                                                {{ $stageData['judge_stage_scores'][$judge->id] ?? '-' }}
                                            </th>
                                        @endforeach
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['max'] !== null ? number_format($stageData['stage_stats']['max'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['min'] !== null ? number_format($stageData['stage_stats']['min'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['range'] !== null ? number_format($stageData['stage_stats']['range'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-black text-sm text-blue-900 bg-blue-100/70 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['average'] !== null ? number_format($stageData['stage_stats']['average'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['deviasi_max'] !== null ? number_format($stageData['stage_stats']['deviasi_max'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-bold text-slate-700 bg-slate-100/60 border-r border-slate-200/80">
                                            {{ $stageData['stage_stats']['deviasi_min'] !== null ? number_format($stageData['stage_stats']['deviasi_min'], 2) : '-' }}
                                        </th>
                                        <th class="p-3 text-center font-black text-purple-900 bg-purple-100/70">
                                            {{ $stageData['stage_stats']['deviasi'] !== null ? number_format($stageData['stage_stats']['deviasi'], 2) . '%' : '-' }}
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
                            <div class="pt-2">
                                <a href="{{ route('qcc.circles.assess', [$circle, 'category_id' => $cat->id]) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition">
                                    <span>Buka Form Penilaian Tahap Ini</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
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

        <!-- Link Preview Box -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Link Penilaian Siap Dibagikan:</label>
            <div class="flex items-center gap-2">
                <input type="text" id="share_url_display" readonly 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-mono text-slate-800 select-all focus:outline-none">
                <button type="button" onclick="copyShareUrl()" id="copy_btn" 
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex-shrink-0 shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <span id="copy_btn_text">Salin Link</span>
                </button>
            </div>
        </div>

        <!-- Quick Share to WhatsApp -->
        <div class="pt-2 flex items-center gap-2">
            <button type="button" onclick="shareToWhatsApp()" 
                class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
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
        document.getElementById('share_url_display').value = fullUrl;

        // Update QR Code Image
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=10&data=' + encodeURIComponent(fullUrl);
        const qrImg = document.getElementById('share_qr_image');
        if (qrImg) qrImg.src = qrUrl;
        const downloadQrBtn = document.getElementById('download_qr_btn');
        if (downloadQrBtn) downloadQrBtn.href = qrUrl;

        document.getElementById('copy_btn_text').innerText = 'Salin Link';
    }

    function copyShareUrl() {
        const input = document.getElementById('share_url_display');
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
        const stageName = stageSelect.options[stageSelect.selectedIndex].text;
        
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
            `• *Gugus:* {{ $circle->circle_name }} ({{ $circle->circle_code }})\n` +
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

        if (text) text.innerText = allDetailsExpanded ? 'Sembunyikan Semua Detail' : 'Buka Semua Detail';
        if (icon) icon.style.transform = allDetailsExpanded ? 'rotate(0deg)' : 'rotate(-90deg)';
    }
</script>
@endpush

