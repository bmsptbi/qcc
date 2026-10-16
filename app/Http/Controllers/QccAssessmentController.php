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
        $activePeriod = AssessmentPeriod::where('status', 'active')->latest()->first()
            ?? AssessmentPeriod::latest()->first();

        $categories = CircleCategory::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        $query = Circle::with(['category', 'department', 'leader', 'facilitator', 'members'])
            ->withCount(['members', 'assessments']);

        if ($activePeriod) {
            $query->where('assessment_period_id', $activePeriod->id);
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

        $stats = [
            'total_circles' => Circle::count(),
            'scheduled_today' => Circle::whereDate('presentation_schedule', now()->toDateString())->count(),
            'completed_assessment' => Circle::whereIn('status', ['evaluated', 'awarded'])->count(),
            'active_judges' => User::where('role', 'judge')->count(),
        ];

        // Active judge in session or default to Judge 1 for convenient testing
        $currentJudgeId = session('active_judge_id', User::where('role', 'judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId) ?? User::where('role', 'judge')->first();
        $allJudges = User::whereIn('role', ['judge', 'admin'])->get();

        return view('qcc.index', compact(
            'activePeriod',
            'categories',
            'departments',
            'circles',
            'stats',
            'currentJudge',
            'allJudges'
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

        $currentJudgeId = session('active_judge_id', User::where('role', 'judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId);

        $myAssessment = $circle->assessments->firstWhere('judge_id', $currentJudgeId);

        return view('qcc.show', compact('circle', 'currentJudge', 'myAssessment'));
    }

    /**
     * Mobile-Optimized Assessment Form for Judges
     */
    public function assess(Circle $circle): View
    {
        $circle->load(['period', 'category', 'department', 'leader', 'facilitator', 'members']);

        $currentJudgeId = session('active_judge_id', User::where('role', 'judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId) ?? User::where('role', 'judge')->first();

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
        $existingAssessment = Assessment::with('scores')
            ->where('circle_id', $circle->id)
            ->where('judge_id', $currentJudge?->id)
            ->first();

        $existingScores = $existingAssessment
            ? $existingAssessment->scores->keyBy('assessment_criterion_id')
            : collect();

        $allJudges = User::whereIn('role', ['judge', 'admin'])->get();

        return view('qcc.assess', compact(
            'circle',
            'currentJudge',
            'categoriesWithCriteria',
            'existingAssessment',
            'existingScores',
            'allJudges'
        ));
    }

    /**
     * Store or Update Assessment Scores
     */
    public function storeAssessment(Request $request, Circle $circle): RedirectResponse
    {
        $currentJudgeId = session('active_judge_id', User::where('role', 'judge')->first()?->id);
        $judge = User::find($currentJudgeId) ?? User::where('role', 'judge')->first();

        if (! $judge) {
            return back()->with('error', 'Juri aktif tidak ditemukan.');
        }

        $scoresInput = $request->input('scores', []);
        $notesInput = $request->input('notes', []);
        $isSubmit = $request->input('action') === 'submit';

        // Calculate total score and weighted score
        $criteria = AssessmentCriterion::with('category.criteria')->whereIn('id', array_keys($scoresInput))->get()->keyBy('id');

        $totalScoreRaw = 0;
        $totalWeightedScore = 0;
        $criteriaCount = $criteria->count();

        foreach ($scoresInput as $criterionId => $scoreVal) {
            $score = max(0, min(100, (float) $scoreVal));
            $crit = $criteria->get($criterionId);
            $weightInParent = $crit ? (float) $crit->effective_weight : 1.0;
            $catWeight = $crit?->category ? (float) $crit->category->weight_percentage : 100.0;

            $totalScoreRaw += $score;
            // Kontribusi skor terhadap nilai akhir (bobot parent * bobot kategori)
            $globalContribution = ($score * $weightInParent / 100) * ($catWeight / 100);
            $totalWeightedScore += $globalContribution;
        }

        $averageRawScore = $criteriaCount > 0 ? ($totalScoreRaw / $criteriaCount) : 0;

        // Upsert Assessment
        $assessment = Assessment::updateOrCreate(
            [
                'circle_id' => $circle->id,
                'judge_id' => $judge->id,
            ],
            [
                'assessment_period_id' => $circle->assessment_period_id,
                'total_score' => round($averageRawScore, 2),
                'weighted_score' => round($totalWeightedScore, 2),
                'feedback_strengths' => $request->input('feedback_strengths'),
                'feedback_improvements' => $request->input('feedback_improvements'),
                'status' => $isSubmit ? 'submitted' : 'draft',
                'submitted_at' => $isSubmit ? now() : null,
            ]
        );

        // Save Detail Scores
        foreach ($scoresInput as $criterionId => $scoreVal) {
            $score = max(0, min(100, (float) $scoreVal));
            $crit = $criteria->get($criterionId);
            $weightInParent = $crit ? (float) $crit->effective_weight : 1.0;
            // Subtotal skor di dalam kategori / tahap
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
     * Live Leaderboard & Awarding
     */
    public function leaderboard(Request $request): View
    {
        $activePeriod = AssessmentPeriod::where('status', 'active')->latest()->first()
            ?? AssessmentPeriod::latest()->first();

        $categories = CircleCategory::where('is_active', true)->get();

        $query = Circle::with(['category', 'department', 'leader', 'assessments'])
            ->withCount('assessments');

        if ($activePeriod) {
            $query->where('assessment_period_id', $activePeriod->id);
        }

        if ($request->filled('category')) {
            $query->where('circle_category_id', $request->category);
        }

        $circles = $query->orderByDesc('final_score')
            ->orderBy('circle_name', 'asc')
            ->get();

        return view('qcc.leaderboard', compact('activePeriod', 'categories', 'circles'));
    }
}
