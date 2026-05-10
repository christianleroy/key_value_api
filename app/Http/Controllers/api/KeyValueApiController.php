<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyValueRequest;
use App\Http\Resources\KeyValueResource;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KeyValueApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//         $keys = Key::with('latestValue')->paginate(15);
        $keys = Key::with('latestValue')->get();
        return KeyValueResource::collection($keys);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KeyValueRequest $request)
    {
        $key = Key::firstOrCreate(
            ['key' => $request->key]
        );

        $keyValue = $key->values()->create([
           ...$request->only(['value']),
            'recorded_at' => now()
        ]);

        $response = [
            $key->key => $keyValue->value,
        ];

        return response()->json([$key->key => $keyValue->value], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $key, Request $request)
    {
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

        $keyValue = Key::where('key', $key)
                    ->whereHas('values', $keyValueConstraint)
                    ->with(['values' => $keyValueConstraint])
                    ->first();

        if(!$keyValue) {
            if($request->query('timestamp')) {
                return response()->json(['message' => "Key $key on timestamp {$request->query('timestamp')} not found."], 404);
            }
            return response()->json(['message' => "Key $key not found."], 404);
        }

        return response()->json($keyValue->values->first()->value, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
//    public function edit(string $id)
//    {
//        //
//    }

    /**
     * Show the form for creating a new resource.
     */
//    public function create()
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(Request $request, Key $key)
//    {
//        $key->update($request->only(['name']));
//        return response()->json($key);
//    }

    /**
     * Remove the specified resource from storage.
     */
//    public function destroy(Key $key)
//    {
//        $key->delete();
//        return response()->json(null, 204);
//    }
}
