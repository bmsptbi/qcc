@extends('layouts.app')

@section('title', 'Manajemen Juri & Penugasan')

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

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xl">
                ⚖️
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Manajemen Dewan Juri & Asesor
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                    Daftarkan juri baru dan atur penugasan penilaian per kategori konvensi mutu.
                </p>
            </div>
        </div>

        <a href="{{ route('admin.circles.create') }}" class="px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs sm:text-sm transition flex items-center gap-1.5 justify-center">
            <span>+ Daftarkan Circle Baru</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <strong class="font-bold block mb-1">Terdapat kesalahan:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Juri Baru (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4 h-fit">
            <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                Tambah Juri Baru
            </h2>

            <form method="POST" action="{{ route('admin.judges.store') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ir. Bambang Wijaya"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="bambang@bekaert.com"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" placeholder="EMP-015"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0812..."
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Departemen / Divisi</label>
                    <select name="department_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 bg-white">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Default</label>
                    <input type="password" name="password" placeholder="Default: password"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <span class="text-[10px] text-slate-400">Kosongkan jika ingin menggunakan password default: 'password'</span>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/25 transition">
                    + Daftarkan Juri
                </button>
            </form>
        </div>

        <!-- Daftar Juri & Penugasan Kategori (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Form Atur Penugasan Cepat -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Tugaskan Juri ke Kategori Konvensi
                </h2>

                <form method="POST" action="{{ route('admin.judges.assign') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    @csrf
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Juri <span class="text-rose-500">*</span></label>
                        <select name="judge_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-semibold">
                            @foreach($judges as $j)
                                <option value="{{ $j->id }}">{{ $j->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Circle</label>
                        <select name="circle_category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Periode</label>
                        <select name="assessment_period_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                            @foreach($periods as $per)
                                <option value="{{ $per->id }}">{{ $per->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-8 flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_lead_judge" name="is_lead_judge" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_lead_judge" class="text-xs font-semibold text-slate-700">Tugaskan sebagai Koordinator Juri (Lead Judge)</label>
                    </div>

                    <div class="sm:col-span-4 text-right">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm transition shadow-sm">
                            Tugaskan
                        </button>
                    </div>
                </form>
            </div>

            <!-- List Juri Terdaftar -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center justify-between border-b border-slate-100 pb-3">
                    <span>Daftar Dewan Juri Aktif ({{ $judges->count() }})</span>
                </h2>

                <div class="divide-y divide-slate-100">
                    @forelse($judges as $judge)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                                    {{ strtoupper(substr($judge->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                        {{ $judge->name }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-0.5">
                                        <span>{{ $judge->email }}</span>
                                        @if($judge->department)
                                            <span>•</span>
                                            <span>{{ $judge->department->name }}</span>
                                        @endif
                                        @if($judge->phone)
                                            <span>•</span>
                                            <span>{{ $judge->phone }}</span>
                                        @endif
                                    </div>

                                    <!-- Assignments Badges -->
                                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                        @forelse($judge->judgeAssignments as $assign)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $assign->is_lead_judge ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $assign->category->name ?? 'Semua Kategori' }}
                                                @if($assign->is_lead_judge)
                                                    <span class="text-amber-600 font-extrabold">(Lead)</span>
                                                @endif
                                                <form method="POST" action="{{ route('admin.judges.assignment.destroy', $assign) }}" class="inline" onsubmit="return confirm('Hapus penugasan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-rose-600 leading-none">&times;</button>
                                                </form>
                                            </span>
                                        @empty
                                            <span class="text-[10px] text-slate-400 italic">Belum ada penugasan kategori</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada juri yang didaftarkan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
