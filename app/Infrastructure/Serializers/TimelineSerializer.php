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
                'streamerId' => $video['user_id'],
                'streamerName' => $video['user_name'],
                'title'        => $video['title'],
                'viewerCount'  => $video['view_count'],
                'created_at'   => $video['created_at'],
            ];
        }
        return $serializedVideos;
    }
}
