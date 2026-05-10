<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyValueRequest;
use App\Http\Resources\KeyValueResource;
use App\Models\Key;
use App\Services\KeyValueService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class KeyValueApiController extends Controller
{

    public function __construct(protected KeyValueService $keyValueService) {}

    #[OA\Get(
        path: '/api/key_values',
        summary: 'List all keys with their latest value',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of keys with their latest value',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'key', type: 'string', example: 'temperature'),
                                    new OA\Property(property: 'value', description: 'The latest recorded value (any JSON type)', example: ['celsius' => 36.6]),
                                    new OA\Property(property: 'timestamp', description: 'Unix timestamp of when the value was recorded', type: 'integer', example: 1715000000),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        return $this->keyValueService->getKeysWithLatestValue();
    }

    #[OA\Post(
        path: '/api/key_values',
        description: 'Creates the key if it does not exist, then records the value with the current timestamp.',
        summary: 'Store a new value for a key',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['key', 'value'],
                properties: [
                    new OA\Property(property: 'key', type: 'string', example: 'temperature', maxLength: 255),
                    new OA\Property(property: 'value', description: 'Any JSON-serializable value', example: ['celsius' => 36.6]),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Value recorded successfully',
                content: new OA\JsonContent(
                    description: 'Object with the key name as the property and the stored value as its value',
                    type: 'object',
                    example: ['temperature' => ['celsius' => 36.6]],
                    additionalProperties: new OA\AdditionalProperties(description: 'The stored value')
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The key field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(KeyValueRequest $request)
    {
        $response = $this->keyValueService->createOrUpdateKey($request);
        return response()->json($response, 201);
    }

    #[OA\Get(
        path: '/api/key_values/{key}',
        description: 'Returns the latest value for a key. If a timestamp is provided, returns the most recent value recorded at or before that point in time.',
        summary: 'Get the value of a key',
        parameters: [
            new OA\Parameter(
                name: 'key',
                description: 'The key name',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'temperature'
            ),
            new OA\Parameter(
                name: 'timestamp',
                description: 'Unix timestamp. If provided, returns the most recent value recorded at or before this time.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1715000000
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The stored value (any JSON type)',
                content: new OA\JsonContent(example: ['celsius' => 36.6])
            ),
            new OA\Response(
                response: 404,
                description: 'Key not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Key temperature not found.'),
                    ]
                )
            ),
        ]
    )]
    public function show(string $key, Request $request)
    {
        $keyValue = $this->keyValueService->getKeyValue($key, $request);

        if(!$keyValue) {
            if($request->query('timestamp')) {
                return response()->json(['message' => "Key $key on timestamp {$request->query('timestamp')} not found."], 404);
            }
            return response()->json(['message' => "Key $key not found."], 404);
        }

        return response()->json($keyValue->values->first()->value, 200);
    }

}
