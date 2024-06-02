<?php

namespace App\Infrastructure\Serializers;

use Carbon\Carbon;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopOfTheTopsSerializer
{
    public static function serialize($gameId, $gameName, $topData, $videoDetails): array
    {
        return [
            'game_id'                => $gameId,
            'game_name'              => $gameName,
            'user_name'              => $topData->user_name,
            'total_videos'           => $topData->total_videos,
            'total_views'            => $topData->total_views,
            'most_viewed_title'      => $videoDetails->video_title,
            'most_viewed_views'      => $topData->most_viewed_views,
            'most_viewed_duration'   => $videoDetails->duration,
            'most_viewed_created_at' => $videoDetails->created_at,
            'last_updated_at'        => Carbon::now()
        ];
    }

}
