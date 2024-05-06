<?php

namespace App\Infrastructure\Controllers;

use App\Services\GetStreamsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamsController
{
    private GetStreamsService $getStreamsService;
    public function __construct(GetStreamsService  $getStreamsService)
    {
        $this->getStreamsService = $getStreamsService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $streams = $this->getStreamsService->executeGetStreams();

        return response()->json($streams, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
