<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function tracks()
    {
        return $this->belongsToMany(track::class, 'track_tag');
    }
}
