<?php

namespace App\Observers;

use App\Models\Subject;

class SubjectObserver
{
  public function creating(Subject $subject)
  {
    $last = Subject::orderByDesc('id')->first();
    $nextNumber = $last ? ((int)preg_replace('/\D/', '', $last->code)) + 1 : 1;
    $subject->code = 'MP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
  }
}
