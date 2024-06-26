<?php

namespace App\Infrastructure\Clients;

use App\Models\Token;
use App\Models\Streamers;
use App\Models\TwitchUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class DBClient
{
    public function getTokenDB()
    {
        return Token::latest('created_at')->first();
    }

    public function setTokenDB($newToken): void
    {
        Token::create(['token' => $newToken]);
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

    public function getAllTwitchUsers(): array
    {
        return TwitchUser::with('streamers')->get()->map(function ($user) {
            return [
                'username'          => $user->username,
                'followedStreamers' => '['.implode(', ', $user->streamer_ids).']'
            ];
        })->toArray();
    }

    public function doesTwitchUserIdExist(int $userId): bool
    {
        return TwitchUser::find($userId) !== null;
    }

    public function doesUserFollowStreamer(int $userId, int $streamerId): bool
    {
        return DB::table('twitch_user_streamers')
            ->where('user_id', $userId)
            ->where('streamer_id', $streamerId)
            ->exists();
    }

    public function followStreamer(int $userId, int $streamerId): void
    {
        $user = TwitchUser::findOrFail($userId);
        if (!$this->doesUserFollowStreamer($userId, $streamerId)) {
            $user->streamers()->attach($streamerId, ['followed_at' => now()]);
        }
    }

    public function unfollowStreamer(int $userId, int $streamerId): void
    {
        $user = TwitchUser::findOrFail($userId);
        if ($this->doesUserFollowStreamer($userId, $streamerId)) {
            $user->streamers()->detach($streamerId);
        }
    }

    public function getFollowedStreamerIds($userId): array
    {
        $user = TwitchUser::findOrFail($userId);
        return $user->streamer_ids;
    }
}
