<?php

namespace App\Infrastructure\Clients;

use App\Models\Token;
use App\Models\UsersTwitch;
use Illuminate\Support\Carbon;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class DBClient
{
    public function __construct()
    {
    }

    public function getTokenDB()
    {
        return Token::latest('created_at')->first();
    }

    public function setTokenDB($newToken): void
    {
        Token::create(['token' => $newToken,]);
    }

    public function updateOrCreateUserInDB(array $userData): UsersTwitch
    {
        return UsersTwitch::updateOrCreate(
            ['id' => $userData['id']],
            [
                'login'             => $userData['login'],
                'display_name'      => $userData['display_name'],
                'type'              => $userData['type'],
                'broadcaster_type'  => $userData['broadcaster_type'],
                'description'       => $userData['description'],
                'profile_image_url' => $userData['profile_image_url'],
                'offline_image_url' => $userData['offline_image_url'],
                'view_count'        => $userData['view_count'],
                'created_at'        => Carbon::parse($userData['created_at'])->toDateTimeString(),
            ]
        );
    }

    public function getUserFromDB(int $userId): ?UsersTwitch
    {
        return UsersTwitch::find($userId);
    }
}
