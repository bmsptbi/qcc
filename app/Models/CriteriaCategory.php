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

    /**
     * Hitung bobot proporsional untuk butir yang bobotnya dikosongkan (blank / null)
     * Bobot ini adalah bobot terhadap parent (kategori/tahap) dengan basis 100%
     */
    public function getProportionalWeightForNulls(): float
    {
        $criteria = $this->criteria;
        $count = $criteria->count();
        if ($count === 0) {
            return 0.0;
        }

        $explicitSum = $criteria->whereNotNull('weight')->sum(fn ($c) => (float) $c->weight);
        $nullCount = $criteria->whereNull('weight')->count();

        if ($nullCount === 0) {
            return 0.0;
        }

        $remainingWeight = max(0, 100.0 - $explicitSum);

        return round($remainingWeight / $nullCount, 2);
    }
}
