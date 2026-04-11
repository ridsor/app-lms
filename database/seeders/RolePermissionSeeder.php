<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions untuk student management
        $permissions = [
            'student.*',
            'student.edit.homeroomteacher',
            'student.view.homeroomteacher',
            'teacher.*',
            'parent.*',
            'class.*',
            'major.*',
            'room.*',
            'period.*',
            'curriculum.*',
            'subject.*',
            'user.*',
            'schedule.*',
            'schedule.view',
            'meeting.edit',
            'meeting.view',
            'attendance.edit',
            'attendance.view',
            'teaching_journal.view',
            'teaching_journal.edit',
            'teaching_journal.create',
            'material.view',
            'material.*',
            'task.view',
            'task.*',
            'meeting_text.view',
            'meeting_text.*',
            'task_submission.view',
            'task_submission.edit',
            'exam.view',
            'exam.create',
            'exam.edit',
            'exam.delete',
            'question.view',
            'question.create',
            'question.edit',
            'question.delete',
            'ukk.view',
            'ukk.create',
            'ukk.edit',
            'ukk.delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Role Admin - Full access
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo([]);

        // Role Vice Principal (Wakasek) - Full student management
        $roleVicePrincipal = Role::create(['name' => 'vice-principal']);
        $roleVicePrincipal->givePermissionTo([
            'room.*',
            'period.*',
            'class.*',
            'major.*',
            'teacher.*',
            'student.*',
            'curriculum.*',
            'subject.*',
            'schedule.*',
            'attendance.view',
            'attendance.edit',
            'teaching_journal.view',
            'meeting.view',
            'meeting.edit',
        ]);

        $roleOperator = Role::create(['name' => 'operator']);

        // Role Teacher - Limited student access (only homeroom students)
        $roleTeacher = Role::create(['name' => 'teacher']);
        $roleTeacher->givePermissionTo([
            'attendance.edit',
            'attendance.view',
            'schedule.view',
            'meeting.view',
            'meeting.edit',
            'teaching_journal.view',
            'teaching_journal.create',
            'teaching_journal.edit',
            'material.*',
            'task.*',
            'material.view',
            'task.view',
            'meeting_text.*',
            'task_submission.view',
            'task_submission.edit',
            'exam.view',
            'exam.edit',
            'question.view',
        ]);

        // Role Parent - No student management access
        $roleParent = Role::create(['name' => 'parent']);
        $roleParent->givePermissionTo([
            'attendance.view',
            'schedule.view',
            'meeting.view',
            'material.view',
            'task.view',
            'meeting_text.view',
            'task_submission.view',
            'exam.view',
            'question.view',
            'ukk.view',
        ]);

        // Role Student - No student management access
        $roleStudent = Role::create(['name' => 'student']);
        $roleStudent->givePermissionTo([
            'attendance.view',
            'schedule.view',
            'meeting.view',
            'material.view',
            'meeting_text.view',
            'task.view',
            'task_submission.view',
            'task_submission.edit',
            'exam.view',
            'exam.create',
            'question.view',
            'ukk.view',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password')
        ]);
        $admin->assignRole('admin');

        $wakasek = User::create([
            'name' => 'Wakil Kepala Sekolah',
            'username' => 'wakasek',
            'password' => bcrypt('password')
        ]);
        $wakasek->assignRole('vice-principal');

        $operator = User::create([
            'name' => 'Operator Ujian',
            'username' => 'operator',
            'password' => bcrypt('password')
        ]);
        $operator->assignRole('operator');
        $operator->givePermissionTo([
            'exam.view',
            'exam.edit',
            'exam.create',
            'exam.delete',
            'question.view',
            'question.create',
            'question.delete',
            'question.edit',
            'ukk.view',
            'ukk.create',
            'ukk.edit',
            'ukk.delete'
        ]);
    }
}
