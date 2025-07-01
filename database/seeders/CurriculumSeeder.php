<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;

class CurriculumSeeder extends Seeder
{
  public function run(): void
  {
    \App\Models\Curriculum::factory()->count(5)->create();
  }
}
