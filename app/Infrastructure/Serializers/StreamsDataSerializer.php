<?php

namespace App\Infrastructure\Serializers;

class StreamsDataSerializer
{
    public static function serialize(array $streamData): array
    {
        return array_map(function ($stream) {
            return [
                'title'     => $stream['title']     ?? null,
                'user_name' => $stream['user_name'] ?? null,
            ];
        }, $streamData);
    }

}
