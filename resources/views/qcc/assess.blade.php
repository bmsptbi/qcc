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
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
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
    <form id="assessmentForm" method="POST" action="{{ route('qcc.circles.assess.store', $circle) }}" onsubmit="return validateBeforeSubmit(event)">
        @csrf

        <!-- Hidden input for active judge id -->
        <input type="hidden" name="judge_id" value="{{ $currentJudge?->id }}">
        <input type="hidden" name="category_id" value="{{ $isStageLocked ? $lockedCategory?->id : '' }}">

        <!-- Stage Filter Navigation Tabs (Setup Tahap Yang Tampil) -->
        @if(! $isStageLocked)
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
        @else
        <!-- Locked Stage Banner (Tidak Menampilkan Pilihan Tahap Agar Tidak Bisa Pindah Tahap) -->
        <div class="mb-6 p-4.5 rounded-3xl bg-gradient-to-r from-blue-50 via-indigo-50/60 to-slate-50 border border-blue-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 flex-shrink-0">
                    🎯
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 block">Fokus Penilaian Tahap Tertentu</span>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                        {{ $lockedCategory?->name ?? 'Tahap Penilaian Khusus' }}
                    </h3>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl bg-white text-blue-700 font-extrabold text-xs sm:text-sm shadow-2xs border border-blue-200">
                    Bobot Tahap: {{ number_format($lockedCategory?->weight_percentage ?? 0, 0) }}%
                </span>
            </div>
        </div>
        @endif

        <!-- Edit Mode Active Notification Banner -->
        <div id="editModeBanner" class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-3 {{ ($hasAlreadyAssessed && request()->has('edit_confirmed')) ? '' : 'hidden' }}">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">✏️</span>
                <div>
                    <strong class="font-bold block text-sm">Mode Edit Penilaian Aktif</strong>
                    <span>Nilai yang telah tersimpan sebelumnya sudah dimuat ke form. Anda dapat melakukan perubahan penilaian dan menyimpan kembali.</span>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('editModeBanner').classList.add('hidden')" class="text-amber-600 hover:text-amber-800 font-bold text-base leading-none">
                &times;
            </button>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-bold text-slate-700 mb-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span>Kelengkapan Penilaian:</span>
                    <button type="button" onclick="openIncompleteModal()" id="incompleteBadgeBtn" 
                        class="hidden text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-0.5 rounded-lg border border-rose-200 transition items-center gap-1 shadow-2xs">
                        <span>⚠️ Masih ada</span>
                        <span id="incompleteCountText" class="underline font-black">0</span>
                        <span>kriteria belum dinilai (Lihat &rarr;)</span>
                    </button>
                </div>
                <span id="progressText" class="text-slate-600 font-mono">0 / 0 Kriteria Dinilai (0%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-blue-600 to-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Loop PDCA Categories -->
        <div class="space-y-6">
            @foreach($categoriesWithCriteria as $categoryIndex => $cat)
                @php
                    $isCategoryHidden = $isStageLocked ? ($cat->id !== $lockedCategory?->id) : ($activeCategoryId && $activeCategoryId !== 'all' && $activeCategoryId != $cat->id);
                @endphp
                <div id="stage_card_{{ $cat->id }}" data-category-id="{{ $cat->id }}" 
                    class="category-stage-card bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden {{ $isCategoryHidden ? 'hidden' : '' }}">
                    
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

                    <!-- Criteria Items List (Ultra-Compact Layout) -->
                    <div class="p-3 sm:p-4 space-y-2.5">
                        @foreach($cat->criteria as $criterion)
                            @php
                                $existingScore = $existingScores->get($criterion->id);
                                $currentVal = old('scores.' . $criterion->id, $existingScore ? $existingScore->score : null);
                                $currentNote = old('notes.' . $criterion->id, $existingScore?->notes ?? '');
                                $isRated = ($currentVal !== null && $currentVal !== '');
                            @endphp

                            <div id="criterion_row_{{ $criterion->id }}"
                                class="criterion-row p-3 rounded-2xl border transition-all duration-150 {{ $isRated ? 'bg-white border-blue-200/90 shadow-2xs' : 'bg-white border-slate-200/90 hover:border-slate-300' }}"
                                data-criterion-id="{{ $criterion->id }}"
                                data-criterion-code="{{ $criterion->code }}"
                                data-criterion-title="{{ $criterion->title }}"
                                data-category-id="{{ $cat->id }}"
                                data-cat-name="{{ $cat->name }}"
                                data-weight="{{ $criterion->effective_weight }}"
                                data-cat-weight="{{ $cat->weight_percentage }}">
                                
                                <!-- Main Row: Code, Title, Weight, Rubric Icon & Stepper Controls -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                    
                                    <!-- Left: Code & Title -->
                                    <div class="flex items-start gap-2 flex-1 min-w-0">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-black bg-blue-50 text-blue-700 border border-blue-200/70 flex-shrink-0 mt-0.5">
                                            {{ $criterion->code }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 leading-snug">
                                                    {{ $criterion->title }}
                                                </h3>
                                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 flex-shrink-0" title="Bobot butir di tahap ini">
                                                    Bobot: {{ number_format($criterion->effective_weight, 1) }}%
                                                </span>
                                                @if($criterion->guidelines)
                                                    <button type="button" onclick="toggleRubric({{ $criterion->id }})" 
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50/80 px-2 py-0.5 rounded-md hover:bg-blue-100 transition flex-shrink-0"
                                                        title="Buka petunjuk dan pedoman penilaian butir ini">
                                                        <span>📋 Petunjuk Nilai</span>
                                                    </button>
                                                @endif

                                                <!-- Note toggle button -->
                                                <button type="button" onclick="toggleNoteInput({{ $criterion->id }})" 
                                                    id="note_btn_{{ $criterion->id }}"
                                                    class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md transition flex-shrink-0 {{ !empty($currentNote) ? 'text-blue-700 bg-blue-50 border border-blue-200' : 'text-slate-500 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 border border-slate-200/80' }}"
                                                    title="Tambah / lihat catatan untuk butir ini">
                                                    <span>📝</span>
                                                    <span id="note_label_{{ $criterion->id }}">{{ !empty($currentNote) ? 'Catatan (Ada)' : 'Catatan' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Touch Stepper & Score Controls -->
                                    <div class="flex items-center justify-between sm:justify-end gap-1.5 flex-shrink-0">
                                        <!-- Step Minus Button -->
                                        <button type="button" onclick="adjustScore({{ $criterion->id }}, -1)"
                                            class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 active:scale-95 flex items-center justify-center font-black text-sm transition touch-action-manipulation shadow-2xs">
                                            -
                                        </button>

                                        <!-- Direct Number Input -->
                                        <div class="relative w-14 sm:w-16">
                                            <input type="number"
                                                id="score_input_{{ $criterion->id }}"
                                                name="scores[{{ $criterion->id }}]"
                                                value="{{ $isRated ? (float)$currentVal : '' }}"
                                                min="0" max="10" step="any"
                                                placeholder="-"
                                                oninput="handleScoreInput({{ $criterion->id }})"
                                                class="score-input w-full text-center h-8 px-1 bg-white rounded-xl border {{ $isRated ? 'border-blue-500 font-black text-slate-900 ring-1 ring-blue-500/20' : 'border-slate-300 font-semibold text-slate-400' }} text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 shadow-2xs">
                                        </div>

                                        <!-- Step Plus Button -->
                                        <button type="button" onclick="adjustScore({{ $criterion->id }}, 1)"
                                            class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 active:scale-95 flex items-center justify-center font-black text-sm transition touch-action-manipulation shadow-2xs">
                                            +
                                        </button>

                                        <!-- Subtotal / Status -->
                                        <div class="w-16 sm:w-20 text-right">
                                            <span class="text-[11px] font-extrabold {{ $isRated ? 'text-blue-600' : 'text-slate-400' }} weighted-subtotal-text block truncate" id="subtotal_{{ $criterion->id }}">
                                                {{ $isRated ? 'Sub: ' . number_format(((float)$currentVal * $criterion->effective_weight) / 100, 2) : 'Belum' }}
                                            </span>
                                        </div>

                                        <!-- Clear Button -->
                                        <button type="button" onclick="clearScore({{ $criterion->id }})"
                                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition flex items-center justify-center flex-shrink-0"
                                            title="Kosongkan nilai butir ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                </div>

                                <!-- Sub-Row: Micro Presets (Full Width Grid 1-10, Zero Overflow on Mobile) -->
                                <div class="mt-2 pt-1.5 border-t border-slate-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] text-slate-400 font-semibold hidden sm:inline flex-shrink-0">Pilihan:</span>
                                        <div class="grid grid-cols-10 gap-0.5 sm:gap-1 flex-1 min-w-0">
                                            @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $presetVal)
                                                <button type="button" onclick="setPresetScore({{ $criterion->id }}, {{ $presetVal }})"
                                                    id="preset_{{ $criterion->id }}_{{ $presetVal }}"
                                                    class="preset-pill-{{ $criterion->id }} w-full py-1.5 sm:py-1 rounded-lg text-[11px] sm:text-xs font-black transition touch-action-manipulation text-center {{ ($isRated && (float)$currentVal == $presetVal) ? 'bg-blue-600 text-white shadow-2xs' : 'bg-slate-50 border border-slate-200/80 text-slate-700 hover:bg-slate-100' }}">
                                                    {{ $presetVal }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Collapsible Rubric Guidelines -->
                                @if($criterion->guidelines)
                                    <div id="rubric_box_{{ $criterion->id }}" class="hidden mt-2 p-3 bg-amber-50/90 border border-amber-200/80 rounded-xl text-xs text-amber-950 leading-relaxed shadow-2xs">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <strong class="font-bold flex items-center gap-1.5 text-xs text-amber-900 mb-1">
                                                    <span>📌</span> Petunjuk / Pedoman Penilaian Butir Ini:
                                                </strong>
                                                <span class="text-slate-700 leading-relaxed">{{ $criterion->guidelines }}</span>
                                            </div>
                                            <button type="button" onclick="toggleRubric({{ $criterion->id }})" class="text-amber-700 hover:text-amber-900 font-black text-sm p-1 leading-none">&times;</button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Note Input (Hidden by default unless filled) -->
                                <div id="note_box_{{ $criterion->id }}" class="{{ empty($currentNote) ? 'hidden' : '' }} mt-2 pt-1">
                                    <input type="text"
                                        id="note_input_{{ $criterion->id }}"
                                        name="notes[{{ $criterion->id }}]"
                                        value="{{ $currentNote }}"
                                        oninput="updateNoteState({{ $criterion->id }})"
                                        placeholder="Tulis catatan khusus untuk butir ini..."
                                        class="w-full px-3 py-1.5 text-xs bg-slate-50 focus:bg-white rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 shadow-inner">
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

        <!-- Floating Toast Warning for Score Validation -->
        <div id="scoreWarningToast" class="fixed top-6 left-1/2 -translate-x-1/2 z-50 transition-all duration-300 opacity-0 pointer-events-none -translate-y-4 max-w-md w-[92%] sm:w-auto">
            <div class="bg-rose-600 text-white text-xs sm:text-sm font-bold px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2.5 border border-rose-400/50">
                <span class="text-base sm:text-lg">⚠️</span>
                <span id="scoreWarningMessage">Nilai maksimal adalah 10!</span>
            </div>
        </div>

        <!-- Sticky Floating Bottom Bar -->
        <div class="fixed bottom-0 left-0 md:left-20 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-2xl px-4 py-3 sm:py-4 transition-[left] duration-300">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                
                <!-- Live Stage-Relative Score Display (Nilai Tertimbang Terhadap Parent Tahap) -->
                <div class="flex items-center justify-between sm:justify-start gap-3 w-full sm:w-auto">
                    <div>
                        <span class="text-[10px] uppercase font-extrabold text-slate-500 tracking-wider block" id="floatingScoreLabel">
                            @if($isStageLocked && $lockedCategory)
                                Nilai Tahap ({{ $lockedCategory->name }})
                            @else
                                Nilai Tertimbang Tahap
                            @endif
                        </span>
                        <div class="flex items-baseline gap-1.5">
                            <span id="floatingTotalScore" class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">0.00</span>
                            <span class="text-xs font-bold text-slate-400">/ 10</span>
                        </div>
                        <span id="floatingScoreSub" class="text-[10px] text-slate-500 font-medium block">
                            @if($isStageLocked && $lockedCategory)
                                Bobot Tahap: {{ number_format($lockedCategory->weight_percentage, 0) }}% | Kontribusi: 0.00
                            @else
                                Nilai tertimbang terhadap parent (tahap)
                            @endif
                        </span>
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

        <!-- Search Input Field -->
        <div class="relative">
            <input type="text"
                id="searchJudgeInput"
                oninput="filterJudgesList()"
                placeholder="Cari nama juri, NIK, atau departemen..."
                class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition shadow-inner">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <button type="button" 
                onclick="clearJudgeSearch()" 
                id="clearJudgeSearchBtn" 
                class="hidden text-slate-400 hover:text-slate-600 absolute right-2.5 top-2.5 text-base font-bold w-5 h-5 flex items-center justify-center">
                &times;
            </button>
        </div>

        <div class="space-y-2 max-h-72 overflow-y-auto pr-1" id="judgeListContainer">
            @forelse($selectableJudges as $j)
                @php
                    $isAssessed = in_array($j->id, $assessedJudgeIds ?? []);
                    $searchData = strtolower($j->name . ' ' . ($j->nik ?? '') . ' ' . ($j->department->name ?? ''));
                @endphp
                <button type="button" onclick="selectJudge({{ $j->id }})" 
                    data-search="{{ $searchData }}"
                    class="judge-item-btn w-full text-left p-3 rounded-2xl border transition flex items-center justify-between gap-3 {{ $currentJudge?->id == $j->id ? 'bg-blue-50/80 border-blue-400 text-blue-950 font-bold shadow-xs' : 'bg-slate-50 border-slate-200/80 text-slate-700 hover:bg-blue-50/60 hover:border-blue-300' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($j->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-xs sm:text-sm font-bold block">{{ $j->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $j->nik ?? 'NIK: -' }} • {{ $j->department->name ?? 'Dewan Juri' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($isAssessed)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                                <span>✓</span> Sudah Menilai
                            </span>
                        @endif
                        @if($currentJudge?->id == $j->id)
                            <span class="text-xs font-black text-blue-600">Aktif</span>
                        @else
                            <span class="text-xs font-semibold text-blue-600">Pilih &rarr;</span>
                        @endif
                    </div>
                </button>
            @empty
                <div class="text-center py-6 text-xs text-slate-400">
                    Tidak ada juri yang cocok dengan penugasan link ini.
                </div>
            @endforelse

            <div id="noJudgesFound" class="hidden text-center py-6 text-xs text-slate-500 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="text-base block mb-1">🔍</span>
                Tidak ada juri yang cocok dengan pencarian "<span id="searchKeywordDisplay" class="font-bold text-slate-800"></span>".
            </div>
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

<!-- Popup Konfirmasi Edit Penilaian Jika Juri Sudah Pernah Menilai -->
@if($hasAlreadyAssessed && !request()->has('edit_confirmed') && $currentJudge)
<div id="alreadyAssessedModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto shadow-inner">
            ⚠️
        </div>

        <div class="text-center space-y-2">
            <h3 class="text-lg font-black text-slate-900">
                Pemberitahuan: Penilaian Sudah Terisi
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Halo <strong class="text-slate-900">{{ $currentJudge->name }}</strong>, Anda tercatat <strong>sudah pernah mengisi penilaian</strong> untuk gugus <strong class="text-blue-700">{{ $circle->circle_name }}</strong>
                @if($isStageLocked && $lockedCategory)
                    pada tahap <strong class="text-indigo-700">{{ $lockedCategory->name }}</strong>
                @endif.
            </p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 text-xs space-y-2">
            <div class="flex items-center justify-between text-slate-600">
                <span>Status Penilaian:</span>
                <span class="font-bold px-2 py-0.5 rounded-full {{ $existingAssessment?->status === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $existingAssessment?->status === 'submitted' ? '✓ Final (Submitted)' : 'Draft Tersimpan' }}
                </span>
            </div>
            <div class="flex items-center justify-between text-slate-600">
                <span>Total Nilai Tersimpan:</span>
                <span class="font-black text-blue-700 text-sm">
                    {{ number_format($existingAssessment?->final_score ?? $existingAssessment?->weighted_score ?? $existingAssessment?->total_score ?? 0, 2) }} / 10
                </span>
            </div>
            @if($existingAssessment?->submitted_at)
                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                    <span>Waktu Submit:</span>
                    <span>{{ $existingAssessment->submitted_at->format('d M Y, H:i') }} WIB</span>
                </div>
            @elseif($existingAssessment?->updated_at)
                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                    <span>Terakhir Disimpan:</span>
                    <span>{{ $existingAssessment->updated_at->format('d M Y, H:i') }} WIB</span>
                </div>
            @endif
        </div>

        <p class="text-center text-xs font-semibold text-slate-700">
            Apakah Anda ingin mengedit penilaian yang sudah ada?
        </p>

        <div class="pt-1 space-y-2">
            <button type="button" onclick="confirmEditAssessment()" 
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/25 transition flex items-center justify-center gap-2 touch-action-manipulation">
                <span>✏️ Ya, Edit / Perbarui Penilaian</span>
            </button>
            
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="openJudgeModalFromAssessed()" 
                    class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center">
                    Ganti Juri Lain
                </button>
                <a href="{{ route('qcc.index') }}" 
                    class="py-2.5 px-3 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition text-center">
                    Ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Bagikan Link & QR Code Penilaian (Hanya untuk Admin) -->
@if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->hasRole('admin')))
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

<!-- Modal Kriteria Belum Lengkap (Mencegah Kirim Final & Menampilkan Daftar Butir yang Belum Diisi) -->
<div id="incompleteModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl font-black flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">
                        Penilaian Belum Lengkap
                    </h3>
                    <p class="text-xs text-rose-600 font-bold" id="incompleteModalSubtitle">
                        Masih ada butir penilaian yang belum diisi nilainya
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeIncompleteModal()" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
        </div>

        <div class="p-3 bg-rose-50/80 border border-rose-200/70 rounded-2xl text-xs text-rose-950 leading-relaxed flex-shrink-0">
            <p>
                <strong>Perhatian:</strong> Untuk dapat melakukan <strong>Kirim Final</strong>, seluruh butir penilaian pada tahap ini wajib dinilai lengkap (skala 0 - 10).
                Jika Anda ingin menyimpan penilaian sementara dan melanjutkannya nanti, silakan gunakan <strong>Simpan Draft</strong>.
            </p>
        </div>

        <div class="flex items-center justify-between text-xs font-bold text-slate-700 pt-0.5 flex-shrink-0">
            <span>Daftar Butir yang Belum Diisi:</span>
            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Klik nama butir untuk langsung mengisi</span>
        </div>

        <!-- Scrollable list of missing items -->
        <div id="incompleteItemsList" class="space-y-1.5 overflow-y-auto flex-1 pr-1 max-h-64">
            <!-- populated by JS -->
        </div>

        <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 flex-shrink-0">
            <button type="button" onclick="closeIncompleteModal()" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center">
                Tutup & Lengkapi Nilai
            </button>
            <button type="button" onclick="saveAsDraftFromIncomplete()" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs transition shadow-md shadow-blue-600/20 text-center">
                Simpan Draft Saja
            </button>
        </div>
    </div>
</div>

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
            </p>
        </div>

        <!-- Card Nilai Tertimbang Terhadap Parent (Tahap) -->
        <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-3.5 text-center space-y-1.5">
            <span id="modalScoreSubtitle" class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">
                @if($isStageLocked && $lockedCategory)
                    Nilai Tertimbang Tahap {{ $lockedCategory->name }}
                @else
                    Nilai Tertimbang Tahap
                @endif
            </span>
            <div class="flex items-baseline justify-center gap-1.5">
                <span id="modalFinalScore" class="text-3xl font-black text-emerald-600">0.00</span>
                <span class="text-xs font-bold text-slate-400">/ 10</span>
            </div>
            <div id="modalScoreDetail" class="text-[11px] text-slate-600 font-medium leading-relaxed">
                @if($isStageLocked && $lockedCategory)
                    Nilai tertimbang terhadap parent (tahap).<br>
                    <span class="text-[10px] text-slate-400">Bobot Tahap: {{ number_format($lockedCategory->weight_percentage, 0) }}% &bull; Kontribusi ke Nilai Akhir: 0.00</span>
                @else
                    Nilai tertimbang terhadap parent (tahap).
                @endif
            </div>
            <div id="modalStageBreakdown" class="hidden text-left pt-2.5 mt-2 border-t border-slate-200/80 space-y-1.5 text-xs">
            </div>
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
    const isStageLocked = @json($isStageLocked);
    const lockedCatId = @json($lockedCategory?->id);
    const categoriesMeta = @json($categoriesWithCriteria->mapWithKeys(fn ($c) => [$c->id => [
        'id' => $c->id,
        'name' => $c->name,
        'weight' => (float) $c->weight_percentage,
    ]]));
    let currentActiveCatId = lockedCatId || (activeStageTab !== 'all' ? activeStageTab : null);

    // Tab Stage Switcher
    function selectStageTab(stageId) {
        activeStageTab = stageId;
        if (stageId !== 'all') {
            currentActiveCatId = stageId;
        }
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
        recalculateScores();
    }

    // Compact UI Helpers: Rubric & Note Toggles
    function toggleRubric(id) {
        const box = document.getElementById('rubric_box_' + id);
        if (box) box.classList.toggle('hidden');
    }

    function toggleNoteInput(id) {
        const box = document.getElementById('note_box_' + id);
        const input = document.getElementById('note_input_' + id);
        if (box) {
            box.classList.toggle('hidden');
            if (!box.classList.contains('hidden') && input) {
                input.focus();
            }
        }
    }

    function updateNoteState(id) {
        const input = document.getElementById('note_input_' + id);
        const btn = document.getElementById('note_btn_' + id);
        const label = document.getElementById('note_label_' + id);
        if (input && btn && label) {
            if (input.value.trim() !== '') {
                btn.className = 'inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md transition text-blue-700 bg-blue-50 border border-blue-200 flex-shrink-0';
                label.innerText = 'Catatan (Ada)';
            } else {
                btn.className = 'inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 px-2 py-0.5 rounded-md transition flex-shrink-0';
                label.innerText = 'Catatan';
            }
        }
    }

    // Criteria Completeness Inspection
    function getIncompleteCriteria() {
        const isLocked = @json($isStageLocked);
        const lockedCatId = @json($lockedCategory?->id);

        const targetRows = Array.from(document.querySelectorAll('.criterion-row')).filter(row => {
            const catId = row.getAttribute('data-category-id');
            if (isLocked && lockedCatId) {
                return catId == lockedCatId;
            }
            return true;
        });

        const incomplete = [];
        targetRows.forEach(row => {
            const id = row.getAttribute('data-criterion-id');
            const code = row.getAttribute('data-criterion-code') || id;
            const title = row.getAttribute('data-criterion-title') || ('Kriteria ' + code);
            const catName = row.getAttribute('data-cat-name') || '';
            const categoryId = row.getAttribute('data-category-id');
            const input = document.getElementById('score_input_' + id);
            const val = input ? input.value.trim() : '';

            if (val === '' || isNaN(val)) {
                incomplete.push({ id, code, title, catName, categoryId, row });
            }
        });

        return {
            incomplete,
            total: targetRows.length,
            filled: targetRows.length - incomplete.length
        };
    }

    // Modal Incomplete Handlers
    function openIncompleteModal() {
        const status = getIncompleteCriteria();
        const listEl = document.getElementById('incompleteItemsList');
        const subtitleEl = document.getElementById('incompleteModalSubtitle');

        if (subtitleEl) {
            subtitleEl.innerText = `${status.incomplete.length} dari ${status.total} butir belum dinilai (${status.filled} sudah diisi)`;
        }

        if (listEl) {
            if (status.incomplete.length === 0) {
                listEl.innerHTML = `
                    <div class="text-center py-6 text-emerald-600 font-bold text-xs bg-emerald-50 rounded-2xl border border-emerald-200">
                        ✓ Luar biasa! Seluruh kriteria (${status.total} butir) sudah lengkap terisi nilai.
                    </div>
                `;
            } else {
                listEl.innerHTML = status.incomplete.map((item, idx) => `
                    <button type="button" onclick="jumpToCriterion(${item.id}, ${item.categoryId})"
                        class="w-full text-left p-2.5 rounded-xl border border-slate-200/90 hover:border-blue-400 bg-white hover:bg-blue-50/50 transition flex items-center justify-between gap-2.5 shadow-2xs group">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 font-black text-[11px] flex items-center justify-center flex-shrink-0">
                                ${idx + 1}
                            </span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200/60 flex-shrink-0">
                                ${item.code}
                            </span>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-blue-700">
                                    ${item.title}
                                </span>
                                <span class="text-[10px] text-slate-400 block truncate">
                                    ${item.catName}
                                </span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-blue-600 group-hover:translate-x-0.5 transition flex-shrink-0 flex items-center gap-1">
                            Isi &rarr;
                        </span>
                    </button>
                `).join('');
            }
        }

        document.getElementById('incompleteModal').classList.remove('hidden');
    }

    function closeIncompleteModal() {
        document.getElementById('incompleteModal').classList.add('hidden');
    }

    function saveAsDraftFromIncomplete() {
        closeIncompleteModal();
        const form = document.getElementById('assessmentForm');
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'draft';
        form.appendChild(actionInput);
        form.submit();
    }

    function jumpToCriterion(criterionId, categoryId) {
        closeIncompleteModal();

        if (!@json($isStageLocked) && activeStageTab !== 'all' && activeStageTab != categoryId) {
            selectStageTab(categoryId);
        }

        setTimeout(() => {
            const row = document.getElementById('criterion_row_' + criterionId);
            const input = document.getElementById('score_input_' + criterionId);
            if (row) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.classList.add('ring-4', 'ring-amber-400', 'border-amber-500', 'bg-amber-50/60');
                setTimeout(() => {
                    row.classList.remove('ring-4', 'ring-amber-400', 'border-amber-500', 'bg-amber-50/60');
                }, 3000);
            }
            if (input) {
                input.focus();
                input.select();
            }
        }, 150);
    }

    // Real-time Score Calculation Script
    function recalculateScores() {
        const isLocked = @json($isStageLocked);
        const lockedCatId = @json($lockedCategory?->id);

        const status = getIncompleteCriteria();

        // Group subtotals & counts by category
        const catSubtotals = {};
        const catWeights = {};
        const catRatedCounts = {};

        const allRows = document.querySelectorAll('.criterion-row');
        allRows.forEach(row => {
            const id = row.getAttribute('data-criterion-id');
            const catId = row.getAttribute('data-category-id');
            const weight = parseFloat(row.getAttribute('data-weight')) || 1.0;
            const catWeight = parseFloat(row.getAttribute('data-cat-weight')) || 100.0;
            const input = document.getElementById('score_input_' + id);
            const subEl = document.getElementById('subtotal_' + id);

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
                    if (val > 10) val = 10;

                    catRatedCounts[catId]++;

                    // Subtotal point in category
                    const subInCat = (val * weight) / 100;
                    catSubtotals[catId] += subInCat;

                    if (subEl) {
                        subEl.innerText = 'Sub: ' + subInCat.toFixed(2);
                        subEl.className = 'text-[11px] font-extrabold text-blue-600 weighted-subtotal-text block truncate';
                    }
                    input.className = 'score-input w-full text-center h-8 px-1 bg-white rounded-xl border border-blue-500 font-black text-slate-900 ring-1 ring-blue-500/20 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 shadow-2xs';
                    row.className = 'criterion-row p-3 rounded-2xl border transition-all duration-150 bg-white border-blue-200/90 shadow-2xs';

                    // Update preset pill active states
                    const pills = row.querySelectorAll('[class*="preset-pill-"]');
                    pills.forEach(pill => {
                        const pVal = parseFloat(pill.innerText.trim());
                        if (pVal === val) {
                            pill.className = `preset-pill-${id} w-full py-1.5 sm:py-1 rounded-lg text-[11px] sm:text-xs font-black transition touch-action-manipulation text-center bg-blue-600 text-white shadow-2xs`;
                        } else {
                            pill.className = `preset-pill-${id} w-full py-1.5 sm:py-1 rounded-lg text-[11px] sm:text-xs font-black transition touch-action-manipulation text-center bg-slate-50 border border-slate-200/80 text-slate-700 hover:bg-slate-100`;
                        }
                    });
                } else {
                    if (subEl) {
                        subEl.innerText = 'Belum';
                        subEl.className = 'text-[11px] font-semibold text-slate-400 weighted-subtotal-text block truncate';
                    }
                    input.className = 'score-input w-full text-center h-8 px-1 bg-white rounded-xl border border-slate-300 font-semibold text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 shadow-2xs';
                    row.className = 'criterion-row p-3 rounded-2xl border transition-all duration-150 bg-white border-slate-200/90 hover:border-slate-300';

                    // Clear preset pills
                    const pills = row.querySelectorAll('[class*="preset-pill-"]');
                    pills.forEach(pill => {
                        pill.className = `preset-pill-${id} w-full py-1.5 sm:py-1 rounded-lg text-[11px] sm:text-xs font-black transition touch-action-manipulation text-center bg-slate-50 border border-slate-200/80 text-slate-700 hover:bg-slate-100`;
                    });
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

        // Determine target category for stage-level view (terhadap parent tahap)
        let targetCatId = null;
        if (isStageLocked && lockedCatId) {
            targetCatId = lockedCatId;
        } else if (activeStageTab && activeStageTab !== 'all') {
            targetCatId = activeStageTab;
        } else if (currentActiveCatId && categoriesMeta[currentActiveCatId]) {
            targetCatId = currentActiveCatId;
        } else {
            // Pick category with rated criteria, or the first available category
            const ratedCatIds = Object.keys(catRatedCounts).filter(cid => catRatedCounts[cid] > 0);
            if (ratedCatIds.length > 0) {
                targetCatId = ratedCatIds[ratedCatIds.length - 1];
            } else {
                targetCatId = Object.keys(categoriesMeta)[0] || null;
            }
        }

        // Update Floating Score (Nilai Tertimbang Terhadap Parent Tahap)
        const floatingEl = document.getElementById('floatingTotalScore');
        const floatingLabel = document.getElementById('floatingScoreLabel');
        const floatingSub = document.getElementById('floatingScoreSub');

        if (targetCatId && categoriesMeta[targetCatId]) {
            const catMeta = categoriesMeta[targetCatId];
            const stageScore = catSubtotals[targetCatId] !== undefined ? catSubtotals[targetCatId] : 0;
            const contribution = (stageScore * catMeta.weight) / 100;

            if (floatingLabel) {
                floatingLabel.innerText = 'Nilai Tahap (' + catMeta.name + ')';
            }
            if (floatingEl) {
                floatingEl.innerText = stageScore.toFixed(2);
            }
            if (floatingSub) {
                floatingSub.innerText = 'Bobot Tahap: ' + catMeta.weight + '% | Kontribusi ke Nilai Akhir: ' + contribution.toFixed(2);
            }
        } else {
            if (floatingLabel) {
                floatingLabel.innerText = 'Nilai Tertimbang Tahap';
            }
            if (floatingEl) {
                floatingEl.innerText = grandTotalWeighted.toFixed(2);
            }
            if (floatingSub) {
                floatingSub.innerText = 'Nilai tertimbang terhadap parent (tahap)';
            }
        }

        // Update Modal Score Display
        const modalScoreSubtitle = document.getElementById('modalScoreSubtitle');
        const modalScoreEl = document.getElementById('modalFinalScore');
        const modalScoreDetail = document.getElementById('modalScoreDetail');
        const modalStageBreakdown = document.getElementById('modalStageBreakdown');

        if (targetCatId && categoriesMeta[targetCatId]) {
            const catMeta = categoriesMeta[targetCatId];
            const stageScore = catSubtotals[targetCatId] !== undefined ? catSubtotals[targetCatId] : 0;
            const contribution = (stageScore * catMeta.weight) / 100;

            if (modalScoreSubtitle) {
                modalScoreSubtitle.innerText = 'Nilai Tertimbang Tahap ' + catMeta.name;
            }
            if (modalScoreEl) {
                modalScoreEl.innerText = stageScore.toFixed(2);
            }
            if (modalScoreDetail) {
                modalScoreDetail.innerHTML = 'Nilai tertimbang terhadap parent (tahap).<br><span class="text-[10px] text-slate-400">Bobot Tahap: ' + catMeta.weight + '% &bull; Kontribusi ke Nilai Akhir: ' + contribution.toFixed(2) + '</span>';
            }
            if (modalStageBreakdown) {
                modalStageBreakdown.classList.add('hidden');
                modalStageBreakdown.innerHTML = '';
            }
        } else {
            // All stages mode
            const ratedCatIds = Object.keys(catRatedCounts).filter(cid => catRatedCounts[cid] > 0);
            if (ratedCatIds.length === 1 && categoriesMeta[ratedCatIds[0]]) {
                const singleCatId = ratedCatIds[0];
                const catMeta = categoriesMeta[singleCatId];
                const stageScore = catSubtotals[singleCatId] || 0;
                const contribution = (stageScore * catMeta.weight) / 100;

                if (modalScoreSubtitle) {
                    modalScoreSubtitle.innerText = 'Nilai Tertimbang Tahap ' + catMeta.name;
                }
                if (modalScoreEl) {
                    modalScoreEl.innerText = stageScore.toFixed(2);
                }
                if (modalScoreDetail) {
                    modalScoreDetail.innerHTML = 'Nilai tertimbang terhadap parent (tahap).<br><span class="text-[10px] text-slate-400">Bobot Tahap: ' + catMeta.weight + '% &bull; Kontribusi ke Nilai Akhir: ' + contribution.toFixed(2) + '</span>';
                }
                if (modalStageBreakdown) {
                    modalStageBreakdown.classList.add('hidden');
                }
            } else {
                if (modalScoreSubtitle) {
                    modalScoreSubtitle.innerText = 'Total Nilai Tertimbang Keseluruhan';
                }
                if (modalScoreEl) {
                    modalScoreEl.innerText = grandTotalWeighted.toFixed(2);
                }
                if (modalScoreDetail) {
                    modalScoreDetail.innerText = 'Rincian nilai tertimbang terhadap parent per tahap:';
                }
                if (modalStageBreakdown) {
                    modalStageBreakdown.classList.remove('hidden');
                    let html = '';
                    for (const [cid, meta] of Object.entries(categoriesMeta)) {
                        const sScore = catSubtotals[cid] || 0;
                        const rCount = catRatedCounts[cid] || 0;
                        const contr = (sScore * meta.weight) / 100;
                        html += `
                            <div class="flex items-center justify-between py-1 border-b border-slate-200/60 last:border-0">
                                <div>
                                    <strong class="text-slate-800 font-bold">${meta.name}</strong>
                                    <span class="text-[10px] text-slate-400 block">Bobot ${meta.weight}% &bull; Kontribusi: ${contr.toFixed(2)}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-black ${rCount > 0 ? 'text-emerald-600' : 'text-slate-400'}">${rCount > 0 ? sScore.toFixed(2) : '-'}</span>
                                    <span class="text-[10px] text-slate-400">/ 10</span>
                                </div>
                            </div>
                        `;
                    }
                    modalStageBreakdown.innerHTML = html;
                }
            }
        }

        // Update Progress Bar & Incomplete Badge Button
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const incompleteBadgeBtn = document.getElementById('incompleteBadgeBtn');
        const incompleteCountText = document.getElementById('incompleteCountText');

        const pct = status.total > 0 ? Math.round((status.filled / status.total) * 100) : 0;
        if (progressBar) progressBar.style.width = pct + '%';
        if (progressText) progressText.innerText = status.filled + ' / ' + status.total + ' Kriteria Dinilai (' + pct + '%)';

        if (incompleteBadgeBtn) {
            if (status.incomplete.length > 0) {
                incompleteBadgeBtn.classList.remove('hidden');
                incompleteBadgeBtn.classList.add('inline-flex');
                if (incompleteCountText) incompleteCountText.innerText = status.incomplete.length;
            } else {
                incompleteBadgeBtn.classList.add('hidden');
                incompleteBadgeBtn.classList.remove('inline-flex');
            }
        }
    }

    let scoreWarningTimer = null;
    function showScoreWarning(message) {
        const toast = document.getElementById('scoreWarningToast');
        const msgEl = document.getElementById('scoreWarningMessage');
        if (msgEl) msgEl.innerText = message;
        if (toast) {
            toast.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-4');
            toast.classList.add('opacity-100', 'translate-y-0');

            if (scoreWarningTimer) clearTimeout(scoreWarningTimer);
            scoreWarningTimer = setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'pointer-events-none', '-translate-y-4');
            }, 3000);
        }
    }

    function syncActiveCategoryByCriterion(id) {
        const row = document.querySelector(`.criterion-row[data-criterion-id="${id}"]`);
        if (row) {
            const catId = row.getAttribute('data-category-id');
            if (catId) {
                currentActiveCatId = catId;
            }
        }
    }

    function handleScoreInput(id) {
        syncActiveCategoryByCriterion(id);
        const input = document.getElementById('score_input_' + id);
        if (!input) return;

        const raw = input.value.trim();
        if (raw === '') {
            recalculateScores();
            return;
        }

        let val = parseFloat(raw);
        if (isNaN(val)) {
            input.value = '';
            recalculateScores();
            return;
        }

        if (val > 10) {
            input.value = 10;
            showScoreWarning('Nilai tidak boleh lebih dari 10! (Skala nilai: 0 - 10)');
            input.classList.add('ring-2', 'ring-rose-500', 'border-rose-500');
            setTimeout(() => {
                input.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500');
            }, 1500);
        } else if (val < 0) {
            input.value = 0;
            showScoreWarning('Nilai minimal adalah 0! (Skala nilai: 0 - 10)');
        }

        recalculateScores();
    }

    function syncFromInput(id) {
        handleScoreInput(id);
    }

    function adjustScore(id, delta) {
        syncActiveCategoryByCriterion(id);
        const input = document.getElementById('score_input_' + id);
        if (input) {
            let current = parseFloat(input.value);
            if (isNaN(current)) current = 8; // Default starting point if blank
            let next = Math.round((current + delta) * 10) / 10;
            if (next > 10) {
                input.value = 10;
                showScoreWarning('Nilai sudah mencapai batas maksimal 10!');
            } else if (next < 0) {
                input.value = 0;
                showScoreWarning('Nilai minimal adalah 0!');
            } else {
                input.value = next;
            }
            recalculateScores();
        }
    }

    function setPresetScore(id, val) {
        syncActiveCategoryByCriterion(id);
        const input = document.getElementById('score_input_' + id);
        if (input) {
            input.value = Math.min(10, Math.max(0, val));
            recalculateScores();
        }
    }

    function clearScore(id) {
        syncActiveCategoryByCriterion(id);
        const input = document.getElementById('score_input_' + id);
        if (input) input.value = '';
        recalculateScores();
    }

    // Auto-detect Active Stage when user focuses/clicks on any criterion
    document.addEventListener('focusin', function(e) {
        const row = e.target.closest('.criterion-row');
        if (row) {
            const catId = row.getAttribute('data-category-id');
            if (catId && catId !== currentActiveCatId) {
                currentActiveCatId = catId;
                recalculateScores();
            }
        }
    });

    document.addEventListener('click', function(e) {
        const row = e.target.closest('.criterion-row');
        if (row) {
            const catId = row.getAttribute('data-category-id');
            if (catId && catId !== currentActiveCatId) {
                currentActiveCatId = catId;
                recalculateScores();
            }
        }
    });

    // Validation & Submit Handlers
    function validateBeforeSubmit(e) {
        const judgeInput = document.querySelector('input[name="judge_id"]');
        if (!judgeInput || !judgeInput.value) {
            if (e) e.preventDefault();
            openJudgeModal();
            alert('Silakan pilih identitas juri Anda terlebih dahulu.');
            return false;
        }

        const submitter = e?.submitter;
        const isSubmitAction = submitter && submitter.value === 'submit';
        if (isSubmitAction) {
            const status = getIncompleteCriteria();
            if (status.incomplete.length > 0) {
                if (e) e.preventDefault();
                openIncompleteModal();
                return false;
            }
        }
        return true;
    }

    function confirmSubmit() {
        // 1. Check judge selection
        const judgeInput = document.querySelector('input[name="judge_id"]');
        if (!judgeInput || !judgeInput.value) {
            openJudgeModal();
            alert('Silakan pilih identitas juri Anda terlebih dahulu.');
            return;
        }

        // 2. Check complete criteria: If incomplete, do not allow Kirim Final & show incomplete items modal
        const status = getIncompleteCriteria();
        if (status.incomplete.length > 0) {
            openIncompleteModal();
            return;
        }

        // 3. Recalculate scores to ensure parent stage score and breakdown are completely fresh
        recalculateScores();

        // 4. All complete -> open confirmation modal
        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function submitFinalForm() {
        const judgeInput = document.querySelector('input[name="judge_id"]');
        if (!judgeInput || !judgeInput.value) {
            closeModal();
            openJudgeModal();
            alert('Silakan pilih identitas juri Anda terlebih dahulu sebelum menyimpan penilaian.');
            return;
        }

        const status = getIncompleteCriteria();
        if (status.incomplete.length > 0) {
            closeModal();
            openIncompleteModal();
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

    // Modal Sudah Menilai & Konfirmasi Edit
    function confirmEditAssessment() {
        const modal = document.getElementById('alreadyAssessedModal');
        if (modal) modal.classList.add('hidden');
        const banner = document.getElementById('editModeBanner');
        if (banner) banner.classList.remove('hidden');

        // Update URL parameter tanpa reload agar mode edit tetap aktif jika refresh
        const url = new URL(window.location.href);
        url.searchParams.set('edit_confirmed', '1');
        window.history.replaceState({}, '', url);
    }

    function openJudgeModalFromAssessed() {
        const modal = document.getElementById('alreadyAssessedModal');
        if (modal) modal.classList.add('hidden');
        openJudgeModal();
    }

    // Judge Switcher Modal & Search Handlers
    function filterJudgesList() {
        const input = document.getElementById('searchJudgeInput');
        const clearBtn = document.getElementById('clearJudgeSearchBtn');
        const noResults = document.getElementById('noJudgesFound');
        const keywordDisplay = document.getElementById('searchKeywordDisplay');
        
        if (!input) return;
        const query = input.value.trim().toLowerCase();
        
        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        const items = document.querySelectorAll('.judge-item-btn');
        let visibleCount = 0;

        items.forEach(item => {
            const searchText = item.getAttribute('data-search') || '';
            if (query === '' || searchText.includes(query)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (noResults) {
            if (visibleCount === 0 && query !== '') {
                noResults.classList.remove('hidden');
                if (keywordDisplay) keywordDisplay.innerText = input.value.trim();
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    function clearJudgeSearch() {
        const input = document.getElementById('searchJudgeInput');
        if (input) {
            input.value = '';
            input.focus();
            filterJudgesList();
        }
    }

    function openJudgeModal() {
        const modal = document.getElementById('judgeModal');
        if (modal) modal.classList.remove('hidden');
        const input = document.getElementById('searchJudgeInput');
        if (input) {
            input.value = '';
            filterJudgesList();
            setTimeout(() => input.focus(), 150);
        }
    }

    function closeJudgeModal() {
        document.getElementById('judgeModal').classList.add('hidden');
    }

    function selectJudge(judgeId) {
        // Reload current page with judge_id parameter and preserve stage tab & judges filter
        let url = new URL(window.location.href);
        url.searchParams.set('judge_id', judgeId);
        url.searchParams.delete('choose_judge');
        url.searchParams.delete('edit_confirmed');
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
        if (checkedJudges.length > 0) {
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

        const judgeModal = document.getElementById('judgeModal');
        if (judgeModal && !judgeModal.classList.contains('hidden')) {
            const searchInput = document.getElementById('searchJudgeInput');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 250);
            }
        }
    });
</script>
@endpush
