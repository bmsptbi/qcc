<?php

namespace App\Models;

use Database\Factories\AssessmentScoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assessment_id', 'assessment_criterion_id', 'score', 'weighted_score', 'notes'])]
class AssessmentScore extends Model
{
    /** @use HasFactory<AssessmentScoreFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'weighted_score' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(AssessmentCriterion::class, 'assessment_criterion_id');
    }
}
