<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitchStream extends Model
{
    use HasFactory;

    protected $table      = 'twitch_streams';
    protected $primaryKey = 'stream_id';

    protected $fillable = ['streamer_id', 'title', 'game', 'viewer_count', 'started_at'];

    public function streamer()
    {
        return $this->belongsTo(TwitchStreamer::class, 'streamer_id');
    }
}
