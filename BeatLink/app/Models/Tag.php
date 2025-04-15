<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name']; // optional: allows mass assignment

    public function tracks()
    {
        return $this->belongsToMany(track::class, 'track_tag');
    }
}
