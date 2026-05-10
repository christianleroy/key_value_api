<?php

namespace Database\Seeders;

use App\Models\Key;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KeyValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keyValues = [
            [
                'key' => 'kpop',
                'values' => [
                    ['recorded_at' => 1700000000, 'value' => ['song' => 'how_sweet', 'artist' => 'NewJeans', 'year' => 2024]],
                    ['recorded_at' => 1700000100, 'value' => ['song' => 'supernova', 'artist' => 'aespa', 'year' => 2024]],
                    ['recorded_at' => 1700000200, 'value' => ['song' => 'magnetic', 'artist' => 'ILLIT', 'year' => 2024]],
                ],
            ],
            [
                'key' => 'playlist',
                'values' => [
                    ['recorded_at' => 1700001000, 'value' => ['song' => 'gods', 'artist' => 'NewJeans', 'year' => 2023]],
                    ['recorded_at' => 1700001100, 'value' => ['song' => 'hype boy', 'artist' => 'NewJeans', 'year' => 2022]],
                    ['recorded_at' => 1700001200, 'value' => ['song' => 'attention', 'artist' => 'NewJeans', 'year' => 2022]],
                ],
            ],
            [
                'key' => 'concert',
                'values' => [
                    ['recorded_at' => 1700022000, 'value' => ['song' => 'pink venom', 'artist' => 'BLACKPINK', 'year' => 2022]],
                    ['recorded_at' => 1700032100, 'value' => ['song' => 'shut down', 'artist' => 'BLACKPINK', 'year' => 2022]],
                    ['recorded_at' => 1700042200, 'value' => ['song' => 'crazy', 'artist' => 'LE SSERAFIM', 'year' => 2024]],
                ],
            ],
            [
                'key' => 'bands',
                'values' => [
                    ['recorded_at' => 1700002000, 'value' => ['song' => 'slts', 'artist' => 'Nirvana', 'year' => 2022]],
                    ['recorded_at' => 1700002100, 'value' => ['song' => 'famous last words', 'artist' => 'MCR', 'year' => 2022]],
                    ['recorded_at' => 1700002200, 'value' => ['song' => 'numb', 'artist' => 'Linkin Park', 'year' => 2024]],
                ],
            ],
        ];

        foreach ($keyValues as $keyValue) {
            $key = Key::create(['key' => $keyValue['key']]);

            $key->values()->createMany(
                array_map(fn($value) => [
                    'recorded_at' => Carbon::createFromTimestamp($value['recorded_at']),
                    'value'        => $value['value'],
                ], $keyValue['values'])
            );
        }

    }
}
