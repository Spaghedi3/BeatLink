<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beat extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'file_path',
        'picture',
        'tags',
        'category',
        'type_beat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
