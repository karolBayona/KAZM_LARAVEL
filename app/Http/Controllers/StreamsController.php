<?php

namespace App\Http\Controllers;

use App\Services\GetStreamsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamsController extends Controller
{
    private GetStreamsService $getStreamsService;
    public function __construct(GetStreamsService  $getStreamsService)
    {
        $this->getStreamsService = $getStreamsService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $streams = $this->getStreamsService->executeGetStreams();

        return response()->json($streams);
    }
}
