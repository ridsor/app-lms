<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Schedule;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            PeriodSeeder::class,
            CurriculumSeeder::class,
            MajorSeeder::class,
            TeacherSeeder::class,
            SubjectSeeder::class,
            SchoolClassSeeder::class,
            StudentSeeder::class,
            ScheduleSeeder::class,
            RoomSeeder::class,
        ]);


        try {
            // Create teacher user
            $teacherUser = User::create([
                'name' => 'Teacher',
                'username' => 'teacher',
                'password' => bcrypt('password')
            ]);
            $teacherUser->assignRole('teacher');

            // Create teacher profile
            $teacher = $teacherUser->teacher()->create([
                'name' => 'Guru',
                'nip' => '19802447391',
                'specialization' => 'Matematika',
                'gender' => 'F',
                'date_of_birth' => '2022-03-11',
                'birthplace' => 'Ambon',
                'religion' => 'Islam'
            ]);

            // Assign teacher to schedule
            $schedule = Schedule::first();
            if ($schedule) {
                $schedule->update([
                    'teacher_id' => $teacher->id
                ]);
            } else {
                throw new Exception('No schedule found to assign teacher to');
            }

            // Create student user
            $studentUser = User::create([
                'name' => 'Student',
                'username' => 'student',
                'password' => bcrypt('password')
            ]);
            $studentUser->assignRole('student');

            // Create student user
            $parentUser = User::create([
                'name' => 'Parent',
                'username' => 'parent',
                'password' => bcrypt('password')
            ]);
            $parentUser->assignRole('parent');

            // Assign user to student
            $student = $schedule->class->students->first();
            if ($student) {
                $student->user->attendances()->update([
                    'user_id' => $studentUser->id,
                ]);
                $student->update([
                    'user_id' => $studentUser->id,
                    'parent_id' => $parentUser->id,
                    'status' => 'active'
                ]);
            } else {
                throw new Exception('No student found to assign user to');
            }
            $schedule->class->students->first()->user->delete();
        } catch (Exception $e) {
            // Handle any errors that occur
            Log::error('Error in user creation: ' . $e->getMessage());
            // You might want to rollback any created records here if needed
        }
    }
}
