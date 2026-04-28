<?php

namespace App\Models\Concerns;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRatings
{
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    public function approvedRatings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable')
            ->where('status', 'approved');
    }

    /**
     * Recalculate & persist avg + count ke kolom summary.
     * Dipanggil via Observer setelah create/update/delete rating.
     */
    public function recalculateRatingSummary(): void
    {
        $summary = $this->approvedRatings()
            ->selectRaw('AVG(score) as avg, COUNT(*) as total')
            ->first();

        $this->update([
            'rating_avg'   => round($summary->avg ?? 0, 2),
            'rating_count' => $summary->total ?? 0,
        ]);
    }
}
