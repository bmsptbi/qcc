<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentPeriod;
use App\Models\AssessmentScore;
use App\Models\Circle;
use App\Models\CircleCategory;
use App\Models\CriteriaCategory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QccAssessmentController extends Controller
{
    /**
     * Dashboard & Circle Overview
     */
    public function index(Request $request): View
    {
        $currentYear = (int) date('Y');
        if ($request->filled('year')) {
            $activeYear = $request->query('year') === 'all' ? 'all' : (int) $request->query('year');
            session(['active_year' => $activeYear]);
        } else {
            $activeYear = $currentYear;
            session(['active_year' => $currentYear]);
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

        // Bersihkan data lama berstatus 'Bronze'
        Circle::where('award_level', 'Bronze')->update(['award_level' => null]);

        if ($activeYear !== 'all') {
            $query->where('year', $activeYear);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            if ($request->status === 'evaluated') {
                $query->whereIn('status', ['evaluated', 'awarded']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('circle_name', 'like', "%{$search}%")
                    ->orWhere('circle_code', 'like', "%{$search}%")
                    ->orWhere('theme_title', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'status');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        switch ($sort) {
            case 'code':
            case 'circle_code':
                $query->orderBy('circle_code', $direction)->orderBy('id', 'asc');
                break;
            case 'name':
            case 'circle_name':
                $query->orderBy('circle_name', $direction)->orderBy('id', 'asc');
                break;
            case 'theme':
            case 'theme_title':
                $query->orderBy('theme_title', $direction)->orderBy('id', 'asc');
                break;
            case 'department':
                $query->orderBy(Department::select('name')->whereColumn('departments.id', 'circles.department_id'), $direction)
                    ->orderBy('id', 'asc');
                break;
            case 'schedule':
            case 'presentation_schedule':
                $query->orderByRaw("presentation_schedule IS NULL, presentation_schedule {$direction}")
                    ->orderBy('id', 'asc');
                break;
            case 'score':
            case 'final_score':
                $query->orderByRaw("final_score IS NULL, final_score {$direction}")
                    ->orderBy('id', 'asc');
                break;
            case 'status':
            default:
                if ($direction === 'desc') {
                    $query->orderByRaw("FIELD(status, 'submitted', 'verified', 'evaluating', 'evaluated') ASC");
                } else {
                    $query->orderByRaw("FIELD(status, 'evaluated', 'evaluating', 'verified', 'submitted') ASC");
                }
                $query->orderBy('circle_code', 'asc')
                    ->orderBy('id', 'asc');
                break;
        }

        $circles = $query->get();

        $statsQuery = Circle::query();
        if ($activeYear !== 'all') {
            $statsQuery->where('year', $activeYear);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'total_circles' => (clone $statsQuery)->count(),
            'submitted' => (clone $statsQuery)->where('status', 'submitted')->count(),
            'verified' => (clone $statsQuery)->where('status', 'verified')->count(),
            'evaluating' => (clone $statsQuery)->where('status', 'evaluating')->count(),
            'evaluated' => (clone $statsQuery)->whereIn('status', ['evaluated', 'awarded'])->count(),
        ];

        // Active judge in session or default to Judge 1 for convenient testing
        $currentJudgeId = session('active_judge_id', User::role('judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId) ?? User::role('judge')->first();
        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();
        $queryYear = $activeYear !== 'all' ? (int) $activeYear : 2026;
        $criteriaStages = CriteriaCategory::where('year', $queryYear)->orderBy('display_order', 'asc')->get();
        if ($criteriaStages->isEmpty()) {
            $criteriaStages = CriteriaCategory::where('year', 2026)->orderBy('display_order', 'asc')->get();
        }

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
            'assessments.judge.department',
            'assessments.scores.criterion.category',
        ]);

        $currentJudgeId = session('active_judge_id', User::role('judge')->first()?->id);
        $currentJudge = User::find($currentJudgeId);

        $myAssessment = $circle->assessments->firstWhere('judge_id', $currentJudgeId);
        $circleYear = (int) ($circle->year ?? 2026);
        $categories = CriteriaCategory::where('year', $circleYear)->with(['criteria' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();
        if ($categories->isEmpty()) {
            $categories = CriteriaCategory::where('year', 2026)->with(['criteria' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order', 'asc');
            }])->orderBy('display_order', 'asc')->get();
        }
        $criteriaStages = $categories;
        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();

        $matrixData = $this->buildAssessmentMatrix($circle, $categories);
        $stagesMatrix = $matrixData['stages'];
        $allEvaluatingJudges = $matrixData['all_evaluating_judges'];
        $calculatedFinalScore = $matrixData['final_score'];

        if ((float) $circle->final_score !== (float) $calculatedFinalScore) {
            $circle->final_score = $calculatedFinalScore;
            $circle->saveQuietly();
        }

        return view('qcc.show', compact(
            'circle',
            'currentJudge',
            'myAssessment',
            'categories',
            'criteriaStages',
            'allJudges',
            'stagesMatrix',
            'allEvaluatingJudges',
            'calculatedFinalScore'
        ));
    }

    /**
     * Mobile-Optimized Assessment Form for Judges
     */
    public function assess(Request $request, Circle $circle): View|RedirectResponse
    {
        if (! in_array($circle->status, ['verified', 'evaluating', 'evaluated', 'awarded'])) {
            return redirect()->route('qcc.circles.show', $circle)
                ->with('error', 'Circle ini belum diverifikasi. Penilaian hanya dapat dilakukan setelah status Circle minimal "Verified".');
        }

        $circle->load(['period', 'category', 'department', 'leader', 'facilitator', 'members']);
        $circleYear = (int) ($circle->year ?? 2026);

        $allJudges = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))->get();
        $allowedJudgeIds = [];

        // Deteksi jika link dikhususkan untuk multiple juri (?judges=1,3)
        if ($request->filled('judges')) {
            $allowedJudgeIds = array_values(array_map('intval', array_filter(explode(',', (string) $request->query('judges')))));
        }

        // Setup Tahap Penilaian yang Aktif / Terkunci
        $activeCategoryId = $request->query('category_id') ?? $request->query('stage_id');
        if (! $activeCategoryId && $request->filled('stage')) {
            $stageName = $request->query('stage');
            $matched = CriteriaCategory::where('year', $circleYear)
                ->where(fn ($q) => $q->where('stage', $stageName)->orWhere('name', 'like', "%{$stageName}%"))
                ->first();
            if ($matched) {
                $activeCategoryId = $matched->id;
            }
        }

        $isStageLocked = false;
        $lockedCategory = null;
        if ($activeCategoryId && $activeCategoryId !== 'all') {
            $lockedCategory = CriteriaCategory::with('criteria')->find($activeCategoryId);
            if ($lockedCategory) {
                $isStageLocked = true;
            } else {
                $activeCategoryId = 'all';
            }
        } else {
            $activeCategoryId = 'all';
        }

        // Resolusi Juri Aktif:
        // Selalu tampil memilih nama juri dulu saat membuka form (tidak mengambil dari cache session)
        $currentJudge = null;
        $mustSelectJudge = true;

        if ($request->filled('judge_id')) {
            $chosenJudge = User::where(fn ($q) => $q->role('judge')->orWhere(fn ($sq) => $sq->role('admin')))
                ->find($request->query('judge_id'));

            if ($chosenJudge) {
                $currentJudge = $chosenJudge;
                $mustSelectJudge = false;
                session(['active_judge_id' => $currentJudge->id]);
            }
        } else {
            // Bersihkan session active_judge_id agar tidak memakai cache juri lama
            session()->forget('active_judge_id');
        }

        // Tentukan daftar juri yang boleh dipilih pada form
        $selectableJudges = ! empty($allowedJudgeIds)
            ? $allJudges->whereIn('id', $allowedJudgeIds)
            : $allJudges;

        // Get all criteria grouped by category for this circle's year
        $categoriesWithCriteria = CriteriaCategory::where('year', $circleYear)->with(['criteria' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();

        if ($categoriesWithCriteria->isEmpty()) {
            $categoriesWithCriteria = CriteriaCategory::where('year', 2026)->with(['criteria' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order', 'asc');
            }])->orderBy('display_order', 'asc')->get();
        }

        foreach ($categoriesWithCriteria as $cat) {
            foreach ($cat->criteria as $crit) {
                $crit->setRelation('category', $cat);
            }
        }

        // Ambil data penilaian yang sudah pernah diisi oleh juri aktif
        $existingAssessment = $currentJudge
            ? Assessment::with('scores')
                ->where('circle_id', $circle->id)
                ->where('judge_id', $currentJudge->id)
                ->first()
            : null;

        $existingScores = $existingAssessment
            ? $existingAssessment->scores->keyBy('assessment_criterion_id')
            : collect();

        // Cek apakah juri aktif sudah pernah menilai circle & tahap ini
        $hasAlreadyAssessed = false;
        if ($currentJudge && $existingAssessment) {
            if ($isStageLocked && $lockedCategory) {
                $lockedCriteriaIds = $lockedCategory->criteria->pluck('id');
                $hasAlreadyAssessed = $lockedCriteriaIds->isNotEmpty()
                    && $existingScores->keys()->intersect($lockedCriteriaIds)->isNotEmpty();
            } else {
                $hasAlreadyAssessed = ($existingAssessment->status === 'submitted') || $existingScores->isNotEmpty();
            }
        }

        // Cari ID seluruh juri yang sudah pernah menilai gugus ini (dan tahap ini jika terkunci)
        $lockedCriteriaIds = ($isStageLocked && $lockedCategory) ? $lockedCategory->criteria->pluck('id') : null;
        $assessedJudgeIds = Assessment::where('circle_id', $circle->id)
            ->whereHas('scores', function ($q) use ($lockedCriteriaIds) {
                if ($lockedCriteriaIds && $lockedCriteriaIds->isNotEmpty()) {
                    $q->whereIn('assessment_criterion_id', $lockedCriteriaIds);
                }
            })
            ->pluck('judge_id')
            ->toArray();

        $categoriesMeta = $categoriesWithCriteria->mapWithKeys(fn ($c) => [
            $c->id => [
                'id' => $c->id,
                'name' => $c->name,
                'weight' => (float) $c->weight_percentage,
            ],
        ])->all();

        return view('qcc.assess', compact(
            'circle',
            'currentJudge',
            'categoriesWithCriteria',
            'categoriesMeta',
            'existingAssessment',
            'existingScores',
            'allJudges',
            'activeCategoryId',
            'isStageLocked',
            'lockedCategory',
            'mustSelectJudge',
            'selectableJudges',
            'allowedJudgeIds',
            'hasAlreadyAssessed',
            'assessedJudgeIds'
        ));
    }

    /**
     * Store or Update Assessment Scores
     */
    public function storeAssessment(Request $request, Circle $circle): RedirectResponse
    {
        if (! in_array($circle->status, ['verified', 'evaluating', 'evaluated', 'awarded'])) {
            return redirect()->route('qcc.circles.show', $circle)
                ->with('error', 'Circle ini belum diverifikasi. Penilaian hanya dapat dilakukan setelah status Circle minimal "Verified".');
        }

        $judgeId = $request->input('judge_id') ?? session('active_judge_id', Auth::id());
        $judge = User::find($judgeId) ?? User::role('judge')->first();

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
                $scoreNum = (float) $scoreVal;
                if ($scoreNum > 10 || $scoreNum < 0) {
                    return back()
                        ->withInput()
                        ->with('error', 'Nilai butir penilaian tidak boleh lebih dari 10 atau kurang dari 0 (Skala 0 - 10).');
                }
                $validScores[$criterionId] = $scoreNum;
            }
        }

        // Jika Kirim Final, pastikan seluruh kriteria pada lingkup penilaian sudah dinilai lengkap
        if ($isSubmit) {
            $requiredCriteriaQuery = AssessmentCriterion::where('is_active', true);
            $targetCategoryId = $request->input('category_id');
            if ($targetCategoryId && $targetCategoryId !== 'all') {
                $requiredCriteriaQuery->where('criteria_category_id', $targetCategoryId);
            } else {
                $circleYear = (int) ($circle->year ?? 2026);
                $yearCatIds = CriteriaCategory::where('year', $circleYear)->pluck('id');
                if ($yearCatIds->isEmpty()) {
                    $yearCatIds = CriteriaCategory::where('year', 2026)->pluck('id');
                }
                $requiredCriteriaQuery->whereIn('criteria_category_id', $yearCatIds);
            }
            $requiredCriteria = $requiredCriteriaQuery->get();
            $missingCriteria = $requiredCriteria->filter(fn ($c) => ! array_key_exists($c->id, $validScores));

            if ($missingCriteria->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('error', 'Penilaian belum lengkap ('.$missingCriteria->count().' butir kriteria belum dinilai). Silakan lengkapi atau gunakan "Simpan Draft" untuk menyimpan sementara.');
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

        // Recalculate Circle Final Score, Award Level, and Status (evaluating/evaluated)
        $this->recalculateCircleScoreAndStatus($circle);

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
            $yearVal = $year === 'all' ? 'all' : (int) $year;
            session(['active_year' => $yearVal]);
        }

        $prevUrl = url()->previous();
        if ($prevUrl) {
            $parsed = parse_url($prevUrl);
            $query = [];
            if (! empty($parsed['query'])) {
                parse_str($parsed['query'], $query);
            }

            $path = $parsed['path'] ?? '';

            // Jika URL sebelumnya memiliki parameter ?year=... atau sedang di halaman criteria, circles, atau leaderboard
            $isCriteria = str_contains($path, '/admin/criteria');
            $isCircles = str_contains($path, '/admin/circles') || str_contains($path, '/circles');
            $isLeaderboard = str_contains($path, '/leaderboard');

            if (isset($query['year']) || $isCriteria || $isCircles || $isLeaderboard) {
                if ($isLeaderboard) {
                    unset($query['year']);
                } elseif ($year === 'all') {
                    if ($isCriteria) {
                        $query['year'] = 2026;
                    } else {
                        $query['year'] = 'all';
                    }
                } else {
                    $query['year'] = (int) $year;
                }

                $newQuery = http_build_query($query);
                $targetUrl = $path.($newQuery ? '?'.$newQuery : '');

                return redirect($targetUrl)->with('info', 'Periode konvensi aktif diubah ke: '.($year === 'all' ? 'Semua Tahun' : 'Tahun '.$year));
            }
        }

        return back()->with('info', 'Periode konvensi aktif diubah ke: '.($year === 'all' ? 'Semua Tahun' : 'Tahun '.$year));
    }

    /**
     * Live Leaderboard & Awarding
     */
    public function leaderboard(Request $request): View
    {
        $currentYear = (int) date('Y');
        if ($request->filled('year')) {
            $activeYear = $request->query('year') === 'all' ? 'all' : (int) $request->query('year');
            session(['active_year' => $activeYear]);
        } else {
            $activeYear = $currentYear;
            session(['active_year' => $currentYear]);
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

        // Leaderboard hanya menampilkan circle yang sudah berstatus verified ke atas
        $query->whereIn('status', ['verified', 'evaluating', 'evaluated', 'awarded']);

        $circles = $query->orderByDesc('final_score')
            ->orderBy('circle_name', 'asc')
            ->get();

        return view('qcc.leaderboard', compact('activePeriod', 'categories', 'circles', 'activeYear'));
    }

    /**
     * Build statistical matrix per stage and judge evaluation
     */
    public function buildAssessmentMatrix(Circle $circle, $categories): array
    {
        $assessments = $circle->assessments()
            ->with(['judge.department', 'scores.criterion'])
            ->get();

        $allEvaluatingJudges = $assessments->map(fn ($a) => $a->judge)->filter()->unique('id')->values();

        $stagesMatrix = [];
        $overallWeightedSum = 0.0;
        $totalWeightSum = 0.0;

        foreach ($categories as $cat) {
            $catCriteria = $cat->criteria;
            $catWeight = (float) $cat->weight_percentage;
            $totalWeightSum += $catWeight;

            // 1. Hitung bobot relatif setiap butir penilaian dalam tahap ini (basis 100%)
            $catCriteriaSum = (float) $catCriteria->sum(fn ($c) => (float) ($c->weight ?? 0));
            $critCount = $catCriteria->count();
            $criteriaRelWeights = [];
            foreach ($catCriteria as $crit) {
                if ($catCriteriaSum > 0 && $crit->weight !== null && (float) $crit->weight > 0) {
                    $criteriaRelWeights[$crit->id] = ((float) $crit->weight / $catCriteriaSum) * 100;
                } else {
                    $criteriaRelWeights[$crit->id] = $critCount > 0 ? (100 / $critCount) : 0;
                }
            }

            // 2. Cari juri yang secara spesifik telah menilai tahap ini
            $catCriteriaIds = $catCriteria->pluck('id')->toArray();
            $stageAssessments = $assessments->filter(function ($a) use ($catCriteriaIds) {
                return $a->scores->whereIn('assessment_criterion_id', $catCriteriaIds)->isNotEmpty();
            });

            $stageJudges = $stageAssessments->map(fn ($a) => $a->judge)->filter()->unique('id')->values();

            // 3. Hitung skor tahap (skala 0 - 10) untuk masing-masing juri pada tahap ini
            $judgeStageScores = [];
            $judgeFeedback = [];

            foreach ($stageJudges as $judge) {
                $judgeAss = $stageAssessments->firstWhere('judge_id', $judge->id);
                if (! $judgeAss) {
                    continue;
                }

                $judgeScoresMap = $judgeAss->scores->keyBy('assessment_criterion_id');
                $weightedSum = 0.0;
                $evaluatedRelWeightSum = 0.0;

                foreach ($catCriteria as $crit) {
                    if ($judgeScoresMap->has($crit->id)) {
                        $rawScore = (float) $judgeScoresMap->get($crit->id)->score;
                        $relW = $criteriaRelWeights[$crit->id] ?? 0;
                        $weightedSum += ($rawScore * $relW);
                        $evaluatedRelWeightSum += $relW;
                    }
                }

                if ($evaluatedRelWeightSum > 0) {
                    $judgeStageScore = $weightedSum / $evaluatedRelWeightSum;
                    $judgeStageScores[$judge->id] = round($judgeStageScore, 2);
                    $judgeFeedback[$judge->id] = [
                        'status' => $judgeAss->status,
                        'strengths' => $judgeAss->feedback_strengths,
                        'improvements' => $judgeAss->feedback_improvements,
                    ];
                }
            }

            // 4. Hitung statistik tahap untuk baris "Nilai Tertimbang"
            $stageScoresList = array_values($judgeStageScores);
            $stageScoresCount = count($stageScoresList);
            $stageMax = $stageScoresCount > 0 ? max($stageScoresList) : null;
            $stageMin = $stageScoresCount > 0 ? min($stageScoresList) : null;
            $stageRange = ($stageMax !== null && $stageMin !== null) ? round($stageMax - $stageMin, 2) : null;
            $stageAverage = $stageScoresCount > 0 ? round(array_sum($stageScoresList) / $stageScoresCount, 2) : null;

            // Deviasi = (Range / Average) dalam percentage (%)
            $stageDeviasi = ($stageAverage !== null && $stageAverage > 0 && $stageRange !== null)
                ? round(($stageRange / $stageAverage) * 100, 2)
                : ($stageScoresCount === 1 ? 0.0 : null);

            $stageStats = [
                'max' => $stageMax,
                'min' => $stageMin,
                'range' => $stageRange,
                'average' => $stageAverage,
                'deviasi_max' => ($stageMax !== null && $stageAverage !== null) ? round($stageMax - $stageAverage, 2) : null,
                'deviasi_min' => ($stageMin !== null && $stageAverage !== null) ? round($stageAverage - $stageMin, 2) : null,
                'deviasi' => $stageDeviasi,
            ];

            // 4b. Auto-Adjustment jika deviasi > batas maksimum (default: 15%)
            $adjustments = [];
            $maxDeviationThreshold = (float) AppSetting::getValue('max_deviation_percentage', 15.0);

            if ($stageStats['deviasi'] !== null && $stageStats['deviasi'] > $maxDeviationThreshold && $stageScoresCount > 1) {
                $currScores = $judgeStageScores;
                $currStats = $stageStats;
                $iteration = 1;
                $maxIterations = 10;

                while ($currStats['deviasi'] !== null && $currStats['deviasi'] > $maxDeviationThreshold && $iteration <= $maxIterations) {
                    $prevAvg = $currStats['average'];
                    $devMax = $currStats['deviasi_max'] ?? 0;
                    $devMin = $currStats['deviasi_min'] ?? 0;

                    $targetJudgeId = null;
                    $actionType = '';

                    if ($devMax >= $devMin) {
                        // Deviasi max lebih besar dari deviasi min: nilai juri terbesar diganti dengan average kalkulasi sebelumnya
                        $highestScore = max($currScores);
                        foreach ($currScores as $jId => $val) {
                            if (abs($val - $highestScore) < 0.0001) {
                                $targetJudgeId = $jId;
                                $actionType = 'max';
                                break;
                            }
                        }
                    } else {
                        // Deviasi min lebih besar dari deviasi max: nilai juri terkecil diganti dengan average kalkulasi sebelumnya
                        $lowestScore = min($currScores);
                        foreach ($currScores as $jId => $val) {
                            if (abs($val - $lowestScore) < 0.0001) {
                                $targetJudgeId = $jId;
                                $actionType = 'min';
                                break;
                            }
                        }
                    }

                    if ($targetJudgeId === null) {
                        break;
                    }

                    $oldScore = $currScores[$targetJudgeId];
                    $currScores[$targetJudgeId] = round($prevAvg, 2);

                    $adjList = array_values($currScores);
                    $adjCount = count($adjList);
                    $adjMax = max($adjList);
                    $adjMin = min($adjList);
                    $adjRange = round($adjMax - $adjMin, 2);
                    $adjAvg = round(array_sum($adjList) / $adjCount, 2);
                    $adjDevMax = round($adjMax - $adjAvg, 2);
                    $adjDevMin = round($adjAvg - $adjMin, 2);
                    $adjDeviasi = ($adjAvg > 0) ? round(($adjRange / $adjAvg) * 100, 2) : 0.0;

                    $currStats = [
                        'max' => $adjMax,
                        'min' => $adjMin,
                        'range' => $adjRange,
                        'average' => $adjAvg,
                        'deviasi_max' => $adjDevMax,
                        'deviasi_min' => $adjDevMin,
                        'deviasi' => $adjDeviasi,
                    ];

                    $isFinal = ($adjDeviasi <= $maxDeviationThreshold || $iteration === $maxIterations);

                    $adjustments[] = [
                        'iteration' => $iteration,
                        'name' => "Adjustment {$iteration}",
                        'judge_scores' => $currScores,
                        'target_judge_id' => $targetJudgeId,
                        'replaced_score' => $oldScore,
                        'new_score' => round($prevAvg, 2),
                        'action_type' => $actionType,
                        'stats' => $currStats,
                        'is_final' => $isFinal,
                    ];

                    $iteration++;
                }
            }

            $isAdjusted = ! empty($adjustments);
            $finalStats = $isAdjusted ? end($adjustments)['stats'] : $stageStats;

            $stageContribution = 0.0;
            if ($finalStats['average'] !== null) {
                $stageContribution = ($finalStats['average'] * $catWeight) / 100;
                $overallWeightedSum += $stageContribution;
            }

            // 5. Hitung baris skor setiap butir kriteria khusus dewan juri di tahap ini
            $criteriaRows = [];
            foreach ($catCriteria as $crit) {
                $critScoresByJudge = [];
                foreach ($stageJudges as $judge) {
                    $judgeAss = $stageAssessments->firstWhere('judge_id', $judge->id);
                    $scoreObj = $judgeAss?->scores->firstWhere('assessment_criterion_id', $crit->id);
                    $critScoresByJudge[$judge->id] = $scoreObj !== null ? (float) $scoreObj->score : null;
                }

                $validCritScores = array_values(array_filter($critScoresByJudge, fn ($v) => $v !== null));
                $vCount = count($validCritScores);

                $critMax = $vCount > 0 ? max($validCritScores) : null;
                $critMin = $vCount > 0 ? min($validCritScores) : null;
                $critRange = ($critMax !== null && $critMin !== null) ? round($critMax - $critMin, 2) : null;
                $critAverage = $vCount > 0 ? round(array_sum($validCritScores) / $vCount, 2) : null;

                // Deviasi = (Range / Average) dalam percentage (%)
                $critDeviasi = ($critAverage !== null && $critAverage > 0 && $critRange !== null)
                    ? round(($critRange / $critAverage) * 100, 2)
                    : ($vCount === 1 ? 0.0 : null);

                $critStats = [
                    'max' => $critMax,
                    'min' => $critMin,
                    'range' => $critRange,
                    'average' => $critAverage,
                    'deviasi_max' => ($critMax !== null && $critAverage !== null) ? round($critMax - $critAverage, 2) : null,
                    'deviasi_min' => ($critMin !== null && $critAverage !== null) ? round($critAverage - $critMin, 2) : null,
                    'deviasi' => $critDeviasi,
                ];

                $criteriaRows[] = [
                    'criterion' => $crit,
                    'rel_weight' => round($criteriaRelWeights[$crit->id] ?? 0, 1),
                    'scores_by_judge' => $critScoresByJudge,
                    'stats' => $critStats,
                ];
            }

            $stagesMatrix[$cat->id] = [
                'category' => $cat,
                'weight' => $catWeight,
                'judges' => $stageJudges,
                'judge_stage_scores' => $judgeStageScores,
                'judge_feedback' => $judgeFeedback,
                'raw_stage_stats' => $stageStats,
                'stage_stats' => $finalStats,
                'adjustments' => $adjustments,
                'is_adjusted' => $isAdjusted,
                'max_deviation_threshold' => $maxDeviationThreshold,
                'stage_contribution' => round($stageContribution, 4),
                'criteria_rows' => $criteriaRows,
            ];
        }

        $calculatedFinalScore = round($overallWeightedSum, 4);

        return [
            'stages' => $stagesMatrix,
            'all_evaluating_judges' => $allEvaluatingJudges,
            'final_score' => $calculatedFinalScore,
        ];
    }

    /**
     * Calculate Sample Standard Deviation
     */
    private function calculateStandardDeviation(array $values): ?float
    {
        $count = count($values);
        if ($count <= 1) {
            return $count === 1 ? 0.0 : null;
        }

        $mean = array_sum($values) / $count;
        $variance = 0.0;
        foreach ($values as $val) {
            $variance += pow($val - $mean, 2);
        }

        return round(sqrt($variance / ($count - 1)), 2);
    }

    /**
     * Export Assessment Summary Matrix to CSV (Excel compatible)
     */
    public function exportAssessmentCsv(Circle $circle): StreamedResponse|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk/login terlebih dahulu untuk mengunduh rekap penilaian.');
        }

        $circleYear = (int) ($circle->year ?? 2026);
        $categories = CriteriaCategory::where('year', $circleYear)->with(['criteria' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();

        if ($categories->isEmpty()) {
            $categories = CriteriaCategory::where('year', 2026)->with(['criteria' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order', 'asc');
            }])->orderBy('display_order', 'asc')->get();
        }

        $matrixData = $this->buildAssessmentMatrix($circle, $categories);
        $stages = $matrixData['stages'];
        $finalScore = $circle->final_score ?? $matrixData['final_score'];

        $filename = 'Rekap_Penilaian_'.str_replace(' ', '_', $circle->circle_name).'_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($circle, $stages, $finalScore) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($stages as $stageData) {
                $cat = $stageData['category'];
                $stageJudges = $stageData['judges'];
                $jCount = count($stageJudges);

                // Top Header Row: Team Permata, Score 7.426667
                $headerRow1 = ['Team', $circle->circle_name];
                for ($i = 0; $i < max(1, $jCount) + 3; $i++) {
                    $headerRow1[] = '';
                }
                $headerRow1[] = 'Score';
                $headerRow1[] = $finalScore;
                fputcsv($handle, $headerRow1);

                // Stage Info
                fputcsv($handle, ['Tahap Penilaian', $cat->name.' (Bobot: '.number_format($stageData['weight'], 0).'%)']);

                if ($jCount === 0) {
                    fputcsv($handle, ['Status', 'Belum ada dewan juri yang memberikan penilaian untuk tahap ini.']);
                    fputcsv($handle, []);
                    fputcsv($handle, []);

                    continue;
                }

                // Table Column Header Row: Item | Judges | Max | Min | Range | Average | Deviasi Max | Deviasi Min | Deviasi (%)
                $showRealNames = (bool) session('admin_unlocked_judges', false);
                $itemHeader = ['Item'];
                foreach ($stageJudges as $idx => $judge) {
                    $itemHeader[] = $showRealNames ? $judge->name : ('Juri '.($idx + 1));
                }
                $itemHeader = array_merge($itemHeader, ['Max', 'Min', 'Range', 'Average', 'Deviasi Max', 'Deviasi Min', 'Deviasi (%)']);
                fputcsv($handle, $itemHeader);

                // Jika ada adjustment, tulis baris Adjustment di atas Nilai Tertimbang (Final di atas)
                if (! empty($stageData['adjustments'])) {
                    foreach (array_reverse($stageData['adjustments']) as $adj) {
                        $adjRow = [$adj['name'].($adj['is_final'] ? ' (Final)' : '')];
                        foreach ($stageJudges as $judge) {
                            $adjRow[] = $adj['judge_scores'][$judge->id] ?? '-';
                        }
                        $adjRow[] = $adj['stats']['max'] !== null ? number_format($adj['stats']['max'], 2) : '-';
                        $adjRow[] = $adj['stats']['min'] !== null ? number_format($adj['stats']['min'], 2) : '-';
                        $adjRow[] = $adj['stats']['range'] !== null ? number_format($adj['stats']['range'], 2) : '-';
                        $adjRow[] = $adj['stats']['average'] !== null ? number_format($adj['stats']['average'], 2) : '-';
                        $adjRow[] = $adj['stats']['deviasi_max'] !== null ? number_format($adj['stats']['deviasi_max'], 2) : '-';
                        $adjRow[] = $adj['stats']['deviasi_min'] !== null ? number_format($adj['stats']['deviasi_min'], 2) : '-';
                        $adjRow[] = $adj['stats']['deviasi'] !== null ? number_format($adj['stats']['deviasi'], 2).'%' : '-';
                        fputcsv($handle, $adjRow);
                    }
                }

                // Nilai Tertimbang Row (Awal)
                $rawStats = $stageData['raw_stage_stats'] ?? $stageData['stage_stats'];
                $tertimbangRow = ['Nilai Tertimbang'.(! empty($stageData['adjustments']) ? ' (Awal)' : '')];
                foreach ($stageJudges as $judge) {
                    $tertimbangRow[] = $stageData['judge_stage_scores'][$judge->id] ?? '-';
                }
                $tertimbangRow[] = $rawStats['max'] !== null ? number_format($rawStats['max'], 2) : '-';
                $tertimbangRow[] = $rawStats['min'] !== null ? number_format($rawStats['min'], 2) : '-';
                $tertimbangRow[] = $rawStats['range'] !== null ? number_format($rawStats['range'], 2) : '-';
                $tertimbangRow[] = $rawStats['average'] !== null ? number_format($rawStats['average'], 2) : '-';
                $tertimbangRow[] = $rawStats['deviasi_max'] !== null ? number_format($rawStats['deviasi_max'], 2) : '-';
                $tertimbangRow[] = $rawStats['deviasi_min'] !== null ? number_format($rawStats['deviasi_min'], 2) : '-';
                $tertimbangRow[] = $rawStats['deviasi'] !== null ? number_format($rawStats['deviasi'], 2).'%' : '-';
                fputcsv($handle, $tertimbangRow);

                // Criteria Rows
                foreach ($stageData['criteria_rows'] as $row) {
                    $crit = $row['criterion'];
                    $stats = $row['stats'];
                    $critRow = [($crit->code ? $crit->code.' - ' : '').$crit->title];

                    foreach ($stageJudges as $judge) {
                        $s = $row['scores_by_judge'][$judge->id] ?? null;
                        $critRow[] = $s !== null ? $s : '-';
                    }

                    $critRow[] = $stats['max'] !== null ? number_format($stats['max'], 2) : '-';
                    $critRow[] = $stats['min'] !== null ? number_format($stats['min'], 2) : '-';
                    $critRow[] = $stats['range'] !== null ? number_format($stats['range'], 2) : '-';
                    $critRow[] = $stats['average'] !== null ? number_format($stats['average'], 2) : '-';
                    $critRow[] = $stats['deviasi_max'] !== null ? number_format($stats['deviasi_max'], 2) : '-';
                    $critRow[] = $stats['deviasi_min'] !== null ? number_format($stats['deviasi_min'], 2) : '-';
                    $critRow[] = $stats['deviasi'] !== null ? number_format($stats['deviasi'], 2).'%' : '-';

                    fputcsv($handle, $critRow);
                }

                // Empty space between stages
                fputcsv($handle, []);
                fputcsv($handle, []);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Verify Admin Password to unlock real judge names
     */
    public function verifyAdminPassword(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $password = $request->input('password');

        // Jika user sedang login dan merupakan admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            if (Hash::check($password, Auth::user()->password)) {
                session(['admin_unlocked_judges' => true]);

                return response()->json([
                    'success' => true,
                    'message' => 'Identitas Administrator terverifikasi.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Password Administrator tidak cocok.',
            ], 422);
        }

        // Cari semua akun admin di database untuk verifikasi password
        $adminUsers = User::role('admin')->get();
        foreach ($adminUsers as $admin) {
            if (Hash::check($password, $admin->password)) {
                session(['admin_unlocked_judges' => true]);

                return response()->json([
                    'success' => true,
                    'message' => 'Identitas Administrator terverifikasi.',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Password Administrator tidak cocok atau Anda tidak memiliki akses admin.',
        ], 422);
    }

    /**
     * Re-hide judge names and remove unlock from session
     */
    public function hideJudgeNames(Request $request): JsonResponse
    {
        session()->forget('admin_unlocked_judges');

        return response()->json([
            'success' => true,
            'revealed' => false,
        ]);
    }

    /**
     * Hapus penilaian dewan juri berdasarkan tahap dan/atau juri tertentu dengan verifikasi password admin.
     */
    public function deleteAssessments(Request $request, Circle $circle): RedirectResponse
    {
        $request->validate([
            'stage_id' => 'required',
            'judge_id' => 'required',
            'admin_password' => 'required|string',
        ]);

        $password = $request->input('admin_password');

        // Verifikasi password admin
        $isAdminValid = false;
        if (Auth::check() && Auth::user()->isAdmin()) {
            $isAdminValid = Hash::check($password, Auth::user()->password);
        } else {
            $adminUsers = User::role('admin')->get();
            foreach ($adminUsers as $admin) {
                if (Hash::check($password, $admin->password)) {
                    $isAdminValid = true;
                    break;
                }
            }
        }

        if (! $isAdminValid) {
            return back()->with('error', 'Password Administrator salah. Tindakan penghapusan penilaian dibatalkan.');
        }

        $stageId = $request->input('stage_id');
        $judgeId = $request->input('judge_id');

        DB::beginTransaction();
        try {
            $deletedDescription = '';

            // 1. Semua Tahap & Semua Juri
            if ($stageId === 'all' && $judgeId === 'all') {
                $assessmentIds = Assessment::where('circle_id', $circle->id)->pluck('id');
                AssessmentScore::whereIn('assessment_id', $assessmentIds)->delete();
                Assessment::where('circle_id', $circle->id)->delete();
                $deletedDescription = "seluruh penilaian (semua tahapan dan seluruh dewan juri) pada circle {$circle->circle_name}";
            }
            // 2. Semua Tahap, tapi Juri Tertentu
            elseif ($stageId === 'all' && $judgeId !== 'all') {
                $judge = User::find($judgeId);
                $judgeName = $judge?->name ?? "Juri (ID: {$judgeId})";
                $assessmentIds = Assessment::where('circle_id', $circle->id)
                    ->where('judge_id', $judgeId)
                    ->pluck('id');
                AssessmentScore::whereIn('assessment_id', $assessmentIds)->delete();
                Assessment::where('circle_id', $circle->id)
                    ->where('judge_id', $judgeId)
                    ->delete();
                $deletedDescription = "seluruh penilaian dari {$judgeName} di semua tahapan pada circle {$circle->circle_name}";
            }
            // 3. Tahap Tertentu, Semua Juri
            elseif ($stageId !== 'all' && $judgeId === 'all') {
                $category = CriteriaCategory::with('criteria')->findOrFail($stageId);
                $criteriaIds = $category->criteria->pluck('id');

                $assessments = Assessment::where('circle_id', $circle->id)->get();
                foreach ($assessments as $ass) {
                    AssessmentScore::where('assessment_id', $ass->id)
                        ->whereIn('assessment_criterion_id', $criteriaIds)
                        ->delete();

                    if ($ass->scores()->count() === 0) {
                        $ass->delete();
                    } else {
                        $this->recalculateSingleAssessment($ass);
                    }
                }
                $deletedDescription = "penilaian pada tahap {$category->name} untuk seluruh dewan juri pada circle {$circle->circle_name}";
            }
            // 4. Tahap Tertentu & Juri Tertentu
            else {
                $category = CriteriaCategory::with('criteria')->findOrFail($stageId);
                $criteriaIds = $category->criteria->pluck('id');
                $judge = User::find($judgeId);
                $judgeName = $judge?->name ?? "Juri (ID: {$judgeId})";

                $ass = Assessment::where('circle_id', $circle->id)
                    ->where('judge_id', $judgeId)
                    ->first();

                if ($ass) {
                    AssessmentScore::where('assessment_id', $ass->id)
                        ->whereIn('assessment_criterion_id', $criteriaIds)
                        ->delete();

                    if ($ass->scores()->count() === 0) {
                        $ass->delete();
                    } else {
                        $this->recalculateSingleAssessment($ass);
                    }
                }
                $deletedDescription = "penilaian tahap {$category->name} dari {$judgeName} pada circle {$circle->circle_name}";
            }

            // Hitung ulang nilai akhir gugus dan perbarui status
            $this->recalculateCircleScoreAndStatus($circle);

            DB::commit();

            return back()->with('success', "Berhasil menghapus {$deletedDescription}.");
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus penilaian: '.$e->getMessage());
        }
    }

    /**
     * Hitung ulang skor total dan skor tertimbang untuk satu baris assessment
     */
    protected function recalculateSingleAssessment(Assessment $assessment): void
    {
        $scores = AssessmentScore::with('criterion.category')->where('assessment_id', $assessment->id)->get();
        if ($scores->isEmpty()) {
            $assessment->delete();

            return;
        }

        $totalScoreRaw = 0;
        $totalWeightedScore = 0;
        $countScored = $scores->count();

        foreach ($scores as $item) {
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
        ]);
    }

    /**
     * Hitung ulang skor akhir (final score), peringkat/award level, dan status gugus
     * Alur status gugus:
     * - submitted: terdaftar awal
     * - verified: diverifikasi oleh admin
     * - evaluating: ketika ada penilaian di salah satu tahap (belum semua tahap selesai)
     * - evaluated: ketika semua tahap penilaian telah selesai dinilai
     */
    protected function recalculateCircleScoreAndStatus(Circle $circle): void
    {
        $circleYear = (int) ($circle->year ?? 2026);
        $categories = CriteriaCategory::where('year', $circleYear)->with(['criteria' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order', 'asc');
        }])->orderBy('display_order', 'asc')->get();

        if ($categories->isEmpty()) {
            $categories = CriteriaCategory::where('year', 2026)->with(['criteria' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order', 'asc');
            }])->orderBy('display_order', 'asc')->get();
        }

        $matrixData = $this->buildAssessmentMatrix($circle, $categories);
        $finalScore = $matrixData['final_score'];
        $circle->final_score = $finalScore;

        if ($finalScore >= 9.0) {
            $circle->award_level = 'Diamond';
        } elseif ($finalScore >= 8.5) {
            $circle->award_level = 'Platinum';
        } elseif ($finalScore >= 8.0) {
            $circle->award_level = 'Gold';
        } elseif ($finalScore >= 7.5) {
            $circle->award_level = 'Silver';
        } else {
            $circle->award_level = null;
        }

        $totalStagesCount = $categories->count();
        $assessedStagesCount = 0;
        foreach ($matrixData['stages'] as $stage) {
            if (! empty($stage['judge_stage_scores'])) {
                $assessedStagesCount++;
            }
        }

        if ($totalStagesCount > 0 && $assessedStagesCount >= $totalStagesCount) {
            $circle->status = 'evaluated';
        } elseif ($assessedStagesCount > 0) {
            $circle->status = 'evaluating';
        } else {
            // Belum ada penilaian sama sekali
            if ($circle->status !== 'verified') {
                $circle->status = 'submitted';
            }
        }
        $circle->save();
    }

    /**
     * Update status circle secara cepat dari tabel dashboard oleh admin (misal: submitted -> verified)
     */
    public function updateQuickStatus(Request $request, Circle $circle): JsonResponse|RedirectResponse
    {
        if (! Auth::check() || (Auth::user()->role !== 'admin' && ! Auth::user()->hasRole('admin'))) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Administrator.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:submitted,verified,evaluating,evaluated'],
        ]);

        $circle->update(['status' => $validated['status']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status circle {$circle->circle_code} berhasil diubah ke ".ucfirst($validated['status']),
                'status' => $circle->status,
            ]);
        }

        return back()->with('success', "Status circle {$circle->circle_code} berhasil diubah ke ".ucfirst($validated['status']));
    }
}
