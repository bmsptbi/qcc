<?php

namespace App\Models;

use Database\Factories\CriteriaCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'stage', 'weight_percentage', 'display_order', 'description'])]
class CriteriaCategory extends Model
{
    /** @use HasFactory<CriteriaCategoryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'weight_percentage' => 'decimal:2',
            'display_order' => 'integer',
        ];
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(AssessmentCriterion::class);
    }
}
