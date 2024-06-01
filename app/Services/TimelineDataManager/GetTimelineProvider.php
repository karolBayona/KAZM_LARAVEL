<?php

namespace App\Services\TimelineDataManager;

use App\Models\TwitchStreamer;
use App\Models\TwitchUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class GetTimelineProvider
{
    private TwitchUser $twitchUser;

    public function __construct(TwitchUser $twitchUser)
    {
        $this->twitchUser = $twitchUser;
    }

    public function execute($userId): JsonResponse
    {
        try {
            $user = $this->twitchUser->findOrFail($userId);

            $followedStreamers = $user->streamers()->pluck('twitch_streamers.id');

            $streams = TwitchStreamer::whereIn('id', $followedStreamers)
                ->with(['streams' => function ($query) {
                    $query->orderByDesc('started_at')->take(5);
                }])
                ->get();

            $timeline = [];

            foreach ($streams as $streamer) {
                foreach ($streamer->streams as $stream) {
                    $timeline[] = [
                        'streamerId'   => $streamer->id,
                        'streamerName' => $streamer->display_name,
                        'title'        => $stream->title,
                        'game'         => $stream->game,
                        'viewerCount'  => $stream->viewer_count,
                        'startedAt'    => $stream->started_at,
                    ];
                }
            }

            return response()->json($timeline);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'El usuario especificado no existe.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el timeline'], 500);
        }
    }
}
