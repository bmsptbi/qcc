@extends('layouts.app')

@section('title', 'Edit Gugus - ' . $circle->circle_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-20">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.circles.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Manajemen Gugus
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xl">
                ✏️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                        {{ $circle->circle_code }}
                    </span>
                    <span class="text-xs text-slate-400">ID: #{{ $circle->id }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                    Edit Data Gugus: {{ $circle->circle_name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    Perbarui informasi gugus mutu, jadwal presentasi, dan susunan anggota tim.
                </p>
            </div>
        </div>

        <a href="{{ route('qcc.circles.show', $circle) }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>Halaman Publik Gugus</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <strong class="font-bold block mb-1">Terdapat kesalahan pengisian:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.circles.update', $circle) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Informasi Utama Gugus -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                1. Data Utama Gugus
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Gugus <span class="text-rose-500">*</span></label>
                    <input type="text" name="circle_code" value="{{ old('circle_code', $circle->circle_code) }}" required
                        placeholder="Contoh: QCC-2026-005"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Gugus / Circle <span class="text-rose-500">*</span></label>
                    <input type="text" name="circle_name" value="{{ old('circle_name', $circle->circle_name) }}" required
                        placeholder="Contoh: QCC THUNDER, KAIZEN MASTER"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Tema Proyek Perbaikan <span class="text-rose-500">*</span></label>
                <input type="text" name="theme_title" value="{{ old('theme_title', $circle->theme_title) }}" required
                    placeholder="Contoh: Penurunan Waktu Setup Cetakan dari 50 Menit ke 20 Menit..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahun Periode <span class="text-rose-500">*</span></label>
                    <select name="year" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (old('year', $circle->year) == $yr) ? 'selected' : '' }}>
                                Tahun {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Departemen <span class="text-rose-500">*</span></label>
                    <select name="department_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $circle->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Periode Konvensi</label>
                    <select name="assessment_period_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <option value="">-- Otomatis Sesuai Tahun --</option>
                        @foreach($periods as $per)
                            <option value="{{ $per->id }}" {{ old('assessment_period_id', $circle->assessment_period_id) == $per->id ? 'selected' : '' }}>
                                {{ $per->name }} ({{ $per->year }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Link Folder SharePoint (Risalah, Presentasi & Dokumen Pendukung)
                </label>
                <div class="relative">
                    <input type="url" name="sharepoint_url" value="{{ old('sharepoint_url', $circle->sharepoint_url) }}"
                        placeholder="Contoh: https://bekaert.sharepoint.com/:f:/r/teams/..."
                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono text-xs text-blue-700">
                    <svg class="w-4 h-4 text-blue-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    Masukkan URL tautan folder SharePoint yang berisi file risalah, presentasi, atau lampiran dokumen pendukung gugus.
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fasilitator Pendamping</label>
                    <select name="facilitator_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <option value="">-- Pilih Fasilitator (Opsional) --</option>
                        @foreach($facilitators as $fac)
                            <option value="{{ $fac->id }}" {{ old('facilitator_id', $circle->facilitator_id) == $fac->id ? 'selected' : '' }}>
                                {{ $fac->name }} ({{ $fac->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Gugus <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white font-semibold">
                        <option value="scheduled" {{ old('status', $circle->status) == 'scheduled' ? 'selected' : '' }}>Terjadwal Siap Presentasi (Scheduled)</option>
                        <option value="evaluated" {{ old('status', $circle->status) == 'evaluated' ? 'selected' : '' }}>Selesai Dinilai (Evaluated)</option>
                        <option value="awarded" {{ old('status', $circle->status) == 'awarded' ? 'selected' : '' }}>Penganugerahan Juara (Awarded)</option>
                        <option value="verified" {{ old('status', $circle->status) == 'verified' ? 'selected' : '' }}>Terverifikasi (Verified)</option>
                        <option value="submitted" {{ old('status', $circle->status) == 'submitted' ? 'selected' : '' }}>Terkirim (Submitted)</option>
                        <option value="draft" {{ old('status', $circle->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Deskripsi Masalah & Sasaran -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                2. Uraian Masalah & Sasaran Perbaikan
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Latar Belakang Permasalahan</label>
                <textarea name="problem_statement" rows="3" placeholder="Jelaskan kondisi sebelum perbaikan, data historis defect/loss, atau dampak biaya..."
                    class="w-full p-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('problem_statement', $circle->problem_statement) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sasaran & Target (SMART)</label>
                <textarea name="target_statement" rows="3" placeholder="Tuliskan target mutu/efisiensi terukur (Specific, Measurable, Time-bound)..."
                    class="w-full p-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('target_statement', $circle->target_statement) }}</textarea>
            </div>
        </div>

        <!-- 3. Jadwal Presentasi & Ruangan -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                3. Jadwal & Ruangan Presentasi Konvensi
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Waktu Presentasi</label>
                    <input type="datetime-local" name="presentation_schedule" 
                        value="{{ old('presentation_schedule', $circle->presentation_schedule ? $circle->presentation_schedule->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lokasi / Ruangan Konvensi</label>
                    <input type="text" name="presentation_room" 
                        value="{{ old('presentation_room', $circle->presentation_room) }}"
                        placeholder="Contoh: Auditorium Lantai 2 / Meeting Room Bima"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- 4. Susunan Anggota Gugus Dinamis -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    4. Susunan Anggota Tim Gugus
                </h2>
                <button type="button" onclick="addMemberRow()" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1">
                    + Tambah Anggota
                </button>
            </div>

            <div id="membersContainer" class="space-y-3">
                @php
                    $members = $circle->members->count() > 0 ? $circle->members : collect([
                        (object)['name' => '', 'nik' => '', 'role' => 'leader', 'email' => ''],
                        (object)['name' => '', 'nik' => '', 'role' => 'secretary', 'email' => '']
                    ]);
                @endphp

                @foreach($members as $index => $m)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center member-row">
                        <div class="sm:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Nama Lengkap</label>
                            <input type="text" name="members[{{ $index }}][name]" value="{{ $m->name }}" placeholder="Nama Anggota" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">NIK</label>
                            <input type="text" name="members[{{ $index }}][nik]" value="{{ $m->nik }}" placeholder="Contoh: EMP-101" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Peran</label>
                            <select name="members[{{ $index }}][role]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                <option value="leader" {{ $m->role === 'leader' ? 'selected' : '' }}>Ketua (Leader)</option>
                                <option value="secretary" {{ $m->role === 'secretary' ? 'selected' : '' }}>Sekretaris</option>
                                <option value="facilitator" {{ $m->role === 'facilitator' ? 'selected' : '' }}>Fasilitator</option>
                                <option value="member" {{ $m->role === 'member' ? 'selected' : '' }}>Anggota</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3 flex items-center gap-1.5">
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email (Opsional)</label>
                                <input type="email" name="members[{{ $index }}][email]" value="{{ $m->email }}" placeholder="email@bekaert.com" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                            </div>
                            @if($index > 0)
                                <button type="button" onclick="this.closest('.member-row').remove()" class="p-1.5 mt-3 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Hapus Anggota">
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tombol Aksi Submit -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.circles.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition">
                Batal
            </a>
            <button type="submit" class="px-7 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Perubahan Gugus</span>
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
    let memberIndex = {{ max($members->count(), 2) }};

    function addMemberRow() {
        const container = document.getElementById('membersContainer');
        const row = document.createElement('div');
        row.className = 'p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center member-row';
        row.innerHTML = `
            <div class="sm:col-span-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Nama Lengkap</label>
                <input type="text" name="members[${memberIndex}][name]" placeholder="Nama Anggota" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">NIK</label>
                <input type="text" name="members[${memberIndex}][nik]" placeholder="Contoh: EMP-103" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Peran</label>
                <select name="members[${memberIndex}][role]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    <option value="member" selected>Anggota</option>
                    <option value="secretary">Sekretaris</option>
                    <option value="leader">Ketua (Leader)</option>
                    <option value="facilitator">Fasilitator</option>
                </select>
            </div>
            <div class="sm:col-span-3 flex items-center gap-1.5">
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email (Opsional)</label>
                    <input type="email" name="members[${memberIndex}][email]" placeholder="email@bekaert.com" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                </div>
                <button type="button" onclick="this.closest('.member-row').remove()" class="p-1.5 mt-3 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Hapus Anggota">
                    &times;
                </button>
            </div>
        `;
        container.appendChild(row);
        memberIndex++;
    }
</script>
@endpush
@endsection
