<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', description: 'Secret API Documentation', title: 'Secret API')]
#[OA\Server(url: '/', description: 'API Server')]
abstract class Controller
{
    //
}
