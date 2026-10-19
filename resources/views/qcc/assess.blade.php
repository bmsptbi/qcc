@extends('layouts.app')

@section('title', 'Form Penilaian - ' . $circle->circle_name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-36">

    <!-- Back Button & Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>

        <!-- Status Penilaian Saat Ini -->
        <div class="flex items-center gap-2">
            @if($existingAssessment)
                @if($existingAssessment->status === 'submitted')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Sudah Disubmit Final
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                        Mode Draft Tersimpan
                    </span>
                @endif
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                    Penilaian Baru
                </span>
            @endif
        </div>
    </div>

    <!-- Judge Identity Banner (Muncul saat buka form) -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-800 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0 shadow-inner">
                ⚖️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    @if($currentJudge)
                        <span class="text-[10px] uppercase font-black tracking-wider px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                            Juri Penilai Aktif
                        </span>
                        @if($currentJudge->nik)
                            <span class="text-[11px] text-slate-300 font-mono">NIK: {{ $currentJudge->nik }}</span>
                        @endif
                    @else
                        <span class="text-[10px] uppercase font-black tracking-wider px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 animate-pulse">
                            Wajib Pilih Identitas Juri
                        </span>
                    @endif
                </div>
                <h2 class="text-base sm:text-lg font-black tracking-tight text-white mt-0.5">
                    {{ $currentJudge?->name ?? 'Belum Memilih Juri' }}
                </h2>
                <p class="text-[11px] sm:text-xs text-slate-300">
                    {{ $currentJudge?->department->name ?? 'Silakan pilih nama Anda untuk mengaktifkan lembar penilaian' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Button Ganti Juri -->
            <button type="button" onclick="openJudgeModal()" class="px-3.5 py-2 rounded-xl {{ ! $currentJudge ? 'bg-amber-400 hover:bg-amber-300 text-slate-950 font-black animate-pulse' : 'bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold' }} text-xs transition flex items-center gap-1.5 shadow-xs">
                <svg class="w-3.5 h-3.5 {{ ! $currentJudge ? 'text-slate-900' : 'text-amber-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span>{{ $currentJudge ? 'Ganti Juri' : 'Pilih Nama Juri' }}</span>
            </button>

            <!-- Button Share Link Penilaian (Hanya untuk Admin) -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <button type="button" onclick="openShareModal()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md shadow-emerald-950/20" title="Buat Link & QR Code Penilaian">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Bagikan Link & QR</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Circle Summary Card -->
    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/90 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800">
                        {{ $circle->circle_code }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $circle->category->name ?? 'Manufacturing' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200">
                        {{ $circle->department->name ?? '-' }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    {{ $circle->circle_name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1 leading-snug">
                    "{{ $circle->theme_title }}"
                </p>
            </div>
        </div>

        <!-- Collapsible Problem & Target Statement (Accordion) -->
        <div class="mt-4 pt-3 border-t border-slate-100">
            <details class="group cursor-pointer">
                <summary class="flex items-center justify-between text-xs font-semibold text-blue-600 hover:text-blue-800 select-none">
                    <span>Lihat Ringkasan Masalah & Target Perbaikan</span>
                    <svg class="w-4 h-4 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="mt-3 p-3.5 bg-slate-50 rounded-2xl text-xs space-y-2 border border-slate-200/60 leading-relaxed text-slate-700">
                    <div>
                        <strong class="text-slate-900 block font-bold mb-0.5">Latar Belakang Permasalahan:</strong>
                        <p>{{ $circle->problem_statement ?? 'Belum ada uraian masalah.' }}</p>
                    </div>
                    <div>
                        <strong class="text-slate-900 block font-bold mb-0.5">Sasaran & Target:</strong>
                        <p>{{ $circle->target_statement ?? 'Belum ada target spesifik.' }}</p>
                    </div>
                </div>
            </details>
        </div>
    </div>

    <!-- Assessment Form Start -->
    <form id="assessmentForm" method="POST" action="{{ route('qcc.circles.assess.store', $circle) }}">
        @csrf

        <!-- Hidden input for active judge id -->
        <input type="hidden" name="judge_id" value="{{ $currentJudge?->id }}">

        <!-- Stage Filter Navigation Tabs (Setup Tahap Yang Tampil) -->
        <div class="mb-6">
            <div class="flex items-center justify-between gap-2 mb-2.5">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Pilihan Tahap Penilaian:
                </span>
                <span class="text-[11px] text-slate-500 font-medium hidden sm:inline">Klik tahap di bawah untuk fokus menilai tahap tertentu</span>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                <button type="button" onclick="selectStageTab('all')" id="tab_btn_all" 
                    class="stage-tab-btn px-4 py-2 rounded-2xl text-xs sm:text-sm font-extrabold transition shadow-xs flex items-center gap-1.5 flex-shrink-0 {{ ($activeCategoryId == 'all' || !$activeCategoryId) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    <span>Semua Tahap</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($activeCategoryId == 'all' || !$activeCategoryId) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                        100%
                    </span>
                </button>

                @foreach($categoriesWithCriteria as $cat)
                    <button type="button" onclick="selectStageTab({{ $cat->id }})" id="tab_btn_{{ $cat->id }}" 
                        class="stage-tab-btn px-4 py-2 rounded-2xl text-xs sm:text-sm font-extrabold transition shadow-xs flex items-center gap-1.5 flex-shrink-0 {{ ($activeCategoryId == $cat->id) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($activeCategoryId == $cat->id) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ number_format($cat->weight_percentage, 0) }}%
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs mb-6">
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                <span>Kelengkapan Penilaian:</span>
                <span id="progressText">0 / 0 Kriteria Dinilai (0%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-blue-600 to-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Loop PDCA Categories -->
        <div class="space-y-6">
            @foreach($categoriesWithCriteria as $categoryIndex => $cat)
                <div id="stage_card_{{ $cat->id }}" data-category-id="{{ $cat->id }}" 
                    class="category-stage-card bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden {{ ($activeCategoryId && $activeCategoryId != 'all' && $activeCategoryId != $cat->id) ? 'hidden' : '' }}">
                    
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-800 text-white p-4 sm:p-5 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <h2 class="text-base sm:text-lg font-bold tracking-tight">
                                    {{ $cat->name }}
                                </h2>
                            </div>
                            @if($cat->description)
                                <p class="text-[11px] sm:text-xs text-slate-300 mt-0.5 line-clamp-1">
                                    {{ $cat->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Category Weight & Stage Score Badge -->
                        <div class="flex items-center gap-3 text-right flex-shrink-0">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Skor Tahap</span>
                                <span class="text-xs sm:text-sm font-black text-amber-400" id="cat_score_{{ $cat->id }}">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Bobot Tahap</span>
                                <span class="px-2.5 py-1 rounded-lg bg-white/10 text-emerald-300 text-xs sm:text-sm font-extrabold border border-white/10">
                                    {{ number_format($cat->weight_percentage, 0) }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Criteria Items List -->
                    <div class="p-4 sm:p-6 space-y-6 divide-y divide-slate-100">
                        @foreach($cat->criteria as $criterion)
                            @php
                                $existingScore = $existingScores->get($criterion->id);
                                $currentVal = old('scores.' . $criterion->id, $existingScore ? $existingScore->score : null);
                                $currentNote = old('notes.' . $criterion->id, $existingScore?->notes ?? '');
                                $isRated = ($currentVal !== null && $currentVal !== '');
                            @endphp

                            <div class="pt-6 first:pt-0 criterion-row" 
                                data-criterion-id="{{ $criterion->id }}" 
                                data-category-id="{{ $cat->id }}" 
                                data-weight="{{ $criterion->effective_weight }}" 
                                data-cat-weight="{{ $cat->weight_percentage }}">
                                
                                <!-- Top Row: Code, Title & Weight -->
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-start gap-2.5">
                                        <span class="px-2 py-1 rounded-lg text-xs font-black bg-blue-50 text-blue-700 border border-blue-200/80 flex-shrink-0 mt-0.5">
                                            {{ $criterion->code }}
                                        </span>
                                        <div>
                                            <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                                {{ $criterion->title }}
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Weight & Weighted Score Preview -->
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded {{ $criterion->weight !== null ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : 'bg-slate-100 text-slate-600' }} block mb-0.5" title="Bobot butir di tahap ini">
                                            Bobot: {{ number_format($criterion->effective_weight, 1) }}% {{ $criterion->weight === null ? '(Auto)' : '' }}
                                        </span>
                                        <span class="text-[11px] font-bold {{ $isRated ? 'text-blue-600' : 'text-slate-400' }} weighted-subtotal-text" id="subtotal_{{ $criterion->id }}">
                                            {{ $isRated ? 'Sub: ' . number_format(((float)$currentVal * $criterion->effective_weight) / 100, 2) : 'Sub: - (Belum)' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Rubric Guidelines (Collapsible) -->
                                @if($criterion->guidelines)
                                    <details class="group mb-4">
                                        <summary class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 hover:text-blue-600 cursor-pointer select-none">
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Panduan Rubrik Penilaian</span>
                                            <svg class="w-3 h-3 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </summary>
                                        <div class="mt-2 p-3 bg-amber-50/70 border border-amber-200/60 rounded-xl text-xs text-amber-900 leading-relaxed">
                                            {{ $criterion->guidelines }}
                                        </div>
                                    </details>
                                @endif

                                <!-- Touch-Friendly Interactive Score Input Controls (Default Blank / Null) -->
                                <div class="bg-slate-50/80 rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 space-y-3">
                                    
                                    <!-- Stepper & Direct Value Display -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-xs font-semibold text-slate-500">
                                            Skor (0 - 100):
                                            <span id="score_status_{{ $criterion->id }}" class="ml-1 text-[11px] {{ $isRated ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">
                                                {{ $isRated ? '✓ Terisi' : '(Kosong)' }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <!-- Step Minus Button -->
                                            <button type="button" onclick="adjustScore({{ $criterion->id }}, -5)" 
                                                class="w-10 h-10 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 active:scale-95 flex items-center justify-center font-black text-lg transition touch-action-manipulation shadow-xs">
                                                -
                                            </button>

                                            <!-- Direct Number Input (Default Null / Blank) -->
                                            <div class="relative w-24">
                                                <input type="number" 
                                                    id="score_input_{{ $criterion->id }}" 
                                                    name="scores[{{ $criterion->id }}]" 
                                                    value="{{ $isRated ? (float)$currentVal : '' }}" 
                                                    min="0" max="100" step="1"
                                                    placeholder="-"
                                                    oninput="syncFromInput({{ $criterion->id }})"
                                                    class="score-input w-full text-center py-1.5 px-1 bg-white rounded-xl border {{ $isRated ? 'border-blue-500 font-black text-slate-900' : 'border-slate-300 font-normal text-slate-400' }} text-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs">
                                            </div>

                                            <!-- Step Plus Button -->
                                            <button type="button" onclick="adjustScore({{ $criterion->id }}, 5)" 
                                                class="w-10 h-10 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 active:scale-95 flex items-center justify-center font-black text-lg transition touch-action-manipulation shadow-xs">
                                                +
                                            </button>

                                            <!-- Clear / Reset to Null Button -->
                                            <button type="button" onclick="clearScore({{ $criterion->id }})" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Kosongkan nilai butir ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Range Slider -->
                                    <div class="pt-1">
                                        <input type="range" 
                                            id="score_slider_{{ $criterion->id }}" 
                                            value="{{ $isRated ? (float)$currentVal : 0 }}" 
                                            min="0" max="100" step="1"
                                            oninput="syncFromSlider({{ $criterion->id }})"
                                            class="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer touch-action-manipulation {{ $isRated ? 'accent-blue-600' : 'opacity-50' }}">
                                        <div class="flex justify-between text-[10px] text-slate-400 font-bold px-0.5 mt-1">
                                            <span>0 (Kurang)</span>
                                            <span>60 (Cukup)</span>
                                            <span>80 (Baik)</span>
                                            <span>100 (Istimewa)</span>
                                        </div>
                                    </div>

                                    <!-- Fast Preset Pills for Easy One-Tap Scoring -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1">
                                        <span class="text-[10px] font-semibold text-slate-400 mr-1 hidden sm:inline">Pilihan Cepat:</span>
                                        @foreach([65, 75, 80, 85, 90, 95] as $presetVal)
                                            <button type="button" onclick="setPresetScore({{ $criterion->id }}, {{ $presetVal }})"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition touch-action-manipulation {{ ($isRated && (float)$currentVal == $presetVal) ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                                {{ $presetVal }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <!-- Optional Note per Criterion -->
                                    <div class="pt-1">
                                        <input type="text" 
                                            name="notes[{{ $criterion->id }}]" 
                                            value="{{ $currentNote }}" 
                                            placeholder="Catatan khusus butir ini (opsional)..."
                                            class="w-full px-3 py-1.5 text-xs bg-white rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Feedback & General Comments -->
        <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/90 shadow-xs mt-6 space-y-4">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Catatan & Saran Evaluator (Juri)
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    1. Kelebihan & Nilai Unggul Gugus (Strengths)
                </label>
                <textarea name="feedback_strengths" rows="3" 
                    placeholder="Tuliskan keunggulan inovasi, penguasaan metodologi, atau kerjasama tim yang sangat baik..."
                    class="w-full p-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('feedback_strengths', $existingAssessment?->feedback_strengths ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    2. Area Perbaikan & Rekomendasi (Opportunities for Improvement)
                </label>
                <textarea name="feedback_improvements" rows="3" 
                    placeholder="Tuliskan masukan untuk peningkatan standarisasi, analisis risiko, atau langkah perbaikan berikutnya..."
                    class="w-full p-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('feedback_improvements', $existingAssessment?->feedback_improvements ?? '') }}</textarea>
            </div>
        </div>

        <!-- Sticky Floating Bottom Bar -->
        <div class="fixed bottom-0 left-0 md:left-20 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-2xl px-4 py-3 sm:py-4 transition-[left] duration-300">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                
                <!-- Live Total Score Display -->
                <div class="flex items-center justify-between sm:justify-start gap-3 w-full sm:w-auto">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Nilai Tertimbang (Grand Score)</span>
                        <div class="flex items-baseline gap-1.5">
                            <span id="floatingTotalScore" class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">0.00</span>
                            <span class="text-xs font-semibold text-slate-400">/ 100</span>
                        </div>
                    </div>

                    <!-- Medal Preview Badge -->
                    <div id="medalBadge" class="px-3 py-1 rounded-full text-xs font-black shadow-xs bg-slate-100 text-slate-600">
                        Belum Dinilai
                    </div>
                </div>

                <!-- Action Buttons: Save Draft vs Submit Final -->
                <div class="grid grid-cols-2 sm:flex items-center gap-2.5 w-full sm:w-auto">
                    <!-- Save Draft Button -->
                    <button type="submit" name="action" value="draft" 
                        class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition touch-action-manipulation text-center">
                        Simpan Draft
                    </button>

                    <!-- Final Submit Button with Confirmation Trigger -->
                    <button type="button" onclick="confirmSubmit()"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs sm:text-sm shadow-lg shadow-emerald-600/25 transition touch-action-manipulation text-center flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Kirim Final</span>
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>

<!-- Modal Pilih / Ganti Juri Penilai -->
<div id="judgeModal" class="fixed inset-0 z-50 bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-4 {{ $mustSelectJudge ? '' : 'hidden' }}">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xl">⚖️</span>
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        {{ $mustSelectJudge ? 'Pilih Identitas Juri Anda' : 'Ganti Juri Penilai' }}
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        {{ ! empty($allowedJudgeIds) ? 'Dikhususkan untuk tim juri yang ditugaskan' : 'Pilih nama Anda dari daftar dewan juri' }}
                    </p>
                </div>
            </div>
            @if(! $mustSelectJudge)
                <button type="button" onclick="closeJudgeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            @endif
        </div>

        <p class="text-xs text-slate-600 font-medium">
            Silakan klik nama Anda untuk membuka & mengisi formulir penilaian:
        </p>

        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
            @forelse($selectableJudges as $j)
                <button type="button" onclick="selectJudge({{ $j->id }})" 
                    class="w-full text-left p-3 rounded-2xl border transition flex items-center justify-between gap-3 {{ $currentJudge?->id == $j->id ? 'bg-blue-50/80 border-blue-400 text-blue-950 font-bold shadow-xs' : 'bg-slate-50 border-slate-200/80 text-slate-700 hover:bg-blue-50/60 hover:border-blue-300' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($j->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-xs sm:text-sm font-bold block">{{ $j->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $j->nik ?? 'NIK: -' }} • {{ $j->department->name ?? 'Dewan Juri' }}</span>
                        </div>
                    </div>
                    @if($currentJudge?->id == $j->id)
                        <span class="text-xs font-black text-blue-600">Aktif</span>
                    @else
                        <span class="text-xs font-semibold text-blue-600">Pilih &rarr;</span>
                    @endif
                </button>
            @empty
                <div class="text-center py-6 text-xs text-slate-400">
                    Tidak ada juri yang cocok dengan penugasan link ini.
                </div>
            @endforelse
        </div>

        @if(! $mustSelectJudge)
            <div class="pt-2 border-t border-slate-100 text-right">
                <button type="button" onclick="closeJudgeModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Tutup
                </button>
            </div>
        @endif
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
                    @foreach($categoriesWithCriteria as $cat)
                        <option value="{{ $cat->id }}" {{ $activeCategoryId == $cat->id ? 'selected' : '' }}>
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

<!-- Confirmation Modal for Final Submit -->
<div id="submitModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4">
        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-900">Konfirmasi Kirim Penilaian</h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Anda akan mengirimkan nilai akhir untuk <strong class="text-slate-800">{{ $circle->circle_name }}</strong> sebagai <strong class="text-slate-800">{{ $currentJudge?->name }}</strong>.
                Total nilai tertimbang saat ini adalah <strong id="modalFinalScore" class="text-emerald-700 font-black">0.00</strong>.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Periksa Lagi
            </button>
            <button type="button" onclick="submitFinalForm()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/30">
                Ya, Kirim Final
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const baseUrl = "{{ url('/circles/' . $circle->id . '/assess') }}";
    let activeStageTab = "{{ $activeCategoryId ?? 'all' }}";

    // Tab Stage Switcher
    function selectStageTab(stageId) {
        activeStageTab = stageId;
        const cards = document.querySelectorAll('.category-stage-card');
        const tabBtns = document.querySelectorAll('.stage-tab-btn');

        tabBtns.forEach(btn => {
            btn.className = 'stage-tab-btn px-4 py-2 rounded-2xl text-xs sm:text-sm font-extrabold transition shadow-xs flex items-center gap-1.5 flex-shrink-0 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50';
            const badge = btn.querySelector('span:last-child');
            if (badge) badge.className = 'px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-600';
        });

        const activeBtn = document.getElementById('tab_btn_' + stageId);
        if (activeBtn) {
            activeBtn.className = 'stage-tab-btn px-4 py-2 rounded-2xl text-xs sm:text-sm font-extrabold transition shadow-xs flex items-center gap-1.5 flex-shrink-0 bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20';
            const badge = activeBtn.querySelector('span:last-child');
            if (badge) badge.className = 'px-1.5 py-0.5 rounded-full text-[10px] bg-white/20 text-white';
        }

        cards.forEach(card => {
            const cardCatId = card.getAttribute('data-category-id');
            if (stageId === 'all' || cardCatId == stageId) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // Real-time Score Calculation Script (Default NULL aware)
    function recalculateScores() {
        const rows = document.querySelectorAll('.criterion-row');
        let filledCount = 0;
        const totalCount = rows.length;

        // Group subtotals & counts by category
        const catSubtotals = {};
        const catWeights = {};
        const catRatedCounts = {};

        rows.forEach(row => {
            const id = row.getAttribute('data-criterion-id');
            const catId = row.getAttribute('data-category-id');
            const weight = parseFloat(row.getAttribute('data-weight')) || 1.0;
            const catWeight = parseFloat(row.getAttribute('data-cat-weight')) || 100.0;
            const input = document.getElementById('score_input_' + id);
            const slider = document.getElementById('score_slider_' + id);
            const subEl = document.getElementById('subtotal_' + id);
            const statusEl = document.getElementById('score_status_' + id);

            if (catSubtotals[catId] === undefined) {
                catSubtotals[catId] = 0;
                catWeights[catId] = catWeight;
                catRatedCounts[catId] = 0;
            }

            if (input) {
                const rawVal = input.value.trim();

                if (rawVal !== '' && !isNaN(rawVal)) {
                    let val = parseFloat(rawVal);
                    if (val < 0) val = 0;
                    if (val > 100) val = 100;

                    filledCount++;
                    catRatedCounts[catId]++;

                    // Subtotal point in category
                    const subInCat = (val * weight) / 100;
                    catSubtotals[catId] += subInCat;

                    if (subEl) {
                        subEl.innerText = 'Sub: ' + subInCat.toFixed(2);
                        subEl.className = 'text-[11px] font-bold text-blue-600 weighted-subtotal-text';
                    }
                    if (statusEl) {
                        statusEl.innerText = '✓ ' + val;
                        statusEl.className = 'ml-1 text-[11px] text-emerald-600 font-bold';
                    }
                    if (slider) {
                        slider.classList.remove('opacity-50');
                        slider.classList.add('accent-blue-600');
                    }
                    input.className = 'score-input w-full text-center py-1.5 px-1 bg-white rounded-xl border border-blue-500 font-black text-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs';
                } else {
                    if (subEl) {
                        subEl.innerText = 'Sub: - (Belum)';
                        subEl.className = 'text-[11px] font-semibold text-slate-400 weighted-subtotal-text';
                    }
                    if (statusEl) {
                        statusEl.innerText = '(Kosong)';
                        statusEl.className = 'ml-1 text-[11px] text-slate-400';
                    }
                    if (slider) {
                        slider.classList.add('opacity-50');
                        slider.classList.remove('accent-blue-600');
                    }
                    input.className = 'score-input w-full text-center py-1.5 px-1 bg-white rounded-xl border border-slate-300 font-normal text-lg text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs';
                }
            }
        });

        // Calculate Grand Total Weighted Score: sum of (catSubtotal * catWeight / 100)
        let grandTotalWeighted = 0;
        for (const [catId, scoreInCat] of Object.entries(catSubtotals)) {
            const catEl = document.getElementById('cat_score_' + catId);
            const ratedCount = catRatedCounts[catId] || 0;
            if (catEl) {
                catEl.innerText = ratedCount > 0 ? scoreInCat.toFixed(2) : '-';
            }
            const catWeight = catWeights[catId] || 100.0;
            grandTotalWeighted += (scoreInCat * catWeight) / 100;
        }

        // Update Floating Score
        const floatingEl = document.getElementById('floatingTotalScore');
        if (floatingEl) {
            floatingEl.innerText = grandTotalWeighted.toFixed(2);
        }

        const modalScoreEl = document.getElementById('modalFinalScore');
        if (modalScoreEl) {
            modalScoreEl.innerText = grandTotalWeighted.toFixed(2);
        }

        // Update Progress Bar
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const pct = totalCount > 0 ? Math.round((filledCount / totalCount) * 100) : 0;
        if (progressBar) progressBar.style.width = pct + '%';
        if (progressText) progressText.innerText = filledCount + ' / ' + totalCount + ' Kriteria Dinilai (' + pct + '%)';

        // Update Medal Preview
        const medalBadge = document.getElementById('medalBadge');
        if (medalBadge) {
            if (filledCount === 0) {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold shadow-xs bg-slate-100 text-slate-500';
                medalBadge.innerText = 'Belum Dinilai';
            } else if (grandTotalWeighted >= 90) {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-black shadow-xs bg-gradient-to-r from-cyan-500 to-blue-600 text-white';
                medalBadge.innerText = '★ Diamond (' + grandTotalWeighted.toFixed(1) + ')';
            } else if (grandTotalWeighted >= 85) {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-black shadow-xs bg-gradient-to-r from-slate-300 to-slate-500 text-white';
                medalBadge.innerText = '★ Platinum (' + grandTotalWeighted.toFixed(1) + ')';
            } else if (grandTotalWeighted >= 80) {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-black shadow-xs bg-gradient-to-r from-amber-400 to-amber-600 text-white';
                medalBadge.innerText = '★ Gold (' + grandTotalWeighted.toFixed(1) + ')';
            } else if (grandTotalWeighted >= 75) {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-black shadow-xs bg-gradient-to-r from-slate-200 to-slate-400 text-slate-800';
                medalBadge.innerText = '★ Silver (' + grandTotalWeighted.toFixed(1) + ')';
            } else {
                medalBadge.className = 'px-3 py-1 rounded-full text-xs font-black shadow-xs bg-gradient-to-r from-amber-700 to-amber-900 text-amber-100';
                medalBadge.innerText = '★ Bronze (' + grandTotalWeighted.toFixed(1) + ')';
            }
        }
    }

    function syncFromInput(id) {
        const input = document.getElementById('score_input_' + id);
        const slider = document.getElementById('score_slider_' + id);
        if (input) {
            const raw = input.value.trim();
            if (raw !== '') {
                let val = parseInt(raw);
                if (isNaN(val)) val = 0;
                if (val < 0) val = 0;
                if (val > 100) val = 100;
                if (slider) slider.value = val;
            }
        }
        recalculateScores();
    }

    function syncFromSlider(id) {
        const input = document.getElementById('score_input_' + id);
        const slider = document.getElementById('score_slider_' + id);
        if (input && slider) {
            input.value = slider.value;
        }
        recalculateScores();
    }

    function adjustScore(id, delta) {
        const input = document.getElementById('score_input_' + id);
        if (input) {
            let current = parseInt(input.value);
            if (isNaN(current)) current = 75; // Starting point if adjusting from blank
            let next = Math.max(0, Math.min(100, current + delta));
            input.value = next;
            syncFromInput(id);
        }
    }

    function setPresetScore(id, val) {
        const input = document.getElementById('score_input_' + id);
        if (input) {
            input.value = val;
            syncFromInput(id);
        }
    }

    function clearScore(id) {
        const input = document.getElementById('score_input_' + id);
        const slider = document.getElementById('score_slider_' + id);
        if (input) input.value = '';
        if (slider) slider.value = 0;
        recalculateScores();
    }

    // Modal Handlers
    function confirmSubmit() {
        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function submitFinalForm() {
        if (!@json($currentJudge?->id)) {
            openJudgeModal();
            alert('Silakan pilih identitas juri Anda terlebih dahulu sebelum menyimpan penilaian.');
            return;
        }
        const form = document.getElementById('assessmentForm');
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'submit';
        form.appendChild(actionInput);
        form.submit();
    }

    // Judge Switcher Modal Handlers
    function openJudgeModal() {
        document.getElementById('judgeModal').classList.remove('hidden');
    }

    function closeJudgeModal() {
        document.getElementById('judgeModal').classList.add('hidden');
    }

    function selectJudge(judgeId) {
        // Reload current page with judge_id parameter and preserve stage tab & judges filter
        let url = new URL(window.location.href);
        url.searchParams.set('judge_id', judgeId);
        url.searchParams.delete('choose_judge');
        if (activeStageTab && activeStageTab !== 'all') {
            url.searchParams.set('category_id', activeStageTab);
        }
        window.location.href = url.toString();
    }

    // Share Modal Handlers
    function openShareModal() {
        document.querySelectorAll('.share-judge-cb').forEach(cb => cb.checked = false);
        const toggleBtn = document.getElementById('toggle_all_judges_btn');
        if (toggleBtn) toggleBtn.innerText = 'Pilih Semua';
        updateGeneratedShareUrl();
        document.getElementById('shareModal').classList.remove('hidden');
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

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        if (activeStageTab) {
            selectStageTab(activeStageTab);
        }
        recalculateScores();
    });
</script>
@endpush
