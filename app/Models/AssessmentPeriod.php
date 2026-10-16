<?php

namespace App\Models;

use Database\Factories\AssessmentPeriodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'year', 'theme', 'start_date', 'end_date', 'registration_deadline', 'status', 'notes'])]
class AssessmentPeriod extends Model
{
    /** @use HasFactory<AssessmentPeriodFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_deadline' => 'date',
        ];
    }

    public function circles(): HasMany
    {
        return $this->hasMany(Circle::class);
    }

    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(JudgeAssignment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
