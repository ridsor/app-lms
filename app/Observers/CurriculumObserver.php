<?php

namespace App\Observers;

use App\Models\Curriculum;

class CurriculumObserver
{
  public function creating(Curriculum $curriculum)
  {
    $existingIds = Curriculum::pluck('id')->toArray();

    $nextId = null;
    $i = 1;
    while (true) {
      $length = ($i > 99) ? 3 : 2;
      $formattedId = str_pad($i, $length, '0', STR_PAD_LEFT);
      if (!in_array($formattedId, $existingIds)) {
        $nextId = $formattedId;
        break;
      }
      $i++;
    }

    $curriculum->id = $nextId;
  }
}
