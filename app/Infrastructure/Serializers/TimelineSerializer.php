<?php

namespace App\Infrastructure\Serializers;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TimelineSerializer
{
    public static function serialize($videos): array
    {
        $serializedVideos = [];
        foreach ($videos as $video) {
            $serializedVideos[] = [
                'streamerId'   => $video['user_id'],
                'streamerName' => $video['user_name'],
                'title'        => $video['title'],
                'viewerCount'  => $video['view_count'],
                'startedAt'    => $video['created_at'],
            ];
        }
        return $serializedVideos;
    }
}
