<?php

namespace App\Infrastructure\Clients;

use App\Models\Token;
use App\Models\Streamers;
use App\Models\TwitchUser;
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

    public function updateOrCreateStreamerInDB(array $streamerData): Streamers
    {
        return Streamers::updateOrCreate(
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

    public function getStreamerFromDB(int $streamerID): ?Streamers
    {
        return Streamers::find($streamerID);
    }

    public function createTwitchUser(string $username, string $password): void
    {
        TwitchUser::create([
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function doesTwitchUserExist(string $username): bool
    {
        $twitchUser = TwitchUser::where('username', $username)->first();

        return $twitchUser !== null;
    }
}
