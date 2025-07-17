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
            'meeting.view',
        ]);

        // Role Teacher - Limited student access (only homeroom students)
        $roleTeacher = Role::create(['name' => 'teacher']);
        $roleTeacher->givePermissionTo([
            'attendance.edit',
            'attendance.view',
            'schedule.view',
            'meeting.view',
            'meeting.edit',
        ]);

        // Role Parent - No student management access
        $roleParent = Role::create(['name' => 'parent']);
        $roleParent->givePermissionTo([
            'attendance.view',
            'schedule.view',
            'meeting.view',
        ]);

        // Role Student - No student management access
        $roleStudent = Role::create(['name' => 'student']);
        $roleStudent->givePermissionTo([
            'attendance.view',
            'schedule.view',
            'meeting.view',
        ]);

        $role = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password')
        ]);
        $role->assignRole('admin');

        $role = User::create([
            'name' => 'Wakil Kepala Sekolah',
            'username' => 'wakasek',
            'password' => bcrypt('password')
        ]);
        $role->assignRole('vice-principal');

        $student = User::create([
            'name' => 'Student',
            'username' => 'student',
            'password' => bcrypt('password')
        ]);
        $student->assignRole('student');

        $teacher = User::create([
            'name' => 'Teacher',
            'username' => 'teacher',
            'password' => bcrypt('password')
        ]);
        $teacher->assignRole('teacher');

        $parent = User::create([
            'name' => 'Parent',
            'username' => 'parent',
            'password' => bcrypt('password')
        ]);
        $parent->assignRole('parent');
    }
}
