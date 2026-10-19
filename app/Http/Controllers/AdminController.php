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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

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

        if ($request->filled('category_id')) {
            $query->where('circle_category_id', $request->category_id);
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
            'scheduled' => Circle::where('status', 'scheduled')->count(),
            'evaluated' => Circle::whereIn('status', ['evaluated', 'awarded'])->count(),
            'draft' => Circle::where('status', 'draft')->count(),
        ];

        return view('admin.circles.index', compact('circles', 'periods', 'categories', 'departments', 'stats'));
    }

    /**
     * Tampilkan form pendaftaran circle baru (Hanya Data Utama & Susunan Anggota)
     */
    public function createCircle(): View
    {
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();
        $facilitators = User::role('facilitator')->get();
        if ($facilitators->isEmpty()) {
            $facilitators = User::all();
        }

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
            'department_id' => ['required', 'exists:departments,id'],
            'facilitator_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'in:draft,submitted,verified,scheduled'],
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
                'status' => $validated['status'] ?? 'scheduled',
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
        $circle->load(['members', 'category', 'department', 'facilitator']);
        $periods = AssessmentPeriod::orderByDesc('year')->get();
        $categories = CircleCategory::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'admin'])->get();

        return view('admin.circles.edit', compact('circle', 'periods', 'categories', 'departments', 'facilitators'));
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
            'year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'assessment_period_id' => ['nullable', 'exists:assessment_periods,id'],
            'circle_category_id' => ['required', 'exists:circle_categories,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'facilitator_id' => ['nullable', 'exists:users,id'],
            'problem_statement' => ['nullable', 'string'],
            'target_statement' => ['nullable', 'string'],
            'presentation_schedule' => ['nullable', 'date'],
            'presentation_room' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:draft,submitted,verified,scheduled,evaluated,awarded'],
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
}
