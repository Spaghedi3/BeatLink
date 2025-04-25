<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reaction;

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
        return $this->belongsToMany(Tag::class, 'track_tag');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'track_type');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function getLovedCountAttribute()
    {
        return $this->reactions()->where('reaction', 'love')->count();
    }

    public function getHatedCountAttribute()
    {
        return $this->reactions()->where('reaction', 'hate')->count();
    }
}
