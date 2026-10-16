@extends('layouts.app')

@section('title', 'Detail Risalah - ' . $circle->circle_name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>

        <!-- Quick Assess Button -->
        <a href="{{ route('qcc.circles.assess', $circle) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Form Nilai Juri</span>
        </a>
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
            @if($circle->final_score)
                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white text-center min-w-[140px] shadow-lg">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 block">Rata-rata Skor</span>
                    <span class="text-3xl sm:text-4xl font-black text-amber-400 block my-1">
                        {{ number_format($circle->final_score, 2) }}
                    </span>
                    <span class="text-[11px] font-semibold text-slate-300">
                        Dari {{ $circle->assessments->count() }} Juri
                    </span>
                </div>
            @endif
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

            <!-- Assessments Summary from Judges -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 tracking-tight mb-4 flex items-center justify-between">
                    <span>Rekap Lembar Penilaian Juri</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700">
                        {{ $circle->assessments->count() }} Penilaian Masuk
                    </span>
                </h2>

                <div class="space-y-3">
                    @forelse($circle->assessments as $ass)
                        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-slate-900">{{ $ass->judge->name ?? 'Juri' }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $ass->status === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($ass->status) }}
                                    </span>
                                </div>
                                @if($ass->feedback_strengths)
                                    <p class="text-xs text-slate-600 mt-1 line-clamp-1">
                                        <strong>Kelebihan:</strong> {{ $ass->feedback_strengths }}
                                    </p>
                                @endif
                            </div>

                            <div class="text-right flex-shrink-0">
                                <span class="text-xl font-black text-slate-900">{{ number_format($ass->weighted_score, 2) }}</span>
                                <span class="text-xs text-slate-400 block font-semibold">Skor Tertimbang</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada juri yang mengirimkan penilaian untuk gugus ini.
                        </div>
                    @endforelse
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

</div>
@endsection
