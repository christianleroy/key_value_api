<?php

use App\Models\Key;
use App\Models\KeyValue;
use Illuminate\Support\Carbon;

it('casts value as array', function () {
    $key = Key::factory()->create();
    $keyValue = KeyValue::factory()->create([
        'key_id' => $key->id,
        'value' => ['name' => 'test', 'count' => 42],
    ]);

    $fresh = $keyValue->fresh();
    expect($fresh->value)->toBeArray();
    expect($fresh->value['name'])->toBe('test');
    expect($fresh->value['count'])->toBe(42);
});

it('casts recorded_at as Carbon datetime', function () {
    $key = Key::factory()->create();
    $keyValue = KeyValue::factory()->create(['key_id' => $key->id]);

    expect($keyValue->recorded_at)->toBeInstanceOf(Carbon::class);
});

it('belongs to a key', function () {
    $key = Key::factory()->create();
    $keyValue = KeyValue::factory()->create(['key_id' => $key->id]);

    expect($keyValue->key->id)->toBe($key->id);
    expect($keyValue->key->key)->toBe($key->key);
});

it('enforces unique constraint on key_id and recorded_at', function () {
    $key = Key::factory()->create();
    $time = now();

    KeyValue::factory()->create(['key_id' => $key->id, 'recorded_at' => $time]);

    expect(fn () => KeyValue::factory()->create(['key_id' => $key->id, 'recorded_at' => $time]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
