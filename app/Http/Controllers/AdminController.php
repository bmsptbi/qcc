<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentPeriod;
use App\Models\Circle;
use App\Models\CircleCategory;
use App\Models\CircleMember;
use App\Models\CriteriaCategory;
use App\Models\Department;
use App\Models\JudgeAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Halaman manajemen daftar gugus QCC (Tabel, Filter, Edit, Hapus)
     */
    public function circlesIndex(Request $request): View
    {
        $periods = AssessmentPeriod::orderByDesc('year')->get();
        $categories = CircleCategory::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();

        $query = Circle::with(['period', 'category', 'department', 'leader', 'facilitator', 'members'])
            ->withCount(['members', 'assessments']);

        if ($request->filled('period_id')) {
            $query->where('assessment_period_id', $request->period_id);
        }

        if ($request->filled('year') && $request->year !== 'all') {
            $query->where('year', $request->year);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('circle_name', 'like', "%{$s}%")
                    ->orWhere('circle_code', 'like', "%{$s}%")
                    ->orWhere('theme_title', 'like', "%{$s}%");
            });
        }

        $circles = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => Circle::count(),
            'submitted' => Circle::where('status', 'submitted')->count(),
            'verified' => Circle::where('status', 'verified')->count(),
            'evaluating' => Circle::where('status', 'evaluating')->count(),
            'evaluated' => Circle::whereIn('status', ['evaluated', 'awarded'])->count(),
        ];

        return view('admin.circles.index', compact('circles', 'periods', 'categories', 'departments', 'stats'));
    }

    /**
     * Tampilkan form pendaftaran circle baru (Hanya Data Utama & Susunan Anggota)
     */
    public function createCircle(): View
    {
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();
        $facilitators = User::orderBy('name', 'asc')->get();

        $activeYear = session('active_year', (int) date('Y'));

        return view('admin.circles.create', compact('departments', 'facilitators', 'activeYear'));
    }

    /**
     * Simpan pendaftaran circle dan anggota timnya (Kode otomatis di-generate unik di backend)
     */
    public function storeCircle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'circle_code' => ['nullable', 'string', 'max:50'],
            'circle_name' => ['required', 'string', 'max:150'],
            'theme_title' => ['required', 'string', 'max:255'],
            'sharepoint_url' => ['nullable', 'string', 'max:2000'],
            'department_id' => ['required', 'exists:departments,id'],
            'facilitator_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'in:submitted,verified,evaluating,evaluated,draft,scheduled,awarded'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'circle_category_id' => ['nullable', 'exists:circle_categories,id'],
            'assessment_period_id' => ['nullable', 'exists:assessment_periods,id'],
            'members' => ['nullable', 'array'],
            'members.*.name' => ['required_with:members.*.nik', 'string', 'max:150'],
            'members.*.nik' => ['required_with:members.*.name', 'string', 'max:50'],
            'members.*.role' => ['required_with:members.*.name', 'in:leader,secretary,facilitator,member'],
            'members.*.email' => ['nullable', 'email'],
        ]);

        $year = (int) ($validated['year'] ?? session('active_year', date('Y')));
        $periodId = $validated['assessment_period_id'] ?? null;
        if (! $periodId) {
            $period = AssessmentPeriod::firstOrCreate(
                ['year' => $year],
                [
                    'name' => "Konvensi Mutu & QCC {$year}",
                    'theme' => "Continuous Improvement & Innovation {$year}",
                    'status' => 'active',
                    'start_date' => "{$year}-01-01",
                    'end_date' => "{$year}-12-31",
                ]
            );
            $periodId = $period->id;
        }

        $categoryId = $validated['circle_category_id'] ?? CircleCategory::first()?->id;

        $circle = DB::transaction(function () use ($validated, $year, $periodId, $categoryId) {
            // Auto-generate kode gugus secara unik jika tidak diisi manual
            $circleCode = ! empty($validated['circle_code']) ? strtoupper(trim($validated['circle_code'])) : null;

            if (! $circleCode) {
                // Lock row tahun terkait agar tidak ada tabrakan nomor saat user mendaftar bersamaan
                $count = Circle::where('year', $year)->lockForUpdate()->count();
                $seq = $count + 1;
                do {
                    $candidate = sprintf('QCC-%d-%03d', $year, $seq);
                    if (! Circle::where('circle_code', $candidate)->exists()) {
                        $circleCode = $candidate;
                        break;
                    }
                    $seq++;
                } while (true);
            }

            $newCircle = Circle::create([
                'assessment_period_id' => $periodId,
                'year' => $year,
                'circle_category_id' => $categoryId,
                'department_id' => $validated['department_id'],
                'facilitator_id' => $validated['facilitator_id'] ?? null,
                'circle_code' => $circleCode,
                'circle_name' => $validated['circle_name'],
                'theme_title' => $validated['theme_title'],
                'sharepoint_url' => $validated['sharepoint_url'] ?? null,
                'status' => $validated['status'] ?? 'submitted',
            ]);

            // Simpan anggota tim
            if (! empty($validated['members'])) {
                foreach ($validated['members'] as $m) {
                    if (! empty($m['name']) && ! empty($m['nik'])) {
                        CircleMember::create([
                            'circle_id' => $newCircle->id,
                            'name' => $m['name'],
                            'nik' => $m['nik'],
                            'role' => $m['role'] ?? 'member',
                            'email' => $m['email'] ?? null,
                        ]);
                    }
                }
            }

            return $newCircle;
        });

        return redirect()->route('admin.circles.index')
            ->with('success', "Gugus {$circle->circle_name} ({$circle->circle_code}) periode {$circle->year} berhasil didaftarkan!");
    }

    /**
     * Tampilkan form edit circle
     */
    public function editCircle(Circle $circle): View
    {
        $circle->load(['members', 'department', 'facilitator']);
        $periods = AssessmentPeriod::orderByDesc('year')->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();
        $facilitators = User::orderBy('name', 'asc')->get();

        return view('admin.circles.edit', compact('circle', 'periods', 'departments', 'facilitators'));
    }

    /**
     * Update data gugus QCC beserta anggota timnya
     */
    public function updateCircle(Request $request, Circle $circle): RedirectResponse
    {
        $validated = $request->validate([
            'circle_code' => ['required', 'string', 'max:50', 'unique:circles,circle_code,'.$circle->id],
            'circle_name' => ['required', 'string', 'max:150'],
            'theme_title' => ['required', 'string', 'max:255'],
            'sharepoint_url' => ['nullable', 'string', 'max:2000'],
            'year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'assessment_period_id' => ['nullable', 'exists:assessment_periods,id'],
            'circle_category_id' => ['nullable', 'exists:circle_categories,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'facilitator_id' => ['nullable', 'exists:users,id'],
            'problem_statement' => ['nullable', 'string'],
            'target_statement' => ['nullable', 'string'],
            'presentation_schedule' => ['nullable', 'date'],
            'presentation_room' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:submitted,verified,evaluating,evaluated,draft,scheduled,awarded'],
            'members' => ['nullable', 'array'],
            'members.*.name' => ['required_with:members.*.nik', 'string', 'max:150'],
            'members.*.nik' => ['required_with:members.*.name', 'string', 'max:50'],
            'members.*.role' => ['required_with:members.*.name', 'in:leader,secretary,facilitator,member'],
            'members.*.email' => ['nullable', 'email'],
        ]);

        $year = (int) $validated['year'];
        $periodId = $validated['assessment_period_id'] ?? null;
        if (! $periodId) {
            $period = AssessmentPeriod::firstOrCreate(
                ['year' => $year],
                [
                    'name' => "Konvensi Mutu & QCC {$year}",
                    'theme' => "Continuous Improvement & Innovation {$year}",
                    'status' => 'active',
                    'start_date' => "{$year}-01-01",
                    'end_date' => "{$year}-12-31",
                ]
            );
            $periodId = $period->id;
        }

        $circle->update([
            'assessment_period_id' => $periodId,
            'year' => $year,
            'circle_category_id' => $validated['circle_category_id'] ?? $circle->circle_category_id ?? CircleCategory::first()?->id,
            'department_id' => $validated['department_id'],
            'facilitator_id' => $validated['facilitator_id'] ?? null,
            'circle_code' => strtoupper($validated['circle_code']),
            'circle_name' => $validated['circle_name'],
            'theme_title' => $validated['theme_title'],
            'sharepoint_url' => $validated['sharepoint_url'] ?? null,
            'problem_statement' => $validated['problem_statement'] ?? null,
            'target_statement' => $validated['target_statement'] ?? null,
            'presentation_schedule' => $validated['presentation_schedule'] ?? null,
            'presentation_room' => $validated['presentation_room'] ?? null,
            'status' => $validated['status'],
        ]);

        // Perbarui anggota tim jika diberikan
        if (isset($validated['members'])) {
            $circle->members()->delete();
            foreach ($validated['members'] as $m) {
                if (! empty($m['name']) && ! empty($m['nik'])) {
                    CircleMember::create([
                        'circle_id' => $circle->id,
                        'name' => $m['name'],
                        'nik' => $m['nik'],
                        'role' => $m['role'] ?? 'member',
                        'email' => $m['email'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.circles.index')
            ->with('success', "Data gugus {$circle->circle_name} ({$circle->circle_code}) berhasil diperbarui!");
    }

    /**
     * Hapus gugus QCC beserta anggota dan skor penilaiannya
     */
    public function destroyCircle(Circle $circle): RedirectResponse
    {
        $name = $circle->circle_name;
        $code = $circle->circle_code;

        foreach ($circle->assessments as $assessment) {
            $assessment->scores()->delete();
            $assessment->delete();
        }

        $circle->members()->delete();
        $circle->delete();

        return redirect()->route('admin.circles.index')
            ->with('success', "Gugus {$name} ({$code}) beserta seluruh data penilaiannya telah dihapus!");
    }

    /**
     * Export data seluruh gugus ke format Excel / CSV
     */
    public function exportCircles(Request $request): StreamedResponse
    {
        $query = Circle::with(['department', 'facilitator', 'members'])->orderBy('year', 'desc')->orderBy('circle_code', 'asc');

        if ($request->filled('year') && $request->year !== 'all') {
            $query->where('year', $request->year);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('circle_name', 'like', "%{$s}%")
                    ->orWhere('circle_code', 'like', "%{$s}%")
                    ->orWhere('theme_title', 'like', "%{$s}%");
            });
        }

        $circles = $query->get();
        $filename = 'Data_Gugus_QCC_'.date('Ymd_His').'.csv';

        return response()->stream(function () use ($circles) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM agar terbaca sempurna di Excel

            // Header kolom
            fputcsv($handle, [
                'Kode Gugus',
                'Nama Gugus',
                'Judul Tema',
                'Departemen',
                'Tahun',
                'Email Fasilitator',
                'Link SharePoint',
                'Status',
                'Susunan Anggota',
            ]);

            foreach ($circles as $c) {
                // Urutkan anggota: Leader urutan 1, Secretary urutan 2, Member urutan 3 dst
                $roleOrder = ['leader' => 1, 'secretary' => 2, 'member' => 3];
                $sortedMembers = $c->members->sort(function ($a, $b) use ($roleOrder) {
                    $orderA = $roleOrder[$a->role] ?? 4;
                    $orderB = $roleOrder[$b->role] ?? 4;

                    return $orderA <=> $orderB ?: $a->id <=> $b->id;
                });

                // Format: Nama*NIK
                $membersFormatted = $sortedMembers->map(function ($m) {
                    return "{$m->name}*{$m->nik}";
                })->implode('; ');

                fputcsv($handle, [
                    $c->circle_code,
                    $c->circle_name,
                    $c->theme_title,
                    $c->department?->name ?? '',
                    $c->year,
                    $c->facilitator?->email ?? '',
                    $c->sharepoint_url ?? '',
                    $c->status,
                    $membersFormatted,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh template contoh format Excel / CSV untuk import gugus
     */
    public function downloadCircleTemplate(): StreamedResponse
    {
        $filename = 'Template_Import_Gugus_QCC.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Gugus',
                'Nama Gugus',
                'Judul Tema',
                'Departemen',
                'Tahun',
                'Email Fasilitator',
                'Link SharePoint',
                'Status',
                'Susunan Anggota',
            ]);

            // Contoh 1: Edit gugus lama (menggunakan Kode Gugus yang sudah ada)
            // Susunan Anggota format: Nama*NIK (urutan 1 = Leader, urutan 2 = Secretary, urutan 3 dst = Anggota)
            fputcsv($handle, [
                'QCC-2026-001',
                'TES CIRCLE EDIT',
                'Optimalisasi Efisiensi Mesin Drawing Line 2',
                'Manufacturing & Operations',
                '2026',
                'iman.sugih@bekaert.com',
                'https://bekaert.sharepoint.com/:f:/r/teams/qcc-tes',
                'scheduled',
                'Budi Santoso*10001; Siti Aminah*10002; Joko Widodo*10003',
            ]);

            // Contoh 2: Tambah gugus baru (Kode Gugus dikosongkan agar otomatis di-generate)
            fputcsv($handle, [
                '',
                'GUGUS INOVASI BARU',
                'Pengurangan Scrap Wire Melalui Kaizen 5S',
                'Engineering',
                '2026',
                'antonius.aswin@bekaert.com',
                '',
                'scheduled',
                'Ahmad Dahlan*10004; Dewi Sartika*10005',
            ]);

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Import data gugus dari file Excel / CSV (Bulk Create & Update)
     */
    public function importCircles(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ], [
            'file.required' => 'Silakan pilih file CSV yang ingin diimpor.',
            'file.max' => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        // Buka file dan deteksi delimiter
        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return back()->with('error', 'File yang diunggah kosong.');
        }

        // Hapus BOM jika ada
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return back()->with('error', 'File tidak memiliki baris data.');
        }

        // Deteksi delimiter (koma, titik koma, tab)
        $firstLine = $lines[0];
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');
        $tabCount = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $handle = fopen($filePath, 'r');
        // Lewati BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'Gagal membaca baris header CSV.');
        }

        // Petakan kolom
        $colMap = [
            'code' => null,
            'name' => null,
            'theme' => null,
            'dept' => null,
            'year' => null,
            'fac' => null,
            'sharepoint' => null,
            'status' => null,
            'members' => null,
        ];

        foreach ($header as $idx => $col) {
            $colClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $col)));
            if (str_contains($colClean, 'kodegugus') || $colClean === 'code' || $colClean === 'circlecode') {
                $colMap['code'] = $idx;
            } elseif (str_contains($colClean, 'namagugus') || $colClean === 'name' || $colClean === 'circlename') {
                $colMap['name'] = $idx;
            } elseif (str_contains($colClean, 'tema') || $colClean === 'themetitle' || $colClean === 'judul') {
                $colMap['theme'] = $idx;
            } elseif (str_contains($colClean, 'departemen') || $colClean === 'department' || $colClean === 'dept') {
                $colMap['dept'] = $idx;
            } elseif (str_contains($colClean, 'tahun') || $colClean === 'year') {
                $colMap['year'] = $idx;
            } elseif (str_contains($colClean, 'fasilitator') || str_contains($colClean, 'facilitator') || str_contains($colClean, 'emailfasilitator')) {
                $colMap['fac'] = $idx;
            } elseif (str_contains($colClean, 'sharepoint') || str_contains($colClean, 'link')) {
                $colMap['sharepoint'] = $idx;
            } elseif (str_contains($colClean, 'status')) {
                $colMap['status'] = $idx;
            } elseif (str_contains($colClean, 'anggota') || str_contains($colClean, 'member')) {
                $colMap['members'] = $idx;
            }
        }

        if ($colMap['name'] === null && $colMap['theme'] === null) {
            fclose($handle);

            return back()->with('error', 'Format header CSV tidak sesuai. Minimal kolom "Nama Gugus" dan "Judul Tema" harus tersedia.');
        }

        $createdCount = 0;
        $updatedCount = 0;
        $activeYearDefault = (int) session('active_year', date('Y'));

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                    continue;
                }

                $codeVal = $colMap['code'] !== null && isset($row[$colMap['code']]) ? strtoupper(trim($row[$colMap['code']])) : '';
                $nameVal = $colMap['name'] !== null && isset($row[$colMap['name']]) ? trim($row[$colMap['name']]) : '';
                $themeVal = $colMap['theme'] !== null && isset($row[$colMap['theme']]) ? trim($row[$colMap['theme']]) : '';
                $deptVal = $colMap['dept'] !== null && isset($row[$colMap['dept']]) ? trim($row[$colMap['dept']]) : '';
                $yearVal = $colMap['year'] !== null && isset($row[$colMap['year']]) && is_numeric(trim($row[$colMap['year']])) ? (int) trim($row[$colMap['year']]) : $activeYearDefault;
                $facVal = $colMap['fac'] !== null && isset($row[$colMap['fac']]) ? trim($row[$colMap['fac']]) : '';
                $spVal = $colMap['sharepoint'] !== null && isset($row[$colMap['sharepoint']]) ? trim($row[$colMap['sharepoint']]) : '';
                $statusVal = $colMap['status'] !== null && isset($row[$colMap['status']]) ? strtolower(trim($row[$colMap['status']])) : 'submitted';
                $membersVal = $colMap['members'] !== null && isset($row[$colMap['members']]) ? trim($row[$colMap['members']]) : '';

                if (empty($nameVal)) {
                    continue;
                }

                if (! in_array($statusVal, ['submitted', 'verified', 'evaluating', 'evaluated', 'draft', 'scheduled', 'awarded'])) {
                    $statusVal = 'submitted';
                }

                // Cari atau pasangkan Departemen
                $department = null;
                if (! empty($deptVal)) {
                    $department = Department::where('name', 'like', "%{$deptVal}%")->first();
                }
                if (! $department) {
                    $department = Department::first();
                }

                // Cari Fasilitator berdasarkan Email (atau fallback NIK / Nama)
                $facilitator = null;
                if (! empty($facVal)) {
                    $facilitator = User::where('email', $facVal)->first()
                        ?? User::where('nik', $facVal)->first()
                        ?? User::where('name', 'like', "%{$facVal}%")->first();
                }

                // Periode
                $period = AssessmentPeriod::firstOrCreate(
                    ['year' => $yearVal],
                    [
                        'name' => "Konvensi Mutu & QCC {$yearVal}",
                        'theme' => "Continuous Improvement & Innovation {$yearVal}",
                        'status' => 'active',
                        'start_date' => "{$yearVal}-01-01",
                        'end_date' => "{$yearVal}-12-31",
                    ]
                );

                // Cek apakah gugus sudah ada berdasarkan Kode Gugus
                $circle = null;
                if (! empty($codeVal)) {
                    $circle = Circle::where('circle_code', $codeVal)->first();
                }

                if ($circle) {
                    // Update gugus yang sudah ada
                    $circle->update([
                        'circle_name' => $nameVal,
                        'theme_title' => $themeVal ?: $circle->theme_title,
                        'department_id' => $department->id,
                        'year' => $yearVal,
                        'assessment_period_id' => $period->id,
                        'facilitator_id' => $facilitator ? $facilitator->id : $circle->facilitator_id,
                        'sharepoint_url' => $spVal ?: $circle->sharepoint_url,
                        'status' => $statusVal,
                    ]);
                    $updatedCount++;
                } else {
                    // Buat kode jika kosong
                    if (empty($codeVal)) {
                        $count = Circle::where('year', $yearVal)->count();
                        $seq = $count + 1;
                        do {
                            $candidate = sprintf('QCC-%d-%03d', $yearVal, $seq);
                            if (! Circle::where('circle_code', $candidate)->exists()) {
                                $codeVal = $candidate;
                                break;
                            }
                            $seq++;
                        } while (true);
                    }

                    $circle = Circle::create([
                        'circle_code' => $codeVal,
                        'circle_name' => $nameVal,
                        'theme_title' => $themeVal ?: $nameVal,
                        'department_id' => $department->id,
                        'year' => $yearVal,
                        'assessment_period_id' => $period->id,
                        'circle_category_id' => CircleCategory::first()?->id,
                        'facilitator_id' => $facilitator?->id,
                        'sharepoint_url' => $spVal ?: null,
                        'status' => $statusVal,
                    ]);
                    $createdCount++;
                }

                // Sinkronisasi Anggota jika kolom anggota terisi
                // Format yang didukung: Nama*NIK dipisahkan ';'
                // Posisi otomatis: Urutan ke-1 = Leader, Urutan ke-2 = Secretary, Urutan ke-3 dst = Member
                if (! empty($membersVal)) {
                    $circle->members()->delete();
                    $memberItems = preg_split('/[;\n]+/', $membersVal);
                    $memberIndex = 0;

                    foreach ($memberItems as $mItem) {
                        $mItem = trim($mItem);
                        if (empty($mItem)) {
                            continue;
                        }

                        // Parse Nama*NIK (atau fallback : atau (NIK))
                        $mName = $mItem;
                        $mNik = '';

                        if (str_contains($mItem, '*')) {
                            $parts = explode('*', $mItem);
                            $mName = trim($parts[0] ?? '');
                            $mNik = trim($parts[1] ?? '');
                        } elseif (str_contains($mItem, ':')) {
                            $parts = explode(':', $mItem);
                            $mName = trim($parts[0] ?? '');
                            $mNik = trim($parts[1] ?? '');
                        } elseif (preg_match('/^(.*?)\s*\((.*?)\)$/', $mItem, $matches)) {
                            $mName = trim($matches[1]);
                            $mNik = trim($matches[2]);
                        }

                        if (empty($mNik)) {
                            $mNik = 'EMP-'.rand(1000, 9999);
                        }

                        // Penentuan peran otomatis sesuai urutan:
                        // 1 = Leader, 2 = Secretary, 3 dst = Member
                        if ($memberIndex === 0) {
                            $mRole = 'leader';
                        } elseif ($memberIndex === 1) {
                            $mRole = 'secretary';
                        } else {
                            $mRole = 'member';
                        }

                        if (! empty($mName)) {
                            CircleMember::create([
                                'circle_id' => $circle->id,
                                'name' => $mName,
                                'nik' => $mNik,
                                'role' => $mRole,
                            ]);
                            $memberIndex++;
                        }
                    }
                }
            }

            DB::commit();
            fclose($handle);

            return redirect()->route('admin.circles.index')
                ->with('success', "Proses import berhasil! {$createdCount} gugus baru ditambahkan dan {$updatedCount} gugus berhasil diperbarui.");
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            return back()->with('error', 'Gagal memproses file import: '.$e->getMessage());
        }
    }

    /**
     * Halaman manajemen juri dan penugasan
     */
    public function judgesIndex(): View
    {
        $judges = User::role('judge')->with(['department', 'judgeAssignments.period', 'judgeAssignments.category'])->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();
        $periods = AssessmentPeriod::orderByDesc('year')->get();
        $categories = CircleCategory::where('is_active', true)->get();

        return view('admin.judges.index', compact('judges', 'departments', 'periods', 'categories'));
    }

    /**
     * Simpan pendaftaran juri baru
     */
    public function storeJudge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nik' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $judge = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password'),
            'role' => 'judge',
            'roles' => ['judge'],
            'nik' => $validated['nik'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', "Juri {$judge->name} berhasil ditambahkan!");
    }

    /**
     * Buat penugasan juri ke periode & kategori
     */
    public function assignJudge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judge_id' => ['required', 'exists:users,id'],
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'circle_category_id' => ['nullable', 'exists:circle_categories,id'],
            'is_lead_judge' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        JudgeAssignment::create([
            'judge_id' => $validated['judge_id'],
            'assessment_period_id' => $validated['assessment_period_id'],
            'circle_category_id' => $validated['circle_category_id'] ?? null,
            'is_lead_judge' => (bool) ($validated['is_lead_judge'] ?? false),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Penugasan juri berhasil ditambahkan!');
    }

    /**
     * Hapus penugasan juri
     */
    public function destroyAssignment(JudgeAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return back()->with('success', 'Penugasan juri telah dihapus.');
    }

    /**
     * Update data dewan juri
     */
    public function updateJudge(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'nik' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('success', "Data juri {$user->name} berhasil diperbarui!");
    }

    /**
     * Hapus dewan juri
     */
    public function destroyJudge(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->judgeAssignments()->delete();
        $user->delete();

        return back()->with('success', "Juri {$name} berhasil dihapus!");
    }

    /**
     * Halaman kelola kategori & butir kriteria penilaian
     */
    public function criteriaIndex(): View
    {
        $categories = CriteriaCategory::with(['criteria' => function ($q) {
            $q->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();

        foreach ($categories as $cat) {
            foreach ($cat->criteria as $crit) {
                $crit->setRelation('category', $cat);
            }
        }

        $totalWeight = $categories->sum('weight_percentage');
        $totalCriteria = AssessmentCriterion::count();

        return view('admin.criteria.index', compact('categories', 'totalWeight', 'totalCriteria'));
    }

    /**
     * Simpan kategori kriteria baru
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'stage' => ['required', 'string', 'max:50'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'display_order' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        CriteriaCategory::create($validated);

        return back()->with('success', "Kategori {$validated['name']} berhasil ditambahkan!");
    }

    /**
     * Simpan butir kriteria baru (Kode otomatis jika kosong, bobot proporsional jika blank)
     */
    public function storeCriterion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'criteria_category_id' => ['required', 'exists:criteria_categories,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'guidelines' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_score' => ['nullable', 'numeric', 'min:0'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $category = CriteriaCategory::findOrFail($validated['criteria_category_id']);

        // Generate kode otomatis jika kosong
        if (empty($validated['code'])) {
            $cleanName = trim(preg_replace('/^[0-9\.\s]+/', '', $category->name));
            $words = preg_split('/\s+/', $cleanName);
            $prefix = '';
            if (count($words) >= 2) {
                $prefix = strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
            } else {
                $prefix = strtoupper(substr($words[0] ?? 'K', 0, 2));
            }

            $count = AssessmentCriterion::where('criteria_category_id', $category->id)->count() + 1;
            $validated['code'] = sprintf('%s%02d', $prefix, $count);
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        // Bobot: jika dikosongkan simpan null (menandakan proporsional otomatis)
        if ($request->input('weight') === '' || $request->input('weight') === null) {
            $validated['weight'] = null;
        }

        AssessmentCriterion::create($validated);

        return back()->with('success', "Butir kriteria [{$validated['code']}] {$validated['title']} berhasil ditambahkan!");
    }

    /**
     * Update kategori / tahap yang sudah ada
     */
    public function updateCategory(Request $request, CriteriaCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'stage' => ['required', 'string', 'max:50'],
            'weight_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return back()->with('success', "Tahap {$category->name} berhasil diperbarui!");
    }

    /**
     * Hapus kategori / tahap beserta kriteria di dalamnya
     */
    public function destroyCategory(CriteriaCategory $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return back()->with('success', "Tahap '{$name}' beserta seluruh kriteria di dalamnya telah dihapus.");
    }

    /**
     * Update butir kriteria yang sudah ada
     */
    public function updateCriterion(Request $request, AssessmentCriterion $criterion): RedirectResponse
    {
        $validated = $request->validate([
            'criteria_category_id' => ['required', 'exists:criteria_categories,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'guidelines' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = $criterion->code;
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        // Bobot: jika dikosongkan simpan null (proporsional otomatis)
        if ($request->input('weight') === '' || $request->input('weight') === null) {
            $validated['weight'] = null;
        }

        $criterion->update($validated);

        return back()->with('success', "Butir kriteria [{$criterion->code}] {$criterion->title} berhasil diperbarui!");
    }

    /**
     * Hapus butir kriteria
     */
    public function destroyCriterion(AssessmentCriterion $criterion): RedirectResponse
    {
        $code = $criterion->code;
        $criterion->delete();

        return back()->with('success', "Kriteria {$code} berhasil dihapus.");
    }

    /**
     * Halaman Manajemen Pengguna (User Management)
     */
    public function usersIndex(Request $request): View
    {
        $query = User::with(['department']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();

        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::role('admin')->count(),
            'total_judges' => User::role('judge')->count(),
            'total_facilitators' => User::role('facilitator')->count(),
            'total_leaders' => User::role('circle_leader')->count(),
        ];

        return view('admin.users.index', compact('users', 'departments', 'stats'));
    }

    /**
     * Simpan pengguna baru
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nik' => ['nullable', 'string', 'max:50'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'in:admin,judge,facilitator,circle_leader,member'],
            'role' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $roles = $validated['roles'] ?? [];
        if (empty($roles) && ! empty($validated['role'])) {
            $roles = [$validated['role']];
        }
        if (empty($roles)) {
            $roles = ['member'];
        }

        $roles = array_values(array_unique($roles));
        $primaryRole = in_array('admin', $roles, true) ? 'admin'
            : (in_array('judge', $roles, true) ? 'judge'
            : (in_array('facilitator', $roles, true) ? 'facilitator'
            : (in_array('circle_leader', $roles, true) ? 'leader' : $roles[0])));

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'] ?? null,
            'role' => $primaryRole,
            'roles' => $roles,
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Pengguna [{$user->name}] berhasil ditambahkan!");
    }

    /**
     * Perbarui data pengguna
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'nik' => ['nullable', 'string', 'max:50'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'in:admin,judge,facilitator,circle_leader,member'],
            'role' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $roles = $validated['roles'] ?? [];
        if (empty($roles) && ! empty($validated['role'])) {
            $roles = [$validated['role']];
        }
        if (empty($roles)) {
            $roles = $user->roles ?: ['member'];
        }

        $roles = array_values(array_unique($roles));
        $primaryRole = in_array('admin', $roles, true) ? 'admin'
            : (in_array('judge', $roles, true) ? 'judge'
            : (in_array('facilitator', $roles, true) ? 'facilitator'
            : (in_array('circle_leader', $roles, true) ? 'leader' : $roles[0])));

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'] ?? null,
            'role' => $primaryRole,
            'roles' => $roles,
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', "Data pengguna [{$user->name}] berhasil diperbarui!");
    }

    /**
     * Hapus akun pengguna
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun pengguna [{$name}] telah berhasil dihapus.");
    }

    /**
     * Halaman pengaturan konfigurasi sistem (Batas Deviasi Maksimum, dll.)
     */
    public function settingsIndex(): View
    {
        $maxDeviation = (float) AppSetting::getValue('max_deviation_percentage', 15.0);

        return view('admin.settings.index', compact('maxDeviation'));
    }

    /**
     * Simpan pembaruan pengaturan sistem
     */
    public function settingsUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'max_deviation_percentage' => 'required|numeric|min:1|max:100',
        ], [
            'max_deviation_percentage.required' => 'Nilai batas deviasi maksimum wajib diisi.',
            'max_deviation_percentage.numeric' => 'Nilai batas deviasi harus berupa angka numerik.',
            'max_deviation_percentage.min' => 'Batas deviasi minimal adalah 1%.',
            'max_deviation_percentage.max' => 'Batas deviasi maksimal adalah 100%.',
        ]);

        $newVal = round((float) $validated['max_deviation_percentage'], 2);

        AppSetting::setValue(
            'max_deviation_percentage',
            (string) $newVal,
            'Batas toleransi maksimum deviasi nilai dewan juri per tahap (persen)'
        );

        return back()->with('success', "Pengaturan berhasil disimpan! Batas toleransi deviasi juri kini diatur ke {$newVal}%.");
    }
}
