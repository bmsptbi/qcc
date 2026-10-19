@extends('layouts.app')

@section('title', 'Manajemen Gugus QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-20">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('qcc.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Banner -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20">
                🏢
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                        Admin Master Data
                    </span>
                    <span class="text-xs text-slate-400">Total: {{ $stats['total'] }} Gugus</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                    Kelola Gugus Mutu QCC
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    Pusat administrasi gugus inovasi: tambah, edit informasi, jadwalkan presentasi, dan kelola data tim.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.circles.create') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm transition shadow-md shadow-blue-600/25 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Daftarkan Gugus Baru</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Gugus</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Semua periode aktif</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-blue-200/90 shadow-xs bg-blue-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-blue-600 uppercase tracking-wider block">Terjadwal</span>
            <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">{{ $stats['scheduled'] }}</div>
            <span class="text-[10px] text-blue-600/80 mt-0.5 block">Siap dinilai</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200/90 shadow-xs bg-emerald-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block">Selesai Dinilai</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">{{ $stats['evaluated'] }}</div>
            <span class="text-[10px] text-emerald-600/80 mt-0.5 block">Skor tersimpan</span>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-200/90 shadow-xs bg-amber-50/20">
            <span class="text-[10px] sm:text-xs font-bold text-amber-600 uppercase tracking-wider block">Draft Gugus</span>
            <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ $stats['draft'] }}</div>
            <span class="text-[10px] text-amber-600/80 mt-0.5 block">Belum terjadwal</span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6 space-y-4">
        <form method="GET" action="{{ route('admin.circles.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Gugus / Kode / Tema</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: QCC-2026 atau Spider..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Filter Tahun Periode -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun Periode</label>
                <select name="year" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ request('year') == (string)$yr ? 'selected' : '' }}>
                            Tahun {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Departemen -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Departemen</label>
                <select name="department_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="evaluated" {{ request('status') === 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                        <option value="awarded" {{ request('status') === 'awarded' ? 'selected' : '' }}>Awarded</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition flex-shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table of Circles -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-3.5 px-4">Gugus & Kode</th>
                        <th class="py-3.5 px-4">Tema Perbaikan Mutu</th>
                        <th class="py-3.5 px-4">Kategori & Departemen</th>
                        <th class="py-3.5 px-4">Tim & Fasilitator</th>
                        <th class="py-3.5 px-4">Jadwal & Ruang</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($circles as $circle)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 inline-block mb-1">
                                    {{ $circle->circle_code }}
                                </span>
                                <a href="{{ route('qcc.circles.show', $circle) }}" class="font-bold text-slate-900 hover:text-blue-600 block text-sm">
                                    {{ $circle->circle_name }}
                                </a>
                                <span class="text-[10px] font-semibold text-slate-500">Tahun {{ $circle->year ?? $circle->period?->year ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-medium text-slate-700 line-clamp-2" title="{{ $circle->theme_title }}">
                                    "{{ $circle->theme_title }}"
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $circle->category->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $circle->department->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-xs font-bold text-slate-700">
                                    {{ $circle->members_count }} Anggota
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    Fasilitator: {{ $circle->facilitator->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($circle->presentation_schedule)
                                    <div class="font-semibold text-slate-800">
                                        {{ $circle->presentation_schedule->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        📍 {{ $circle->presentation_room ?? 'Ruang Presentasi' }}
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $badgeStyles = [
                                        'draft' => 'bg-slate-100 text-slate-700',
                                        'submitted' => 'bg-amber-100 text-amber-800',
                                        'verified' => 'bg-sky-100 text-sky-800',
                                        'scheduled' => 'bg-blue-100 text-blue-800',
                                        'evaluated' => 'bg-emerald-100 text-emerald-800',
                                        'awarded' => 'bg-purple-100 text-purple-800',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeStyles[$circle->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $circle->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('qcc.circles.show', $circle) }}" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.circles.edit', $circle) }}" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Edit Gugus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.circles.destroy', $circle) }}" class="inline" 
                                        onsubmit="return confirm('PERINGATAN: Hapus gugus {{ $circle->circle_name }} ({{ $circle->circle_code }})? Seluruh nilai dan riwayat asesmen juri akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Gugus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada gugus yang sesuai dengan pencarian atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($circles->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $circles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
