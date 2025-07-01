<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurriculumFactory extends Factory
{
  protected $model = \App\Models\Curriculum::class;

  public function definition(): array
  {
    return [
      'name' => $this->faker->unique()->words(2, true),
      'description' => $this->faker->sentence(),
      'status' => false,
    ];
  }
}
