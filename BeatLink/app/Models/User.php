<?php

namespace App\Models;

use App\Models\ChMessage as ModelsChMessage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'is_admin',
        'is_artist',
        'phone',
        'profile_picture',
        'social_links',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'         => 'boolean',
        'social_links'      => 'array',
    ];

    public function likedReactions()
    {
        return $this->hasMany(\App\Models\Reaction::class, 'user_id')
            ->where('reaction', 'love');
    }

    public function favoriteTracks(): BelongsToMany
    {
        return $this->belongsToMany(
            Track::class,
            'reactions',
            'user_id',
            'track_id'
        )
            ->withPivot('reaction')
            ->wherePivot('reaction', 'love');
    }

    public function privateTracks()
    {
        return $this->hasMany(Track::class)->where('is_private', 1);
    }


    public function updateFromProfile(array $data, ?UploadedFile $picture = null): self
    {
        if ($picture) {
            $this->profile_picture = $picture->store('profile_pictures', 'public');
        }
        $this->social_links = [
            'beatstars' => $data['beatstars'] ?? null,
            'facebook'  => $data['facebook']  ?? null,
            'twitter'   => $data['twitter']   ?? null,
            'instagram' => $data['instagram'] ?? null,
            'tiktok'    => $data['tiktok']    ?? null,
        ];
        $this->fill([
            'username' => $data['username'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
        ]);
        if ($this->isDirty('email')) {
            $this->email_verified_at = null;
        }
        $this->save();
        return $this;
    }

    public function deleteAccount(): void
    {
        if ($this->profile_picture) {
            Storage::disk('public')->delete($this->profile_picture);
        }
        $this->delete();
    }


    /** Use username for route binding */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function getUnreadMessageCount()
    {
        $userId = Auth::user()->id;

        // Chatify’s default “Message” model has a `to_id` column and a `seen` column.
        $unreadCount = ModelsChMessage::where('to_id', $userId)
            ->where('seen', 0)
            ->count();

        return $unreadCount;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
