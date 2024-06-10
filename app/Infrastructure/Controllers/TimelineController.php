<?php

namespace App\Infrastructure\Controllers;

use App\Services\TimelineDataManager\GetTimelineProvider;
use Illuminate\Http\JsonResponse;

class TimelineController
{
    private GetTimelineProvider $timelineProvider;

    public function __construct(GetTimelineProvider $timelineProvider)
    {
        $this->timelineProvider = $timelineProvider;
    }

    public function __invoke($userId): JsonResponse
    {
        return $this->timelineProvider->execute($userId);
    }
}
