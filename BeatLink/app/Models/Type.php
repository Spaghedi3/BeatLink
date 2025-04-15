<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name'];

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_type');
    }
}
