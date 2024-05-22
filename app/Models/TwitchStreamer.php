<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitchStreamer extends Model
{
    use HasFactory;

    protected $table = 'twitch_streamers';
    protected $primaryKey = 'streamer_id';

    protected $fillable = ['name', 'other_details'];

    public function users()
    {
        return $this->belongsToMany(TwitchUser::class, 'twitch_user_streamers', 'streamer_id', 'user_id')->withTimestamps();
    }

    public function streams()
    {
        return $this->hasMany(TwitchStream::class, 'streamer_id');
    }
}
