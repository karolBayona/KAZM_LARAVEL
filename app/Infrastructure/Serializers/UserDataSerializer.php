<?php

namespace App\Infrastructure\Serializers;

use Carbon\Carbon;

class UserDataSerializer
{
    public static function serialize(array $userData): array
    {
        return [
            'id'                => $userData['id']                ?? '',
            'username'          => $userData['login']             ?? '',
            'display_name'      => $userData['display_name']      ?? '',
            'type'              => $userData['type']              ?? '',
            'broadcaster_type'  => $userData['broadcaster_type']  ?? '',
            'description'       => $userData['description']       ?? '',
            'profile_image_url' => $userData['profile_image_url'] ?? '',
            'offline_image_url' => $userData['offline_image_url'] ?? '',
            'view_count'        => $userData['view_count']        ?? 0,
            'created_at'        => isset($userData['created_at']) ? Carbon::parse($userData['created_at'])->toIso8601String() : null,
        ];
    }
}
