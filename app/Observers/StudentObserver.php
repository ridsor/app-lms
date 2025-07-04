<?php

namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentObserver
{
    public function saving(Student $student)
    {
        $homeroom_teacher = $student->homeroom_teacher;
        $user = $homeroom_teacher ? $homeroom_teacher->user : null;
        if ($homeroom_teacher) {
            if ($user && !$user->hasPermissionTo('student.view.homeroomteacher') && !$user->hasPermissionTo('student.edit.homeroomteacher')) {
                $user->givePermissionTo([
                    'student.view.homeroomteacher',
                    'student.edit.homeroomteacher'
                ]);
            }
        } else {
            if ($user && $user->hasPermissionTo('student.view.homeroomteacher') && $user->hasPermissionTo('student.edit.homeroomteacher')) {
                $user->revokePermissionTo('student.view.homeroomteacher');
                $user->revokePermissionTo('student.edit.homeroomteacher');
            }
        }
    }
}
