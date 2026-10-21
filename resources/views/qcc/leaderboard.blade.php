@extends('layouts.app')

@section('title', 'Leaderboard & Hasil Konvensi QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

    <!-- Header Banner -->
    <div class="text-center max-w-3xl mx-auto mb-8">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold uppercase tracking-wider mb-2">
            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Klasemen & Perolehan Nilai
        </div>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Leaderboard Konvensi QCC {{ $activeYear === 'all' ? 'Semua Periode' : $activeYear }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 font-medium">
            Perhitungan skor agregat rata-rata dari seluruh dewan juri penilai konvensi mutu.
        </p>



        <!-- Year Period Quick Switcher Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 mt-4 max-w-xl mx-auto">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Periode:</span>
            @foreach($availableYears as $yr)
                <a href="{{ route('qcc.leaderboard', ['year' => $yr]) }}"
                    class="px-3 py-1 rounded-full text-xs font-bold transition {{ (string)$activeYear === (string)$yr ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Tahun {{ $yr }}
                </a>
            @endforeach
            <a href="{{ route('qcc.leaderboard', ['year' => 'all']) }}"
                class="px-3 py-1 rounded-full text-xs font-bold transition {{ (string)$activeYear === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua Tahun
            </a>
        </div>
    </div>

    <!-- Live Standings Table / Card List -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-12">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Peringkat & Kategori Penghargaan
            </h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ $circles->count() }} Circle Tercatat
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($circles as $index => $circle)
                @php
                    $rank = $index + 1;
                    $score = $circle->final_score;
                @endphp
                <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition">
                    
                    <div class="flex items-center space-x-4">
                        <!-- Rank Medal / Number Badge -->
                        <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center font-black text-base sm:text-lg shadow-xs {{ $rank === 1 ? 'bg-gradient-to-tr from-amber-400 to-amber-500 text-white' : ($rank === 2 ? 'bg-gradient-to-tr from-slate-300 to-slate-400 text-slate-800' : ($rank === 3 ? 'bg-gradient-to-tr from-amber-700 to-amber-800 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200')) }}">
                            #{{ $rank }}
                        </div>

                        <!-- Circle Detail -->
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                    {{ $circle->circle_code }}
                                </span>
                                <span class="text-xs font-medium text-slate-500">
                                    {{ $circle->department->name ?? '-' }}
                                </span>
                                @if($circle->status === 'evaluated')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Evaluated
                                    </span>
                                @elseif($circle->status === 'evaluating')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                                        Evaluating
                                    </span>
                                @elseif($circle->status === 'verified')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-200">
                                        Verified
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                <a href="{{ route('qcc.circles.show', $circle) }}" class="hover:text-blue-600 transition">
                                    {{ $circle->circle_name }}
                                </a>
                            </h3>

                            <p class="text-xs text-slate-600 line-clamp-1 max-w-xl mt-0.5">
                                "{{ $circle->theme_title }}"
                            </p>
                        </div>
                    </div>

                    <!-- Score & Award Badge -->
                    <div class="flex items-center justify-between sm:justify-end gap-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        @if($score)
                            <!-- Award Level Pill -->
                            <div>
                                @if($score >= 9.0 || $score >= 90)
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-xs block text-center">
                                        ★ DIAMOND
                                    </span>
                                @elseif($score >= 8.5 || $score >= 85)
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-slate-400 to-slate-600 text-white shadow-xs block text-center">
                                        ★ PLATINUM
                                    </span>
                                @elseif($score >= 8.0 || $score >= 80)
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-amber-600 text-white shadow-xs block text-center">
                                        ★ GOLD
                                    </span>
                                @elseif($score >= 7.5 || $score >= 75)
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-slate-200 text-slate-800 block text-center">
                                        ★ SILVER
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400 text-center block mt-0.5">{{ $circle->assessments_count }} Juri Menilai</span>
                            </div>

                            <!-- Numeric Score Display -->
                            <div class="text-right min-w-[70px]">
                                <span class="text-2xl font-black text-slate-900 leading-none block">
                                    {{ number_format($score, 2) }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Poin (/ 10)</span>
                            </div>
                        @else
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500">
                                Menunggu Penilaian
                            </span>
                        @endif

                        <!-- Detail Action Button -->
                        <a href="{{ route('qcc.circles.show', $circle) }}" class="p-2 text-slate-400 hover:text-blue-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>
            @empty
                <div class="text-center py-12 text-slate-400 text-sm">
                    Belum ada data circle (hanya circle berstatus Verified, Evaluating, atau Evaluated yang tampil di leaderboard).
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
