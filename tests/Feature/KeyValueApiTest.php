<?php

use App\Models\Key;
use App\Models\KeyValue;

// GET /api/key_values
describe('GET /api/key_values', function () {
    it('returns an empty collection when no keys exist', function () {
        $this->getJson('/api/key_values')
            ->assertStatus(200)
            ->assertJson(['data' => []]);
    });

    it('returns all keys with their latest values', function () {
        $key = Key::factory()->create(['key' => 'temperature']);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['celsius' => 20],
            'recorded_at' => now()->subHour(),
        ]);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['celsius' => 25],
            'recorded_at' => now(),
        ]);

        $this->getJson('/api/key_values')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', 'temperature')
            ->assertJsonPath('data.0.value.celsius', 25);
    });

    it('returns key, value, and timestamp fields in each item', function () {
        $key = Key::factory()->create();
        KeyValue::factory()->create(['key_id' => $key->id]);

        $this->getJson('/api/key_values')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'value', 'timestamp'],
                ],
            ]);
    });

    it('returns latest value across multiple keys', function () {
        $key1 = Key::factory()->create(['key' => 'alpha']);
        $key2 = Key::factory()->create(['key' => 'beta']);
        KeyValue::factory()->create(['key_id' => $key1->id, 'recorded_at' => now()->subHour()]);
        KeyValue::factory()->create(['key_id' => $key2->id, 'recorded_at' => now()]);

        $this->getJson('/api/key_values')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });
});

// POST /api/key_values
describe('POST /api/key_values', function () {
    it('creates a new key-value pair and returns 201', function () {
        $this->postJson('/api/key_values', [
            'key' => 'my-key',
            'value' => ['status' => 'active'],
        ])
            ->assertStatus(201)
            ->assertJson(['my-key' => ['status' => 'active']]);

        $this->assertDatabaseHas('keys', ['key' => 'my-key']);
        $this->assertDatabaseCount('key_values', 1);
    });

    it('returns 422 when key field is missing', function () {
        $this->postJson('/api/key_values', ['value' => ['foo' => 'bar']])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    });

    it('returns 422 when key is not a string', function () {
        $this->postJson('/api/key_values', ['key' => ['not-a-string']])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    });

    it('reuses an existing key instead of creating a duplicate', function () {
        Key::factory()->create(['key' => 'existing-key']);

        $this->postJson('/api/key_values', [
            'key' => 'existing-key',
            'value' => ['data' => 'new'],
        ])->assertStatus(201);

        $this->assertDatabaseCount('keys', 1);
    });

    it('appends a new value record when key already has values', function () {
        $key = Key::factory()->create(['key' => 'counter']);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['count' => 1],
            'recorded_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/key_values', [
            'key' => 'counter',
            'value' => ['count' => 2],
        ])->assertStatus(201);

        $this->assertDatabaseCount('key_values', 2);
    });

    it('returns 422 when value field is omitted', function () {
        $this->postJson('/api/key_values', ['key' => 'no-value-key'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['value']);
    });

    it('stores nested object values', function () {
        $this->postJson('/api/key_values', [
            'key' => 'config',
            'value' => ['nested' => ['deep' => true]],
        ])
            ->assertStatus(201)
            ->assertJson(['config' => ['nested' => ['deep' => true]]]);
    });
});

// GET /api/key_values/{key}
describe('GET /api/key_values/{key}', function () {
    it('returns the latest value for an existing key', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['reading' => 10],
            'recorded_at' => now()->subHour(),
        ]);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['reading' => 50],
            'recorded_at' => now(),
        ]);

        $this->getJson('/api/key_values/sensor')
            ->assertStatus(200)
            ->assertJson(['reading' => 50]);
    });

    it('returns 404 when key does not exist', function () {
        $this->getJson('/api/key_values/nonexistent')
            ->assertStatus(404)
            ->assertJsonPath('message', 'Key nonexistent not found.');
    });

    it('returns the value at or before a given timestamp', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        $earlyTime = now()->subHours(2);

        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['reading' => 10],
            'recorded_at' => $earlyTime,
        ]);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['reading' => 50],
            'recorded_at' => now(),
        ]);

        $this->getJson('/api/key_values/sensor?timestamp=' . $earlyTime->timestamp)
            ->assertStatus(200)
            ->assertJson(['reading' => 10]);
    });

    it('returns 404 with timestamp context when no value exists before the given timestamp', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        KeyValue::factory()->create([
            'key_id' => $key->id,
            'value' => ['reading' => 10],
            'recorded_at' => now(),
        ]);

        $pastTimestamp = now()->subDay()->timestamp;

        $this->getJson("/api/key_values/sensor?timestamp={$pastTimestamp}")
            ->assertStatus(404)
            ->assertJsonPath('message', "Key sensor on timestamp {$pastTimestamp} not found.");
    });

    it('returns the most recent value when multiple exist and no timestamp given', function () {
        $key = Key::factory()->create(['key' => 'gauge']);
        KeyValue::factory()->count(3)->sequence(
            ['value' => ['v' => 1], 'recorded_at' => now()->subHours(3)],
            ['value' => ['v' => 2], 'recorded_at' => now()->subHours(2)],
            ['value' => ['v' => 3], 'recorded_at' => now()->subHour()],
        )->create(['key_id' => $key->id]);

        $this->getJson('/api/key_values/gauge')
            ->assertStatus(200)
            ->assertJson(['v' => 3]);
    });
});
