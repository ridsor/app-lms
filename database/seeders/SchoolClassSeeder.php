<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Major;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $majors = Major::all();
        $names = ['A', 'B', 'C'];
        $levels = ['10', '11', '12'];

        foreach ($majors as $major) {
            foreach ($levels as $level) {
                foreach ($names as $name) {
                    SchoolClass::firstOrCreate([
                        'name' => $name,
                        'level' => $level,
                        'major_id' => $major->id,
                    ], [
                        'capacity' => rand(20, 40)
                    ]);
                }
            }
        }
    }
}
