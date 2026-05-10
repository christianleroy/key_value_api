<?php

namespace Database\Factories;

use App\Models\Key;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyFactory extends Factory
{
    protected $model = Key::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug(2),
        ];
    }
}
