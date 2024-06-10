<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TwitchUser extends Authenticatable
{
    use HasFactory;

    protected $table      = 'twitch_users';
    protected $primaryKey = 'user_id';

    protected $fillable = ['username', 'password'];

    public function streamers()
    {
        return $this->belongsToMany(TwitchStreamer::class, 'twitch_user_streamers', 'user_id', 'streamer_id')->withTimestamps();
    }
    public function followedStreamers()
    {
        return $this->hasMany(TwitchUserStreamers::class, 'user_id');
    }
    public function getStreamerIdsAttribute()
    {
        return $this->followedStreamers->pluck('streamer_id')->all();
    }
}
