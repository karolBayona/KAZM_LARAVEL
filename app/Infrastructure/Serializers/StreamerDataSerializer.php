<?php

namespace App\Infrastructure\Serializers;

use Carbon\Carbon;

class StreamerDataSerializer
{
    public static function serialize(array $streamerData): array
    {
        return [
            'id'                => (string) ($streamerData['id'] ?? ''),  // Casting to string to ensure type consistency
            'username'          => $streamerData['login']             ?? '',
            'display_name'      => $streamerData['display_name']      ?? '',
            'type'              => $streamerData['type']              ?? '',
            'broadcaster_type'  => $streamerData['broadcaster_type']  ?? '',
            'description'       => $streamerData['description']       ?? '',
            'profile_image_url' => $streamerData['profile_image_url'] ?? '',
            'offline_image_url' => $streamerData['offline_image_url'] ?? '',
            'view_count'        => $streamerData['view_count']        ?? 0,
            'created_at'        => isset($streamerData['created_at']) ? Carbon::parse($streamerData['created_at'])->toIso8601String() : '',
        ];
    }
}
