<?php

use App\Http\Requests\KeyValueRequest;
use App\Http\Resources\KeyValueResource;
use App\Models\Key;
use App\Models\KeyValue;
use App\Services\KeyValueService;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->service = new KeyValueService();
});

// getKeysWithLatestValue
describe('getKeysWithLatestValue', function () {
    it('returns an empty resource collection when no keys exist', function () {
        $result = $this->service->getKeysWithLatestValue();

        expect($result)->toBeInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class);
        expect($result->count())->toBe(0);
    });

    it('returns each key with its latest value', function () {
        $key = Key::factory()->create(['key' => 'temperature']);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['c' => 20], 'recorded_at' => now()->subHour()]);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['c' => 25], 'recorded_at' => now()]);

        $result = $this->service->getKeysWithLatestValue();

        expect($result->count())->toBe(1);
        expect($result->first()->key)->toBe('temperature');
        expect($result->first()->latestValue->value['c'])->toBe(25);
    });

    it('returns a KeyValueResource collection', function () {
        $key = Key::factory()->create();
        KeyValue::factory()->create(['key_id' => $key->id, 'recorded_at' => now()]);

        $result = $this->service->getKeysWithLatestValue();

        expect($result->first())->toBeInstanceOf(KeyValueResource::class);
    });
});

// createOrUpdateKey
describe('createOrUpdateKey', function () {
    it('creates a new key and records the value', function () {
        $request = new KeyValueRequest();
        $request->replace(['key' => 'my-key', 'value' => ['status' => 'ok']]);

        $result = $this->service->createOrUpdateKey($request);

        expect($result)->toBe(['my-key' => ['status' => 'ok']]);
        expect(Key::count())->toBe(1);
        expect(KeyValue::count())->toBe(1);
    });

    it('reuses an existing key without creating a duplicate', function () {
        Key::factory()->create(['key' => 'existing']);

        $request = new KeyValueRequest();
        $request->replace(['key' => 'existing', 'value' => ['n' => 1]]);

        $this->service->createOrUpdateKey($request);

        expect(Key::count())->toBe(1);
    });

    it('appends a new value record each time it is called', function () {
        $request = new KeyValueRequest();
        $request->replace(['key' => 'counter', 'value' => ['n' => 1]]);
        $this->travel(-5)->seconds();
        $this->service->createOrUpdateKey($request);

        $this->travelBack();
        $request->replace(['key' => 'counter', 'value' => ['n' => 2]]);
        $this->service->createOrUpdateKey($request);

        expect(KeyValue::count())->toBe(2);
    });

    it('returns an array keyed by the key name', function () {
        $request = new KeyValueRequest();
        $request->replace(['key' => 'sensor', 'value' => ['reading' => 42]]);

        $result = $this->service->createOrUpdateKey($request);

        expect($result)->toHaveKey('sensor');
        expect($result['sensor'])->toBe(['reading' => 42]);
    });
});

// getKeyValue
describe('getKeyValue', function () {
    it('returns null when the key does not exist', function () {
        $result = $this->service->getKeyValue('missing', new Request());

        expect($result)->toBeNull();
    });

    it('returns the key model with latest value when no timestamp given', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['v' => 1], 'recorded_at' => now()->subHour()]);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['v' => 2], 'recorded_at' => now()]);

        $result = $this->service->getKeyValue('sensor', new Request());

        expect($result)->toBeInstanceOf(Key::class);
        expect($result->values->first()->value['v'])->toBe(2);
    });

    it('returns the value recorded at or before the given timestamp', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        $earlyTime = now()->subHours(2);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['v' => 10], 'recorded_at' => $earlyTime]);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['v' => 99], 'recorded_at' => now()]);

        $request = Request::create('/api/key_values/sensor', 'GET', ['timestamp' => $earlyTime->timestamp]);
        $result = $this->service->getKeyValue('sensor', $request);

        expect($result->values->first()->value['v'])->toBe(10);
    });

    it('returns null when no value exists at or before the given timestamp', function () {
        $key = Key::factory()->create(['key' => 'sensor']);
        KeyValue::factory()->create(['key_id' => $key->id, 'value' => ['v' => 1], 'recorded_at' => now()]);

        $request = Request::create('/api/key_values/sensor', 'GET', ['timestamp' => now()->subDay()->timestamp]);
        $result = $this->service->getKeyValue('sensor', $request);

        expect($result)->toBeNull();
    });
});
