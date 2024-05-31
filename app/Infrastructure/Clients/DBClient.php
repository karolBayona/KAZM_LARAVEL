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

    public function getAllTwitchUsers(): array
    {
        return TwitchUser::with('streamers')->get()->map(function ($user) {
            return [
                'username'          => $user->username,
                'followedStreamers' => $user->streamers->pluck('name')->all()
            ];
        })->toArray();
    }

    public function doesUserFollowStreamer(int $userId, int $streamerId): bool
    {
        return TwitchUser::where('user_id', $userId)
            ->whereHas('streamers', function ($query) use ($streamerId) {
                $query->where('streamer_id', $streamerId);
            })
            ->exists();
    }

    public function followStreamer(int $userId, int $streamerId): void
    {
        $user = TwitchUser::findOrFail($userId);
        $user->streamers()->attach($streamerId);
    }

    public function updateTopGamesData(array $gamesData): void
    {
        DB::table('top_games')->truncate();

        foreach ($gamesData as $game) {
            DB::table('top_games')->updateOrInsert(
                ['game_id' => $game['id']],
                ['game_name' => $game['name']]
            );
        }
    }

    public function updateOrInsertTopVideosData(array $videosData, string $gameId): void
    {
        DB::table('top_videos')->truncate();

        foreach ($videosData as $video) {
            DB::table('top_videos')->updateOrInsert(
                ['game_id' => $gameId, 'video_id' => $video['id']],
                [
                    'video_title' => $video['title'],
                    'video_views' => $video['view_count'],
                    'user_name'   => $video['user_name'],
                    'duration'    => $video['duration'],
                    'created_at'  => $video['created_at'],
                ]
            );
        }
    }

    public function getTopGameData($gameId)
    {
        return DB::table('top_games')->where('game_id', $gameId)->value('game_name');
    }

    public function getTopDataForGame($gameId): object|null
    {
        return DB::table('top_videos')
            ->select('user_name', DB::raw('COUNT(*) AS total_videos'), DB::raw('SUM(video_views) AS total_views'), DB::raw('MAX(video_views) AS most_viewed_views'))
            ->where('game_id', $gameId)
            ->groupBy('user_name')
            ->orderByDesc('most_viewed_views')
            ->limit(1)
            ->first();
    }

    public function getVideoDetailsForTopGame($userName, $gameId, $mostViewedViews): object|null
    {
        return DB::table('top_videos')
            ->select('video_title', 'duration', 'created_at')
            ->where('user_name', $userName)
            ->where('game_id', $gameId)
            ->where('video_views', $mostViewedViews)
            ->first();
    }

    public function updateTopOfTheTopsTable($gameId, $fields): void
    {
        DB::table('topofthetops')->updateOrInsert(
            ['game_id' => $gameId],
            $fields
        );
    }
}
