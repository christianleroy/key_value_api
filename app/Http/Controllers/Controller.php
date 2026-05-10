<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Secret API', version: '1.0.0', description: 'Secret API Documentation')]
#[OA\Server(url: '/', description: 'API Server')]
abstract class Controller
{
    //
}
