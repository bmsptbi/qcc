@extends('layouts.app')

@section('title', 'Pengaturan Sistem QCC')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-20">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xl">
                ⚙️
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Pengaturan Sistem
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                    Konfigurasi parameter kalibrasi penilaian, batas deviasi juri, dan preferensi aplikasi.
                </p>
            </div>
        </div>

        <div class="px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-right self-start sm:self-auto">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Batas Saat Ini</span>
            <span class="text-lg font-black text-indigo-600">
                {{ number_format($maxDeviation, 1) }}%
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-sm font-bold shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            <strong class="font-bold block mb-1">Terdapat kesalahan:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Pengaturan Deviasi -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                Parameter Deviasi Penilaian Dewan Juri
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Tentukan batas toleransi deviasi maksimum antara dewan juri pada setiap tahapan penilaian (Makalah, Penjurian Lapangan, Konvensi).
            </p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <div>
                    <label for="max_deviation_percentage" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Batas Toleransi Maksimum Deviasi (%) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative max-w-xs">
                        <input type="number" 
                            step="0.1" 
                            min="1" 
                            max="100" 
                            id="max_deviation_percentage" 
                            name="max_deviation_percentage" 
                            value="{{ old('max_deviation_percentage', $maxDeviation) }}"
                            class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900 font-black text-lg font-mono shadow-2xs"
                            required>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 font-bold text-sm">
                            %
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-1.5 block">
                        Standar default konvensi QCC adalah <strong>15.0%</strong>. Masukkan nilai antara 1.0% hingga 100.0%.
                    </span>
                </div>

                <!-- Explanation Box -->
                <div class="p-4 rounded-xl bg-blue-50/80 border border-blue-200/80 text-blue-950 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-blue-900">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Bagaimana Logika Auto-Adjustment Bekerja?</span>
                    </div>
                    <p class="leading-relaxed text-blue-900/90">
                        1. Jika deviasi skor tertimbang antar juri pada suatu tahap <strong>melebihi {{ number_format($maxDeviation, 1) }}%</strong>, sistem akan membuat baris <strong>Adjustment 1</strong> di atas Nilai Tertimbang.<br>
                        2. Sistem membandingkan <strong>Deviasi Max</strong> dan <strong>Deviasi Min</strong>:<br>
                        &bull; Jika <em>Deviasi Max &gt; Deviasi Min</em>, nilai juri tertinggi digantikan dengan nilai <em>Average</em> tahap sebelumnya.<br>
                        &bull; Jika <em>Deviasi Min &gt; Deviasi Max</em>, nilai juri terendah digantikan dengan nilai <em>Average</em> tahap sebelumnya.<br>
                        3. Dilakukan kalkulasi ulang. Jika deviasi baru masih <strong>&gt; {{ number_format($maxDeviation, 1) }}%</strong>, dibuat baris <strong>Adjustment 2</strong> dan seterusnya sampai deviasi memenuhi batas toleransi.<br>
                        4. Nilai pada baris adjustment terakhir adalah nilai yang <strong>digunakan untuk total nilai gugus</strong>.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('qcc.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
