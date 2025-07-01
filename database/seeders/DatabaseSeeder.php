<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


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
            MaterialSeeder::class,
        ]);
    }
}
