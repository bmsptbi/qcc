@extends('layouts.app')

@section('title', 'Pendaftaran Circle QCC Baru')

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

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xl">
                +
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Pendaftaran Circle QCC Baru
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                    Masukkan data utama circle mutu, departemen, dan susunan anggota tim.
                </p>
            </div>
        </div>
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

    <form method="POST" action="{{ route('admin.circles.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Informasi Utama Circle -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                1. Data Utama Circle
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Circle <span class="text-rose-500">*</span></label>
                <input type="text" name="circle_name" value="{{ old('circle_name') }}" required
                    placeholder="Contoh: QCC THUNDER, KAIZEN MASTER, DELTA TEAM"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <span class="text-[11px] text-slate-400 mt-1 block">
                    ✨ Kode Circle (seperti QCC-{{ date('Y') }}-xxx) otomatis di-generate unik oleh sistem saat disimpan agar tidak terjadi bentrok nomor.
                </span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Tema Proyek Perbaikan <span class="text-rose-500">*</span></label>
                <input type="text" name="theme_title" value="{{ old('theme_title') }}" required
                    placeholder="Contoh: Penurunan Waktu Setup Cetakan dari 50 Menit ke 20 Menit..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <!-- Status awal otomatis 'submitted' -->
            <input type="hidden" name="status" value="submitted">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahun Periode <span class="text-rose-500">*</span></label>
                    <input type="number" name="year" value="{{ old('year', (int)date('Y')) }}" min="2000" max="2099" required
                        placeholder="Contoh: {{ date('Y') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Departemen <span class="text-rose-500">*</span></label>
                    <select name="department_id" required class="searchable-select w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fasilitator Pendamping</label>
                    <select name="facilitator_id" class="searchable-select w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <option value="">-- Pilih Fasilitator (Opsional) --</option>
                        @foreach($facilitators as $fac)
                            <option value="{{ $fac->id }}" {{ old('facilitator_id') == $fac->id ? 'selected' : '' }}>
                                {{ $fac->name }} ({{ $fac->email }})
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
                    <input type="url" name="sharepoint_url" value="{{ old('sharepoint_url') }}"
                        placeholder="Contoh: https://bekaert.sharepoint.com/:f:/r/teams/..."
                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono text-xs text-blue-700">
                    <svg class="w-4 h-4 text-blue-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    Masukkan URL tautan folder SharePoint yang berisi file risalah, presentasi, atau lampiran circle. Tautan ini akan tampil langsung pada halaman rincian circle untuk dewan juri & fasilitator.
                </span>
            </div>
        </div>

        <!-- 2. Susunan Anggota Circle Dinamis -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    2. Susunan Anggota Tim Circle
                </h2>
                <button type="button" onclick="addMemberRow()" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1">
                    + Tambah Anggota
                </button>
            </div>

            <div id="membersContainer" class="space-y-3">
                <!-- Baris Default 1: Leader -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center member-row">
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Nama Lengkap</label>
                        <input type="text" name="members[0][name]" placeholder="Nama Anggota" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">NIK</label>
                        <input type="text" name="members[0][nik]" placeholder="Contoh: EMP-101" required class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Peran</label>
                        <select name="members[0][role]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                            <option value="leader">Ketua (Leader)</option>
                            <option value="secretary">Sekretaris</option>
                            <option value="member">Anggota</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email (Opsional)</label>
                        <input type="email" name="members[0][email]" placeholder="email@bekaert.com" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                </div>

                <!-- Baris Default 2: Secretary -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center member-row">
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Nama Lengkap</label>
                        <input type="text" name="members[1][name]" placeholder="Nama Anggota" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">NIK</label>
                        <input type="text" name="members[1][nik]" placeholder="Contoh: EMP-102" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Peran</label>
                        <select name="members[1][role]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                            <option value="secretary">Sekretaris</option>
                            <option value="leader">Ketua (Leader)</option>
                            <option value="member">Anggota</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3 flex items-center gap-1.5">
                        <div class="flex-1">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email (Opsional)</label>
                            <input type="email" name="members[1][email]" placeholder="email@bekaert.com" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                        </div>
                        <button type="button" onclick="this.closest('.member-row').remove()" class="mt-4 p-1.5 text-slate-400 hover:text-rose-600 transition">
                            &times;
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('qcc.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-sm shadow-lg shadow-blue-600/30 transition">
                Simpan & Daftarkan Circle
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    let memberIndex = 2;

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
                    <option value="member">Anggota</option>
                    <option value="secretary">Sekretaris</option>
                    <option value="leader">Ketua (Leader)</option>
                </select>
            </div>
            <div class="sm:col-span-3 flex items-center gap-1.5">
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Email (Opsional)</label>
                    <input type="email" name="members[${memberIndex}][email]" placeholder="email@bekaert.com" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                </div>
                <button type="button" onclick="this.closest('.member-row').remove()" class="mt-4 p-1.5 text-slate-400 hover:text-rose-600 transition text-lg font-bold">
                    &times;
                </button>
            </div>
        `;
        container.appendChild(row);
        memberIndex++;
    }
</script>
@endpush
