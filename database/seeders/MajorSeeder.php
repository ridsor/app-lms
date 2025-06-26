<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'IPA'],
            ['name' => 'IPS'],
            ['name' => 'Bahasa'],
        ];

        foreach ($data as $item) {
            Major::create($item);
        }
    }
}
