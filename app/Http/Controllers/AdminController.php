<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan form pendaftaran circle baru
     */
    public function createCircle(): View
    {
        $periods = AssessmentPeriod::orderByDesc('year')->get();
        $categories = CircleCategory::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $facilitators = User::whereIn('role', ['facilitator', 'admin'])->get();

        // Rekomendasi kode gugus berikutnya (cth: QCC-2026-005)
        $latestId = Circle::max('id') + 1;
        $year = date('Y');
        $suggestedCode = sprintf('QCC-%s-%03d', $year, $latestId);

        return view('admin.circles.create', compact('periods', 'categories', 'departments', 'facilitators', 'suggestedCode'));
    }

    /**
     * Simpan pendaftaran circle dan anggota timnya
     */
    public function storeCircle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'circle_code' => ['required', 'string', 'max:50', 'unique:circles,circle_code'],
            'circle_name' => ['required', 'string', 'max:150'],
            'theme_title' => ['required', 'string', 'max:255'],
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'circle_category_id' => ['required', 'exists:circle_categories,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'facilitator_id' => ['nullable', 'exists:users,id'],
            'problem_statement' => ['nullable', 'string'],
            'target_statement' => ['nullable', 'string'],
            'presentation_schedule' => ['nullable', 'date'],
            'presentation_room' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:draft,submitted,verified,scheduled'],
            'members' => ['nullable', 'array'],
            'members.*.name' => ['required_with:members.*.nik', 'string', 'max:150'],
            'members.*.nik' => ['required_with:members.*.name', 'string', 'max:50'],
            'members.*.role' => ['required_with:members.*.name', 'in:leader,secretary,facilitator,member'],
            'members.*.email' => ['nullable', 'email'],
        ]);

        $circle = Circle::create([
            'assessment_period_id' => $validated['assessment_period_id'],
            'circle_category_id' => $validated['circle_category_id'],
            'department_id' => $validated['department_id'],
            'facilitator_id' => $validated['facilitator_id'] ?? null,
            'circle_code' => strtoupper($validated['circle_code']),
            'circle_name' => $validated['circle_name'],
            'theme_title' => $validated['theme_title'],
            'problem_statement' => $validated['problem_statement'] ?? null,
            'target_statement' => $validated['target_statement'] ?? null,
            'presentation_schedule' => $validated['presentation_schedule'] ?? null,
            'presentation_room' => $validated['presentation_room'] ?? null,
            'status' => $validated['status'],
        ]);

        // Simpan anggota tim
        if (! empty($validated['members'])) {
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

        return redirect()->route('qcc.circles.show', $circle)
            ->with('success', "Gugus {$circle->circle_name} ({$circle->circle_code}) berhasil didaftarkan!");
    }

    /**
     * Halaman manajemen juri dan penugasan
     */
    public function judgesIndex(): View
    {
        $judges = User::where('role', 'judge')->with(['department', 'judgeAssignments.period', 'judgeAssignments.category'])->get();
        $departments = Department::where('is_active', true)->get();
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
}
