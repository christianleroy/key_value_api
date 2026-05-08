<?php

namespace Database\Seeders;

use App\Models\Key;
use Illuminate\Database\Seeder;

class keySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Key::create([
            'key' => 'simple_key'
        ]);
        Key::create([
            'key' => 'your_key'
        ]);
        Key::create([
            'key' => 'my_key'
        ]);
    }
}
