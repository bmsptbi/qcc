@extends('layouts.app')

@section('title', 'Kriteria & Rubrik Penilaian QCC')

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

    <!-- Header & Summary Cards -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xl">
                📋
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Kriteria & Rubrik Penilaian QCC
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                    Kustomisasi butir penilaian, tahapan PDCA, bobot persentase, dan panduan rubrik juri.
                </p>
            </div>
        </div>

        <!-- Total Weight Status Badge -->
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-right">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Total Bobot Tahap</span>
                <span class="text-lg font-black {{ $totalWeight == 100 ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ number_format($totalWeight, 0) }}% / 100%
                </span>
            </div>

            <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-right">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Total Butir</span>
                <span class="text-lg font-black text-slate-900">
                    {{ $totalCriteria }} Butir
                </span>
            </div>
        </div>
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

        <!-- Form Tambah Butir & Tambah Kategori (1 Col) -->
        <div class="space-y-6">
            
            <!-- Box Tambah Butir Kriteria -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    + Tambah Butir Kriteria Baru
                </h2>

                <form method="POST" action="{{ route('admin.criteria.store') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahap / Kategori <span class="text-rose-500">*</span></label>
                        <select name="criteria_category_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-semibold">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} (Bobot: {{ number_format($cat->weight_percentage, 0) }}%)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode <span class="text-slate-400 font-normal">(Auto)</span></label>
                            <input type="text" name="code" placeholder="Otomatis"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold uppercase focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bobot di Tahap (%) <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="number" name="weight" step="0.1" min="0.1" max="100" placeholder="Kosongkan = Auto Proporsional"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-tight">
                        💡 <em>Kode dibuat otomatis. Bobot adalah porsi terhadap tahap ini (total 100%). Jika dikosongkan, nilai akan dibagi rata secara proporsional.</em>
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Kriteria <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" placeholder="Contoh: Analisis Risiko & Mitigasi Gemba" required
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Panduan Rubrik Penilaian</label>
                        <textarea name="guidelines" rows="3" placeholder="Tuliskan indikator bagaimana juri memberikan nilai..."
                            class="w-full p-3 rounded-2xl border border-slate-200 text-xs focus:outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Urutan Tampil</label>
                            <input type="number" name="display_order" value="1" min="1"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Skor Maks</label>
                            <input type="number" name="max_score" value="100"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50" readonly>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/25 transition">
                        Simpan Butir Kriteria
                    </button>
                </form>
            </div>

            <!-- Box Tambah Kategori Baru -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    + Tambah Tahap / Kategori Baru
                </h2>

                <form method="POST" action="{{ route('admin.criteria.categories.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kategori Tahap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" placeholder="Contoh: 6. Dampak Safety & Ergonomi" required
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahap (Stage) <span class="text-rose-500">*</span></label>
                            <select name="stage" class="w-full px-2 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                                <option value="plan">Plan</option>
                                <option value="do">Do</option>
                                <option value="check">Check</option>
                                <option value="action">Action</option>
                                <option value="presentation">Presentation</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bobot (%) <span class="text-rose-500">*</span></label>
                            <input type="number" name="weight_percentage" step="1" min="1" max="100" placeholder="Contoh: 10" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="description" placeholder="Penjelasan singkat tahap..."
                            class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-emerald-500">
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/25 transition">
                        Tambah Kategori
                    </button>
                </form>
            </div>

        </div>

        <!-- Daftar Tahapan & Butir Kriteria Saat Ini (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            @foreach($categories as $category)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    
                    <!-- Category Header with Edit & Delete Buttons -->
                    <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <h3 class="text-base font-bold tracking-tight">{{ $category->name }}</h3>
                            </div>
                            @if($category->description)
                                <p class="text-[11px] text-slate-300 mt-0.5 line-clamp-1">{{ $category->description }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-2.5">
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 text-emerald-300 font-extrabold text-xs sm:text-sm border border-white/10">
                                Bobot: {{ number_format($category->weight_percentage, 0) }}%
                            </span>

                            <!-- Tombol Edit Tahap -->
                            <button type="button" onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->stage }}', {{ $category->weight_percentage }}, '{{ addslashes($category->description ?? '') }}')" 
                                class="px-2.5 py-1 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs font-semibold transition" title="Ubah Tahap">
                                Edit
                            </button>

                            <!-- Tombol Hapus Tahap -->
                            <form method="POST" action="{{ route('admin.criteria.categories.destroy', $category) }}" 
                                onsubmit="return confirm('PERINGATAN: Menghapus tahap \'{{ $category->name }}\' akan menghapus seluruh butir kriteria di dalamnya! Lanjutkan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/80 hover:bg-rose-500 text-white text-xs font-semibold transition" title="Hapus Tahap">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Criteria List -->
                    <div class="divide-y divide-slate-100 p-4 sm:p-6 space-y-4">
                        @forelse($category->criteria as $crit)
                            <div class="pt-4 first:pt-0 flex items-start justify-between gap-3">
                                <div class="space-y-1 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-black bg-blue-100 text-blue-800">
                                            {{ $crit->code }}
                                        </span>
                                        <h4 class="text-sm font-bold text-slate-900">
                                            {{ $crit->title }}
                                        </h4>
                                        @if($crit->weight !== null)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60" title="Bobot diatur manual">
                                                Bobot: {{ number_format($crit->effective_weight, 1) }}% (Manual)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60" title="Bobot dihitung proporsional dari sisa bobot kategori">
                                                Bobot: {{ number_format($crit->effective_weight, 1) }}% (Auto)
                                            </span>
                                        @endif
                                    </div>

                                    @if($crit->guidelines)
                                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                                            {{ $crit->guidelines }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Buttons: Edit & Delete Criterion -->
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button type="button" onclick="openEditCriterionModal({{ $crit->id }}, {{ $category->id }}, '{{ addslashes($crit->code) }}', '{{ addslashes($crit->title) }}', {{ $crit->weight !== null ? $crit->weight : 'null' }}, '{{ addslashes($crit->guidelines ?? '') }}')" 
                                        class="p-1.5 text-slate-400 hover:text-blue-600 transition" title="Ubah Butir Kriteria">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('admin.criteria.destroy', $crit) }}" onsubmit="return confirm('Hapus butir kriteria {{ $crit->code }} - {{ $crit->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-300 hover:text-rose-600 transition" title="Hapus Butir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs">
                                Belum ada butir kriteria di kategori ini.
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

    </div>

</div>

<!-- Modal Edit Kategori / Tahap -->
<div id="editCategoryModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Edit Tahap / Kategori</h3>
            <button type="button" onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form id="editCategoryForm" method="POST" action="" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kategori Tahap</label>
                <input type="text" id="edit_cat_name" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahap (Stage)</label>
                    <select id="edit_cat_stage" name="stage" class="w-full px-2 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <option value="plan">Plan</option>
                        <option value="do">Do</option>
                        <option value="check">Check</option>
                        <option value="action">Action</option>
                        <option value="presentation">Presentation</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bobot (%)</label>
                    <input type="number" id="edit_cat_weight" name="weight_percentage" step="1" min="1" max="100" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan</label>
                <input type="text" id="edit_cat_desc" name="description" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Butir Kriteria -->
<div id="editCriterionModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Edit Butir Kriteria</h3>
            <button type="button" onclick="closeEditCriterionModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form id="editCriterionForm" method="POST" action="" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tahap / Kategori</label>
                <select id="edit_crit_category_id" name="criteria_category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white font-semibold">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode <span class="text-slate-400 font-normal">(Auto)</span></label>
                    <input type="text" id="edit_crit_code" name="code" placeholder="Otomatis" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold uppercase bg-slate-50/50">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bobot di Tahap (%) <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="number" id="edit_crit_weight" name="weight" step="0.1" min="0.1" max="100" placeholder="Kosongkan = Auto Proporsional" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                </div>
            </div>
            <p class="text-[10px] text-slate-500 leading-tight">
                💡 <em>Bobot adalah porsi dalam tahap ini (total 100%). Jika dikosongkan, bobot butir ini akan dibagi rata secara proporsional.</em>
            </p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Kriteria</label>
                <input type="text" id="edit_crit_title" name="title" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Panduan Rubrik Penilaian</label>
                <textarea id="edit_crit_guidelines" name="guidelines" rows="3" class="w-full p-3 rounded-2xl border border-slate-200 text-xs"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeEditCriterionModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditCategoryModal(id, name, stage, weight, desc) {
        document.getElementById('edit_cat_name').value = name;
        document.getElementById('edit_cat_stage').value = stage;
        document.getElementById('edit_cat_weight').value = weight;
        document.getElementById('edit_cat_desc').value = desc;
        document.getElementById('editCategoryForm').action = '/admin/criteria/categories/' + id;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
    }

    function openEditCriterionModal(id, categoryId, code, title, weight, guidelines) {
        document.getElementById('edit_crit_category_id').value = categoryId;
        document.getElementById('edit_crit_code').value = code || '';
        document.getElementById('edit_crit_title').value = title;
        document.getElementById('edit_crit_weight').value = (weight !== null && weight !== 'null' && weight !== undefined) ? weight : '';
        document.getElementById('edit_crit_guidelines').value = guidelines || '';
        document.getElementById('editCriterionForm').action = '/admin/criteria/' + id;
        document.getElementById('editCriterionModal').classList.remove('hidden');
    }

    function closeEditCriterionModal() {
        document.getElementById('editCriterionModal').classList.add('hidden');
    }
</script>
@endpush
