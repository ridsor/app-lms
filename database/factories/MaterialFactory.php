<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition()
    {
        return [
            'meeting_id' => Meeting::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'file_path' => $this->faker->word() . '.pdf',
            'file_type' => $this->faker->randomElement(['eBook', 'Archive', 'Link']),
        ];
    }
}
