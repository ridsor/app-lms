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
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Role Admin - Full access
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());

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
        ]);

        // Role Teacher - Limited student access (only homeroom students)
        $roleTeacher = Role::create(['name' => 'teacher']);

        // Role Parent - No student management access
        $roleParent = Role::create(['name' => 'parent']);

        // Role Student - No student management access
        $roleStudent = Role::create(['name' => 'student']);

        $role = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password')
        ]);
        $role->assignRole('admin');

        $role = User::create([
            'name' => 'Wakil Kepala Sekolah',
            'username' => 'wakasek',
            'email' => 'wakasek@gmail.com',
            'password' => bcrypt('password')
        ]);
        $role->assignRole('vice-principal');

        $student = User::create([
            'name' => 'Student',
            'username' => 'student',
            'email' => 'student@gmail.com',
            'password' => bcrypt('password')
        ]);
        $student->assignRole('student');

        $teacher = User::create([
            'name' => 'Teacher',
            'username' => 'teacher',
            'email' => 'teacher@gmail.com',
            'password' => bcrypt('password')
        ]);
        $teacher->assignRole('teacher');

        $parent = User::create([
            'name' => 'Parent',
            'username' => 'parent',
            'email' => 'parent@gmail.com',
            'password' => bcrypt('password')
        ]);
        $parent->assignRole('parent');
    }
}
