<?php

namespace App\Observers;

use App\Models\Curriculum;
use Illuminate\Support\Facades\DB;

class CurriculumObserver
{
  /**
   * Handle the Curriculum "saving" event.
   *
   * @param  \App\Models\Curriculum  $curriculum
   * @return void
   */
  public function saving(Curriculum $curriculum)
  {
    if ($curriculum->isDirty('status') && $curriculum->status) {
      DB::transaction(function () use ($curriculum) {
        $query = Curriculum::where('status', true);
        if ($curriculum->exists) {
          $query->where('id', '!=', $curriculum->id);
        }
        $query->update(['status' => false]);
      });
    }
  }
}
