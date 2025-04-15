<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'file_path',
        'picture',
        'category',
        'is_private',
        'folder_files',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'beat_tag');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'beat_type');
    }
}
