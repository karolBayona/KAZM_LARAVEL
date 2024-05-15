<?php

namespace App\Infrastructure\Clients;

use App\Models\Token;
use App\Models\StreamersTwitch;
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

    public function updateOrCreateStreamerInDB(array $streamerData): StreamersTwitch
    {
        return StreamersTwitch::updateOrCreate(
            ['id' => $streamerData['id']],
            [
                'login'             => $streamerData['login'],
                'display_name'      => $streamerData['display_name'],
                'type'              => $streamerData['type'],
                'broadcaster_type'  => $streamerData['broadcaster_type'],
                'description'       => $streamerData['description'],
                'profile_image_url' => $streamerData['profile_image_url'],
                'offline_image_url' => $streamerData['offline_image_url'],
                'view_count'        => $streamerData['view_count'],
                'created_at'        => Carbon::parse($streamerData['created_at'])->toDateTimeString(),
            ]
        );
    }

    public function getStreamerFromDB(int $streamerID): ?StreamersTwitch
    {
        return StreamersTwitch::find($streamerID);
    }
}
