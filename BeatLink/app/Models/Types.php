<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name'];

    public function beats()
    {
        return $this->belongsToMany(Beat::class, 'beat_type');
    }
}
