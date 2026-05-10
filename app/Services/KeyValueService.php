<?php

namespace App\Services;

use App\Http\Requests\KeyValueRequest;
use App\Http\Resources\KeyValueResource;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KeyValueService
{

    public function getKeysWithLatestValue() {
        $keys = Key::with('latestValue')->get();
        return KeyValueResource::collection($keys);
    }

    public function createOrUpdateKey(KeyValueRequest $request) {

        $key = Key::firstOrCreate(
            ['key' => $request->key]
        );

        $keyValue = $key->values()->create([
            ...$request->only(['value']),
            'recorded_at' => now()
        ]);

        return [
            $key->key => $keyValue->value,
        ];
    }

    public function getKeyValue(string $key, Request $request) {
        $keyValueConstraint = function ($query) use ($request) {
            $query->when($request->query('timestamp'),
                fn($q, $recordedAt) =>
                $q
                    ->where('recorded_at', '<=', Carbon::createFromTimestamp($recordedAt))
                    ->latest('recorded_at')
                    ->limit(1),
                fn($q) =>
                $q->latest('recorded_at')->limit(1)
            );
        };

        return Key::where('key', $key)
            ->whereHas('values', $keyValueConstraint)
            ->with(['values' => $keyValueConstraint])
            ->first();
    }
}