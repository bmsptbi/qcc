<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Circle;
use App\Models\CircleCategory;
use App\Models\CircleMember;
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
}
