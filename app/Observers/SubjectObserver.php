<?php

namespace App\Observers;

use App\Models\Period;
use App\Models\Subject;

class SubjectObserver
{
  public function creating(Subject $subject)
  {
    $existingIds = Subject::pluck('id')->toArray();

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

    $subject->id = $nextId;

    // Generate code untuk subject
    $educationLevel = env('EDUCATION_LEVEL', '01');
    $educationLevel = match ($educationLevel) {
      'SD' => '01',
      'SMP' => '02',
      'SMA' => '03',
      default => $educationLevel
    };
    $activePeriod = Period::where('status', true)->first();
    if ($activePeriod) {
      $semester = $activePeriod->semester;
      $semester = match ($semester) {
        'odd' => '01',
        'even' => '02',
        default => $semester
      };
      $academicYear = $activePeriod->academic_year;
      $tahun = explode('/', $academicYear);
      if (count($tahun) == 2) {
        $academicYear = substr($tahun[0], -2) . substr($tahun[1], -2);
      }
      $subject->code =  $educationLevel . '.' . $nextId . '.' . $semester . '.' . $academicYear;
    }
  }
}
