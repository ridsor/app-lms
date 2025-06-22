<?php

namespace App\Observers;

use App\Models\Period;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodObserver
{
    /**
     * Handle the Period "created" event.
     */
    public function created(Period $period): void
    {
        //
    }

    /**
     * Handle the Period "updated" event.
     */
    public function updated(Period $period): void
    {
        //
    }

    /**
     * Handle the Period "deleted" event.
     */
    public function deleted(Period $period): void
    {
        //
    }

    /**
     * Handle the Period "restored" event.
     */
    public function restored(Period $period): void
    {
        //
    }

    /**
     * Handle the Period "force deleted" event.
     */
    public function forceDeleted(Period $period): void
    {
        //
    }

    /**
     * Handle the Period "saving" event.
     *
     * @param  \App\Models\Period  $period
     * @return void
     */
    public function saving(Period $period)
    {
        if ($period->isDirty('status') && $period->status) {
            DB::transaction(function () use ($period) {
                $query = Period::where('status', true);

                if ($period->exists) {
                    $query->where('id', '!=', $period->id);
                }

                $query->update(['status' => false]);
            });
        }
    }
}
