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

        // Buat permissions
        $permissions = [
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat roles dan assign permissions

        // $admin = Role::create(['name' => 'admin']);
        // $admin->givePermissionTo(Permission::all());    
        // $admin->givePermissionTo([
        //     'user_management',
        //     'content_management'
        // ]);

        $roleAdmin = Role::create(['name' => 'admin']);

        $roleVicePrincipal = Role::create(['name' => 'vice-principal']);
        // $roleVicePrincipal->givePermissionTo([
        //     'room-management',
        // ]);

        $roleTeacher = Role::create(['name' => 'teacher']);
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
    }
}
