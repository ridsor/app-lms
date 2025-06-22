<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeroomTeacher;

class HomeroomTeacherSeeder extends Seeder
{
    public function run(): void
    {
        HomeroomTeacher::factory()->count(3)->create();
    }
}
