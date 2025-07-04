<?php

namespace App\Observers;

use App\Models\Curriculum;
use Illuminate\Support\Facades\DB;

class CurriculumObsever
{
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
