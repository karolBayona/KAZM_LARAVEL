<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streamers extends Model
{
    protected $table      = 'streamers';
    protected $primaryKey = 'id';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id', 'login', 'display_name', 'type', 'broadcaster_type',
        'description', 'profile_image_url', 'offline_image_url', 'view_count', 'created_at'
    ];
}
