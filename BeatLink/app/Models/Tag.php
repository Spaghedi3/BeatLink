<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name']; // optional: allows mass assignment

    public function beats()
    {
        return $this->belongsToMany(Beat::class, 'beat_tag');
    }
}
