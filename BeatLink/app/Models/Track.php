<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Reaction;
use App\Models\TrackStat;
use App\Models\UserInteraction;
use App\Models\User;
use App\Models\Tag;
use App\Models\Type;
use App\Notifications\ReactionNotification;

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
        'bpm',
        'key',
        'scale',
    ];

    // ─── Relationships ─────────────────────────────────────────

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
    public function stats()
    {
        return $this->hasOne(TrackStat::class);
    }

    // ─── Query Scopes ──────────────────────────────────────────

    public function scopeOwnedBy($q, $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeSearch($q, ?string $term)
    {
        return $term
            ? $q->where(
                fn($q2) =>
                $q2->where('name', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%")
                    ->orWhereHas('tags',   fn($t) => $t->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('types',  fn($t) => $t->where('name', 'like', "%{$term}%"))
            )
            : $q;
    }

    public function scopeFilterBpm($q, ?string $range)
    {
        if (! $range) return $q;
        $range = trim($range);
        if (str_contains($range, '-')) {
            [$min, $max] = array_map('trim', explode('-', $range, 2));
            if (is_numeric($min) && is_numeric($max) && $min <= $max) {
                return $q->whereBetween('bpm', [(int)$min, (int)$max]);
            }
        } elseif (is_numeric($range)) {
            return $q->where('bpm', (int)$range);
        }
        return $q;
    }

    public function scopeFilterKey($q, ?string $key)
    {
        return $key ? $q->where('key', $key) : $q;
    }

    public function scopeFilterScale($q, ?string $scale)
    {
        return $scale ? $q->where('scale', $scale) : $q;
    }

    public function scopeFilterCategory($q, ?array $cats = null)
    {
        if ($cats && ! Auth::user()->is_artist) {
            return $q->whereIn('category', $cats);
        }
        return $q;
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getLovedCountAttribute()
    {
        return $this->reactions()->where('reaction', 'love')->count();
    }

    public function getHatedCountAttribute()
    {
        return $this->reactions()->where('reaction', 'hate')->count();
    }

    // ─── Factories & Updaters ─────────────────────────────────

    /**
     * Create a track (uploads, Essentia, tags/types).
     */
    public static function createFromRequest(Request $r): self
    {
        $user      = $r->user();
        $username  = $user->username;

        // strip fields not applicable for artists
        if ($user->is_artist) {
            $r->request->remove('category');
            $r->request->remove('audio_folder');
        }

        // validation is still done in a FormRequest or here...

        // handle uploads
        [$audioPath, $folderJson] = self::processAudioUploads($r, $username);
        $picturePath = $r->hasFile('picture')
            ? $r->file('picture')->store('track_pictures', 'public')
            : null;

        // create DB row
        $track = self::create([
            'user_id'      => $user->id,
            'name'         => $r->name,
            'file_path'    => $audioPath,
            'picture'      => $picturePath,
            'category'     => $r->category,
            'is_private'   => $r->boolean('is_private'),
            'folder_files' => $folderJson,
            'type'         => $user->is_artist ? 'song' : 'beat',
        ]);

        // sync tags/types
        $track->syncTags($r->tags);
        $track->syncTypes($r->types);

        // run Essentia on single-file uploads
        if ($r->hasFile('audio_file')) {
            $track->analyzeWithEssentia();
        }

        return $track;
    }

    /**
     * Update an existing track (uploads, Essentia, tags/types).
     */
    public function updateFromRequest(Request $r): self
    {
        $user     = Auth::user();
        $username = $user->username;

        if ($user->is_artist) {
            $r->request->remove('category');
            $r->request->remove('audio_folder');
        }

        // handle replacements
        [$audioPath, $folderJson] = self::processAudioUploads($r, $username, $this->file_path, $this->folder_files);
        $picturePath = $r->hasFile('picture')
            ? $r->file('picture')->store('track_pictures', 'public')
            : $this->picture;

        $this->update([
            'name'         => $r->name,
            'file_path'    => $audioPath,
            'category'     => $r->category,
            'is_private'   => $r->boolean('is_private'),
            'folder_files' => $folderJson,
            'picture'      => $picturePath,
            'type'         => $user->is_artist ? 'song' : 'beat',
        ]);

        $this->syncTags($r->tags);
        $this->syncTypes($r->types);

        // re-run Essentia only if single new file
        if ($r->hasFile('audio_file')) {
            $this->analyzeWithEssentia();
        }

        return $this;
    }

    // ─── Upload Helpers ──────────────────────────────────────

    protected static function processAudioUploads(Request $r, string $username, ?string $oldPath = null, ?string $oldJson = null): array
    {
        $audioPath = $oldPath;
        $folderJson = $oldJson;

        if ($r->hasFile('audio_file')) {
            $audioPath  = $r->file('audio_file')->store("tracks/{$username}", 'public');
        } elseif ($r->hasFile('audio_folder')) {
            $sanitized = Str::slug($r->name);
            $paths     = [];
            foreach ($r->file('audio_folder') as $f) {
                $paths[] = $f->storeAs("kits/{$username}/{$sanitized}", $f->getClientOriginalName(), 'public');
            }
            $folderJson = json_encode($paths);
            $audioPath = "kits/{$username}/{$sanitized}";
        }

        return [$audioPath, $folderJson];
    }

    protected function analyzeWithEssentia(): void
    {
        $full = storage_path("app/public/{$this->file_path}");
        $bat  = base_path('run_essentia.bat');
        $output = shell_exec("\"{$bat}\" \"{$full}\"");

        if ($output) {
            $lines = array_filter(explode("\n", trim($output)));
            if (count($lines) >= 2) {
                $bpm = (int) filter_var($lines[0], FILTER_SANITIZE_NUMBER_INT);
                [$key, $scale] = explode(' ', str_replace('Key: ', '', $lines[1]));
                $this->update(compact('bpm', 'key', 'scale'));
                return;
            }
        }
        Log::error("Essentia failed on track {$this->id}: {$output}");
    }

    // ─── Tag & Type Sync ─────────────────────────────────────

    public function syncTags(?string $raw): void
    {
        if (! $raw) return;
        $ids = collect(explode(',', $raw))
            ->map(fn($t) => trim(strtolower($t)))
            ->filter()->unique()
            ->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id);
        $this->tags()->sync($ids->all());
    }

    public function syncTypes(?string $raw): void
    {
        if (! $raw) return;
        $ids = collect(explode(',', $raw))
            ->map(fn($t) => trim(strtolower($t)))
            ->filter()->unique()
            ->map(fn($name) => Type::firstOrCreate(['name' => $name])->id);
        $this->types()->sync($ids->all());
    }

    // ─── Deletion Helper ──────────────────────────────────────

    public function deleteWithFiles(): void
    {
        if ($this->file_path)    Storage::delete("public/{$this->file_path}");
        if ($this->picture)      Storage::delete("public/{$this->picture}");
        if ($this->folder_files) {
            foreach (json_decode($this->folder_files, true) as $f) {
                Storage::delete("public/{$f}");
            }
        }
        $this->delete();
    }

    // ─── Reader Methods ──────────────────────────────────────

    public static function listForUser(Request $r, string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        return self::query()
            ->ownedBy($user->id)
            ->when(
                ! (Auth::check() && Auth::id() === $user->id),
                fn($q) => $q->where('is_private', false)
            )
            ->search($r->search)
            ->filterCategory($r->category)
            ->with(['tags', 'types', 'user'])
            ->get()
            ->each(
                fn($t) =>
                $t->userReactedWith = Reaction::where([
                    'track_id' => $t->id,
                    'user_id' => Auth::id()
                ])->value('reaction')
            );
    }

    public static function favoritesForUser(Request $r)
    {
        $user = User::findOrFail(Auth::id());

        return $user->favoriteTracks()
            ->with(['user', 'tags', 'types', 'reactions'])
            ->filterScopesFromRequest($r)
            ->get()
            ->each(fn($t) => $t->userReactedWith = 'love');
    }

    public static function nameExists(string $name, ?int $exceptId = null): bool
    {
        return self::where('user_id', Auth::id())
            ->where('name', $name)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    // ─── Reaction Workflow ──────────────────────────────────

    public function react(string $type): array
    {
        $visitorId = Auth::id();
        $ownerId   = $this->user_id;
        $user      = Auth::user();

        $existing = $this->reactions()
            ->where('user_id', $visitorId)
            ->first();

        $stat = TrackStat::firstOrCreate(
            ['track_id' => $this->id],
            ['total_listen_seconds' => 0, 'love_count' => 0, 'hate_count' => 0]
        );

        $ui = UserInteraction::firstOrCreate(
            ['user_id' => $visitorId, 'beat_id' => $this->id],
            ['liked' => 0, 'listen_duration' => 0]
        );

        // 1) remove same reaction
        if ($existing && $existing->reaction === $type) {
            $existing->delete();
            $stat->decrement("{$type}_count", $stat->{$type . '_count'} > 0 ? 1 : 0);
            $ui->update(['liked' => 0]);
            $status = 'removed';
        }
        // 2) switch reaction
        elseif ($existing && $existing->reaction !== $type) {
            $old = $existing->reaction;
            $existing->update(['reaction' => $type]);
            $stat->decrement("{$old}_count", $stat->{$old . '_count'} > 0 ? 1 : 0);
            $stat->increment("{$type}_count");
            $ui->update(['liked' => $type === 'love' ? 1 : 0]);
            $status = 'switched';
            if ($ownerId !== $visitorId) {
                $this->user->notify(new ReactionNotification($user, $type, $this));
            }
        }
        // 3) new reaction
        else {
            Reaction::create([
                'owner_id' => $ownerId,
                'user_id' => $visitorId,
                'track_id' => $this->id,
                'reaction' => $type
            ]);
            $stat->increment("{$type}_count");
            $ui->update(['liked' => $type === 'love' ? 1 : 0]);
            $status = 'reacted';
            if ($ownerId !== $visitorId) {
                $this->user->notify(new ReactionNotification($user, $type, $this));
            }
        }

        return [
            'status'     => $status,
            'reaction'   => $type,
            'love_count' => $this->loved_count,
            'hate_count' => $this->hated_count,
        ];
    }

    // ─── Helper to chain scopes in favorites ───────────────

    public function scopeFilterScopesFromRequest($q, Request $r)
    {
        return $q
            ->search($r->search)
            ->filterBpm($r->bpm_range)
            ->filterKey($r->key)
            ->filterScale($r->scale)
            ->filterCategory($r->category);
    }
}
