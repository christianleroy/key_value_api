<?php

namespace Database\Factories;

use App\Models\Key;
use App\Models\KeyValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyValueFactory extends Factory
{
    protected $model = KeyValue::class;

    public function definition(): array
    {
        return [
            'key_id' => Key::factory(),
            'value' => [$this->faker->word() => $this->faker->word()],
            'recorded_at' => now(),
        ];
    }
}
