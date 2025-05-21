<?php

// app/Models/TrackStat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackStat extends Model
{
    protected $primaryKey = 'track_id';
    public $incrementing = false;

    protected $fillable = [
        'track_id',
        'total_listen_seconds',
        'love_count',
        'hate_count',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
