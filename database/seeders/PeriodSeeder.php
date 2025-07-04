<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        Period::create([
            'semester' => 'odd',
            'academic_year' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'status' => true
        ]);
    }
}
