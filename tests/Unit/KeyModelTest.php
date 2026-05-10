<?php

use App\Models\Key;
use App\Models\KeyValue;

it('has many values', function () {
    $key = Key::factory()->create();

    KeyValue::factory()->count(3)->sequence(
        ['recorded_at' => now()->subHours(3)],
        ['recorded_at' => now()->subHours(2)],
        ['recorded_at' => now()->subHour()],
    )->create(['key_id' => $key->id]);

    expect($key->values)->toHaveCount(3);
});

it('latestValue returns the most recently recorded value', function () {
    $key = Key::factory()->create();

    $old = KeyValue::factory()->create([
        'key_id' => $key->id,
        'value' => ['temp' => 10],
        'recorded_at' => now()->subHour(),
    ]);
    $latest = KeyValue::factory()->create([
        'key_id' => $key->id,
        'value' => ['temp' => 99],
        'recorded_at' => now(),
    ]);

    expect($key->latestValue->id)->toBe($latest->id);
});

it('firstOrCreate does not duplicate keys', function () {
    Key::firstOrCreate(['key' => 'duplicate-key']);
    Key::firstOrCreate(['key' => 'duplicate-key']);

    expect(Key::count())->toBe(1);
});

it('key field is unique', function () {
    Key::factory()->create(['key' => 'unique-key']);

    expect(fn () => Key::factory()->create(['key' => 'unique-key']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
