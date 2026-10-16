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

    <!-- Circle Header Card (Sticky Summary on Mobile) -->
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

            <!-- Juri Info & Action -->
            <div class="flex items-center gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                <div class="text-left md:text-right">
                    <span class="text-[11px] font-semibold text-slate-400 block uppercase">Penilai / Juri</span>
                    <span class="text-xs sm:text-sm font-bold text-slate-900">{{ $currentJudge?->name ?? 'Juri' }}</span>
                </div>
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

        <!-- Progress Indicator -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs mb-6">
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                <span>Kelengkapan Penilaian:</span>
                <span id="progressText">0 / 10 Kriteria Terisi</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-blue-600 to-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Loop PDCA Categories -->
        <div class="space-y-6">
            @foreach($categoriesWithCriteria as $categoryIndex => $cat)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
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
                                <span class="text-xs sm:text-sm font-black text-amber-400" id="cat_score_{{ $cat->id }}">0.00</span>
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
                                $currentVal = old('scores.' . $criterion->id, $existingScore?->score ?? 80);
                                $currentNote = old('notes.' . $criterion->id, $existingScore?->notes ?? '');
                            @endphp

                            <div class="pt-6 first:pt-0 criterion-row" data-criterion-id="{{ $criterion->id }}" data-category-id="{{ $cat->id }}" data-weight="{{ $criterion->effective_weight }}" data-cat-weight="{{ $cat->weight_percentage }}">
                                
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
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded {{ $criterion->weight !== null ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : 'bg-slate-100 text-slate-600' }} block mb-0.5">
                                            Bobot: {{ number_format($criterion->effective_weight, 1) }}% {{ $criterion->weight === null ? '(Auto)' : '' }}
                                        </span>
                                        <span class="text-[11px] font-bold text-blue-600 weighted-subtotal-text" id="subtotal_{{ $criterion->id }}">
                                            Sub: 0.00
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

                                <!-- Touch-Friendly Interactive Score Input Controls -->
                                <div class="bg-slate-50/80 rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 space-y-3">
                                    
                                    <!-- Stepper & Direct Value Display -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-xs font-semibold text-slate-500">Skor (0 - 100):</div>

                                        <div class="flex items-center gap-2">
                                            <!-- Step Minus Button (Large thumb target) -->
                                            <button type="button" onclick="adjustScore({{ $criterion->id }}, -5)" 
                                                class="w-10 h-10 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 active:scale-95 flex items-center justify-center font-black text-lg transition touch-action-manipulation shadow-xs">
                                                -
                                            </button>

                                            <!-- Direct Number Input -->
                                            <div class="relative w-20">
                                                <input type="number" 
                                                    id="score_input_{{ $criterion->id }}" 
                                                    name="scores[{{ $criterion->id }}]" 
                                                    value="{{ $currentVal }}" 
                                                    min="0" max="100" step="1"
                                                    oninput="syncFromInput({{ $criterion->id }})"
                                                    class="score-input w-full text-center py-1.5 px-1 bg-white rounded-xl border border-blue-500 font-black text-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-xs">
                                            </div>

                                            <!-- Step Plus Button (Large thumb target) -->
                                            <button type="button" onclick="adjustScore({{ $criterion->id }}, 5)" 
                                                class="w-10 h-10 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 active:scale-95 flex items-center justify-center font-black text-lg transition touch-action-manipulation shadow-xs">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Range Slider (Smooth Scrubbing) -->
                                    <div class="pt-1">
                                        <input type="range" 
                                            id="score_slider_{{ $criterion->id }}" 
                                            value="{{ $currentVal }}" 
                                            min="0" max="100" step="1"
                                            oninput="syncFromSlider({{ $criterion->id }})"
                                            class="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer touch-action-manipulation">
                                        <div class="flex justify-between text-[10px] text-slate-400 font-bold px-0.5 mt-1">
                                            <span>0 (Kurang)</span>
                                            <span>60 (Cukup)</span>
                                            <span>80 (Baik)</span>
                                            <span>100 (Istimewa)</span>
                                        </div>
                                    </div>

                                    <!-- Fast Preset Pills for Easy One-Tap Scoring on Mobile -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1">
                                        <span class="text-[10px] font-semibold text-slate-400 mr-1 hidden sm:inline">Pilihan Cepat:</span>
                                        @foreach([65, 75, 80, 85, 90, 95] as $presetVal)
                                            <button type="button" onclick="setPresetScore({{ $criterion->id }}, {{ $presetVal }})"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition touch-action-manipulation {{ $currentVal == $presetVal ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                                {{ $presetVal }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <!-- Optional Note per Criterion -->
                                    <div class="pt-1">
                                        <input type="text" 
                                            name="notes[{{ $criterion->id }}]" 
                                            value="{{ $currentNote }}" 
                                            placeholder="Catatan khusus kriteria ini (opsional)..."
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

        <!-- Sticky Floating Bottom Bar (Desktop & Mobile) -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-2xl px-4 py-3 sm:py-4">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                
                <!-- Live Total Score Display -->
                <div class="flex items-center justify-between sm:justify-start gap-3 w-full sm:w-auto">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Nilai Tertimbang</span>
                        <div class="flex items-baseline gap-1.5">
                            <span id="floatingTotalScore" class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">0.00</span>
                            <span class="text-xs font-semibold text-slate-400">/ 100</span>
                        </div>
                    </div>

                    <!-- Medal Preview Badge -->
                    <div id="medalBadge" class="px-3 py-1 rounded-full text-xs font-black shadow-xs bg-slate-100 text-slate-600">
                        Calculating...
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
                Anda akan mengirimkan nilai akhir untuk <strong class="text-slate-800">{{ $circle->circle_name }}</strong>.
                Total nilai tertimbang adalah <strong id="modalFinalScore" class="text-emerald-700 font-black">0.00</strong>.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Periksa Lagi
            </button>
            <button type="button" onclick="submitFinalForm()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/30">
                Ya, Kirim Sekarang
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Real-time Score Calculation Script
    function recalculateScores() {
        const rows = document.querySelectorAll('.criterion-row');
        let filledCount = 0;
        const totalCount = rows.length;

        // Group subtotals by category
        const catSubtotals = {};
        const catWeights = {};

        rows.forEach(row => {
            const id = row.getAttribute('data-criterion-id');
            const catId = row.getAttribute('data-category-id');
            const weight = parseFloat(row.getAttribute('data-weight')) || 1.0;
            const catWeight = parseFloat(row.getAttribute('data-cat-weight')) || 100.0;
            const input = document.getElementById('score_input_' + id);
            
            if (catSubtotals[catId] === undefined) {
                catSubtotals[catId] = 0;
                catWeights[catId] = catWeight;
            }

            if (input) {
                const val = parseFloat(input.value) || 0;
                if (val > 0) filledCount++;
                
                // Subtotal poin butir dalam kategori (skor * bobot ke parent / 100)
                const subInCat = (val * weight) / 100;
                catSubtotals[catId] += subInCat;

                // Update subtotal text
                const subEl = document.getElementById('subtotal_' + id);
                if (subEl) {
                    subEl.innerText = 'Sub: ' + subInCat.toFixed(2);
                }
            }
        });

        // Calculate Grand Total Weighted Score: sum of (catSubtotal * catWeight / 100)
        let grandTotalWeighted = 0;
        for (const [catId, scoreInCat] of Object.entries(catSubtotals)) {
            const catEl = document.getElementById('cat_score_' + catId);
            if (catEl) {
                catEl.innerText = scoreInCat.toFixed(2);
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
        if (progressText) progressText.innerText = filledCount + ' / ' + totalCount + ' Kriteria Terisi (' + pct + '%)';

        // Update Medal Preview
        const medalBadge = document.getElementById('medalBadge');
        if (medalBadge) {
            if (grandTotalWeighted >= 90) {
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
        let val = parseInt(input.value);
        if (isNaN(val)) val = 0;
        if (val < 0) val = 0;
        if (val > 100) val = 100;
        if (slider) slider.value = val;
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
            let current = parseInt(input.value) || 0;
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

    function confirmSubmit() {
        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function submitFinalForm() {
        const form = document.getElementById('assessmentForm');
        // create hidden input action=submit
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'submit';
        form.appendChild(actionInput);
        form.submit();
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        recalculateScores();
    });
</script>
@endpush
