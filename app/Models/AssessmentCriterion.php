<?php

namespace App\Models;

use Database\Factories\AssessmentCriterionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'criteria_category_id',
    'code',
    'title',
    'guidelines',
    'min_score',
    'max_score',
    'weight',
    'display_order',
    'is_active',
])]
class AssessmentCriterion extends Model
{
    /** @use HasFactory<AssessmentCriterionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'weight' => 'decimal:2',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CriteriaCategory::class, 'criteria_category_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class);
    }

    /**
     * Dapatkan bobot efektif: jika diisi manual gunakan nilai tersebut,
     * jika blank/null hitung proporsional dari kategori
     */
    public function getEffectiveWeightAttribute(): float
    {
        if ($this->weight !== null && (float) $this->weight > 0) {
            return (float) $this->weight;
        }

        if ($this->category) {
            return $this->category->getProportionalWeightForNulls();
        }

        return 1.0;
    }
}
