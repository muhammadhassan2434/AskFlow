<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BotResource;
use App\Models\Bot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function show(Bot $bot): JsonResponse
    {
        $bot->load([
            'workspace',
            'sources',
        ]);

        return response()->json([
            'success' => true,
            'data' => new BotResource($bot),
        ]);
    }
}
