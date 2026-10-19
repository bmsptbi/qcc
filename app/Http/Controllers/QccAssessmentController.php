<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentPeriod;
use App\Models\AssessmentScore;
use App\Models\Circle;
use App\Models\CircleCategory;
use App\Models\CriteriaCategory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QccAssessmentController extends Controller
{
    /**
     * Dashboard & Circle Overview
     */
    public function index(Request $request): View
    {
        $activeYear = session('active_year', $request->query('year', 2026));
        if ($request->filled('year')) {
            $activeYear = $request->query('year') === 'all' ? 'all' : (int) $request->query('year');
            session(['active_year' => $activeYear]);
        }

        $activePeriod = null;
        if ($activeYear !== 'all') {
            $activePeriod = AssessmentPeriod::where('year', $activeYear)->first();
        }
        if (! $activePeriod) {
            $activePeriod = AssessmentPeriod::where('status', 'active')->latest()->first()
                ?? AssessmentPeriod::latest()->first();
        }

        $categories = CircleCategory::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->orderBy('display_order', 'asc')->get();

        $query = Circle::with(['category', 'department', 'leader', 'facilitator', 'members'])
            ->withCount(['members', 'assessments']);

        if ($activeYear !== 'all') {
            $query->where('year', $activeYear);
        }

        if ($request->filled('category')) {
            $query->where('circle_category_id', $request->category);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('circle_name', 'like', "%{$search}%")
                    ->orWhere('circle_code', 'like', "%{$search}%")
                    ->orWhere('theme_title', 'like', "%{$search}%");
            });
        }

        $circles = $query->orderBy('presentation_schedule', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $statsQuery = Circle::query();
        if ($activeYear !== 'all') {
            $statsQuery->where('year', $activeYear);
        }

        $stats = [
            'total_circles' => (clone $statsQuery)->count(),
            'scheduled_today' => (clone $statsQuery)->whereDate('presentation_schedule', now()->toDateString())->count(),
            'completed_assessment' => (clone $statsQuery)->whereIn('status', ['evaluated', 'awarded'])->count(),
            'active_judges' => User::role('judge')->count(),
        ];

        // Active judge in session or default to Judge 1 for convenient testing
        $currentJudgeId = session('active_judge_id', User::role('judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId) ?? User::role('judge')->first();
        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();
        $criteriaStages = CriteriaCategory::orderBy('display_order', 'asc')->get();

        return view('qcc.index', compact(
            'activePeriod',
            'categories',
            'departments',
            'circles',
            'stats',
            'currentJudge',
            'allJudges',
            'criteriaStages',
            'activeYear'
        ));
    }

    /**
     * Circle Detail Page
     */
    public function show(Circle $circle): View
    {
        $circle->load([
            'period',
            'category',
            'department',
            'leader',
            'facilitator',
            'members',
            'assessments.judge',
            'assessments.scores.criterion',
        ]);

        $currentJudgeId = session('active_judge_id', User::role('judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId);

        $myAssessment = $circle->assessments->firstWhere('judge_id', $currentJudgeId);
        $categories = CriteriaCategory::orderBy('display_order', 'asc')->get();
        $criteriaStages = $categories;
        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();

        return view('qcc.show', compact('circle', 'currentJudge', 'myAssessment', 'categories', 'criteriaStages', 'allJudges'));
    }

    /**
     * Mobile-Optimized Assessment Form for Judges
     */
    public function assess(Request $request, Circle $circle): View
    {
        $circle->load(['period', 'category', 'department', 'leader', 'facilitator', 'members']);

        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();
        $mustSelectJudge = false;
        $allowedJudgeIds = [];

        // Deteksi jika link dikhususkan untuk multiple juri (?judges=1,3)
        if ($request->filled('judges')) {
            $allowedJudgeIds = array_values(array_map('intval', array_filter(explode(',', (string) $request->query('judges')))));
        }

        // Check if judge_id is explicitly passed in query
        if ($request->filled('judge_id')) {
            $sharedJudge = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->find($request->query('judge_id'));
            if ($sharedJudge) {
                session(['active_judge_id' => $sharedJudge->id]);
            }
        } elseif (! empty($allowedJudgeIds)) {
            if (count($allowedJudgeIds) === 1) {
                $sharedJudge = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->find($allowedJudgeIds[0]);
                if ($sharedJudge) {
                    session(['active_judge_id' => $sharedJudge->id]);
                }
            } else {
                // Link ditujukan ke multiple juri: wajib memilih nama saat buka form!
                $sessionJudgeId = session('active_judge_id');
                if (! $sessionJudgeId || ! in_array((int) $sessionJudgeId, $allowedJudgeIds, true) || $request->has('choose_judge')) {
                    $mustSelectJudge = true;
                    session()->forget('active_judge_id');
                }
            }
        } elseif ($request->has('choose_judge')) {
            $mustSelectJudge = true;
        }

        $currentJudgeId = session('active_judge_id', Auth::check() ? Auth::id() : null);
        $currentJudge = $currentJudgeId ? User::find($currentJudgeId) : null;

        // Jika belum ada juri terpilih, juri harus memilih nama terlebih dahulu
        if (! $currentJudge) {
            $mustSelectJudge = true;
        }

        // Tentukan daftar juri yang boleh dipilih pada form
        $selectableJudges = ! empty($allowedJudgeIds)
            ? $allJudges->whereIn('id', $allowedJudgeIds)
            : $allJudges;

        // Check if category_id or stage_id is passed in query (share stage setup)
        $activeCategoryId = $request->query('category_id') ?? $request->query('stage_id');
        if (! $activeCategoryId && $request->filled('stage')) {
            $stageName = $request->query('stage');
            $matched = CriteriaCategory::where('stage', $stageName)->orWhere('name', 'like', "%{$stageName}%")->first();
            if ($matched) {
                $activeCategoryId = $matched->id;
            }
        }

        // Get all criteria grouped by category
        $categoriesWithCriteria = CriteriaCategory::with(['criteria' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();

        foreach ($categoriesWithCriteria as $cat) {
            foreach ($cat->criteria as $crit) {
                $crit->setRelation('category', $cat);
            }
        }

        // Check if judge has an existing draft/submission
        $existingAssessment = $currentJudge
            ? Assessment::with('scores')
                ->where('circle_id', $circle->id)
                ->where('judge_id', $currentJudge->id)
                ->first()
            : null;

        $existingScores = $existingAssessment
            ? $existingAssessment->scores->keyBy('assessment_criterion_id')
            : collect();

        return view('qcc.assess', compact(
            'circle',
            'currentJudge',
            'categoriesWithCriteria',
            'existingAssessment',
            'existingScores',
            'allJudges',
            'activeCategoryId',
            'mustSelectJudge',
            'selectableJudges',
            'allowedJudgeIds'
        ));
    }

    /**
     * Store or Update Assessment Scores
     */
    public function storeAssessment(Request $request, Circle $circle): RedirectResponse
    {
        $judgeId = $request->input('judge_id') ?? session('active_judge_id', Auth::id());
        $judge = User::find($judgeId) ?? User::where('role', 'judge')->first();

        if (! $judge) {
            return back()->with('error', 'Juri penilai tidak ditemukan.');
        }

        session(['active_judge_id' => $judge->id]);

        $scoresInput = $request->input('scores', []);
        $notesInput = $request->input('notes', []);
        $isSubmit = $request->input('action') === 'submit';

        // Filter valid scores (hanya yang diisi; nilai default adalah null / belum dinilai)
        $validScores = [];
        foreach ($scoresInput as $criterionId => $scoreVal) {
            if ($scoreVal !== null && $scoreVal !== '' && is_numeric($scoreVal)) {
                $validScores[$criterionId] = max(0, min(100, (float) $scoreVal));
            }
        }

        // Get or create Assessment record
        $assessment = Assessment::firstOrCreate(
            [
                'circle_id' => $circle->id,
                'judge_id' => $judge->id,
            ],
            [
                'assessment_period_id' => $circle->assessment_period_id,
                'total_score' => 0,
                'weighted_score' => 0,
                'status' => 'draft',
            ]
        );

        // Save Detail Scores untuk butir-butir yang telah dinilai
        $criteria = AssessmentCriterion::with('category.criteria')->whereIn('id', array_keys($validScores))->get()->keyBy('id');

        foreach ($validScores as $criterionId => $score) {
            $crit = $criteria->get($criterionId);
            $weightInParent = $crit ? (float) $crit->effective_weight : 1.0;
            $subtotalInParent = ($score * $weightInParent) / 100;

            AssessmentScore::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'assessment_criterion_id' => $criterionId,
                ],
                [
                    'score' => round($score, 2),
                    'weighted_score' => round($subtotalInParent, 2),
                    'notes' => $notesInput[$criterionId] ?? null,
                ]
            );
        }

        // Hitung akumulasi total skor dan weighted score berdasarkan seluruh butir yang tersimpan pada assessment ini
        $allAssessmentScores = AssessmentScore::with('criterion.category.criteria')
            ->where('assessment_id', $assessment->id)
            ->get();

        $totalScoreRaw = 0;
        $totalWeightedScore = 0;
        $countScored = $allAssessmentScores->count();

        foreach ($allAssessmentScores as $item) {
            $crit = $item->criterion;
            $score = (float) $item->score;
            $weightInParent = $crit ? (float) $crit->effective_weight : 1.0;
            $catWeight = $crit?->category ? (float) $crit->category->weight_percentage : 100.0;

            $totalScoreRaw += $score;
            $globalContribution = ($score * $weightInParent / 100) * ($catWeight / 100);
            $totalWeightedScore += $globalContribution;
        }

        $averageRawScore = $countScored > 0 ? ($totalScoreRaw / $countScored) : 0;

        $assessment->update([
            'total_score' => round($averageRawScore, 2),
            'weighted_score' => round($totalWeightedScore, 2),
            'feedback_strengths' => $request->input('feedback_strengths', $assessment->feedback_strengths),
            'feedback_improvements' => $request->input('feedback_improvements', $assessment->feedback_improvements),
            'status' => $isSubmit ? 'submitted' : 'draft',
            'submitted_at' => $isSubmit ? now() : $assessment->submitted_at,
        ]);

        // Recalculate Circle Final Score and Award if all submitted
        $submittedAssessments = Assessment::where('circle_id', $circle->id)
            ->where('status', 'submitted')
            ->get();

        if ($submittedAssessments->isNotEmpty()) {
            $finalWeighted = $submittedAssessments->avg('weighted_score');
            $circle->final_score = round($finalWeighted, 2);

            // Award classification
            if ($finalWeighted >= 90) {
                $circle->award_level = 'Diamond';
            } elseif ($finalWeighted >= 85) {
                $circle->award_level = 'Platinum';
            } elseif ($finalWeighted >= 80) {
                $circle->award_level = 'Gold';
            } elseif ($finalWeighted >= 75) {
                $circle->award_level = 'Silver';
            } else {
                $circle->award_level = 'Bronze';
            }

            $circle->status = 'evaluated';
            $circle->save();
        } elseif ($circle->status === 'scheduled') {
            $circle->status = 'under_assessment';
            $circle->save();
        }

        $message = $isSubmit
            ? 'Penilaian berhasil dikirim secara final!'
            : 'Draft penilaian berhasil disimpan.';

        return redirect()->route('qcc.circles.show', $circle)->with('success', $message);
    }

    /**
     * Switch Active Judge for easy testing
     */
    public function switchJudge(Request $request): RedirectResponse
    {
        $judgeId = $request->input('judge_id');
        if (User::where('id', $judgeId)->exists()) {
            session(['active_judge_id' => $judgeId]);
        }

        return back()->with('info', 'Beralih ke akun penilai: '.User::find($judgeId)?->name);
    }

    /**
     * Switch Active Convention Year Period
     */
    public function switchYear(Request $request): RedirectResponse
    {
        $year = $request->input('year');
        if ($year === 'all' || is_numeric($year)) {
            session(['active_year' => $year === 'all' ? 'all' : (int) $year]);
        }

        return back()->with('info', 'Periode konvensi aktif diubah ke: '.($year === 'all' ? 'Semua Tahun' : 'Tahun '.$year));
    }

    /**
     * Live Leaderboard & Awarding
     */
    public function leaderboard(Request $request): View
    {
        $activeYear = session('active_year', $request->query('year', 2026));
        if ($request->filled('year')) {
            $activeYear = $request->query('year') === 'all' ? 'all' : (int) $request->query('year');
            session(['active_year' => $activeYear]);
        }

        $activePeriod = null;
        if ($activeYear !== 'all') {
            $activePeriod = AssessmentPeriod::where('year', $activeYear)->first();
        }
        if (! $activePeriod) {
            $activePeriod = AssessmentPeriod::where('status', 'active')->latest()->first()
                ?? AssessmentPeriod::latest()->first();
        }

        $categories = CircleCategory::where('is_active', true)->get();

        $query = Circle::with(['category', 'department', 'leader', 'assessments'])
            ->withCount('assessments');

        if ($activeYear !== 'all') {
            $query->where('year', $activeYear);
        }

        if ($request->filled('category')) {
            $query->where('circle_category_id', $request->category);
        }

        $circles = $query->orderByDesc('final_score')
            ->orderBy('circle_name', 'asc')
            ->get();

        return view('qcc.leaderboard', compact('activePeriod', 'categories', 'circles', 'activeYear'));
    }
}
