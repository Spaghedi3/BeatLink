<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = [
        'owner_id',
        'user_id',
        'track_id',
        'reaction',
    ];
}
