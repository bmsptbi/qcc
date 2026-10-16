<?php

namespace App\Models;

use Database\Factories\JudgeAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assessment_period_id', 'judge_id', 'circle_category_id', 'circle_id', 'is_lead_judge', 'notes'])]
class JudgeAssignment extends Model
{
    /** @use HasFactory<JudgeAssignmentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_lead_judge' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CircleCategory::class, 'circle_category_id');
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }
}
