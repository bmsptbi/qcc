<?php

namespace App\Models;

use Database\Factories\CircleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'assessment_period_id',
    'year',
    'circle_category_id',
    'department_id',
    'facilitator_id',
    'leader_id',
    'circle_code',
    'circle_name',
    'theme_title',
    'sharepoint_url',
    'problem_statement',
    'target_statement',
    'paper_file_path',
    'presentation_file_path',
    'presentation_schedule',
    'presentation_room',
    'status',
    'final_score',
    'award_level',
    'ranking',
])]
class Circle extends Model
{
    /** @use HasFactory<CircleFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Circle $circle) {
            if (empty($circle->year) && $circle->assessment_period_id) {
                $circle->year = $circle->period?->year ?? (int) date('Y');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'presentation_schedule' => 'datetime',
            'final_score' => 'decimal:2',
            'ranking' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'assessment_period_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CircleCategory::class, 'circle_category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
