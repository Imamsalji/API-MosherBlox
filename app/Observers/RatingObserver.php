<?php

namespace App\Observers;

use App\Models\Rating;

class RatingObserver
{
    public function created(Rating $rating): void
    {
        $this->syncSummary($rating);
    }

    public function updated(Rating $rating): void
    {
        // Hanya sync jika score atau status berubah
        if ($rating->isDirty(['score', 'status'])) {
            $this->syncSummary($rating);
        }
    }

    public function deleted(Rating $rating): void
    {
        $this->syncSummary($rating);
    }

    private function syncSummary(Rating $rating): void
    {
        $ratable = $rating->ratable;

        if ($ratable && method_exists($ratable, 'recalculateRatingSummary')) {
            $ratable->recalculateRatingSummary();
        }
    }
}
