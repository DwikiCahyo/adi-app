<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use App\Exceptions\TooManySlugAttemptsException;

class Events extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'agenda',
        'title',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot model hooks
     */
    protected static function booted(): void
    {
        static::creating(function (Events $event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }

            if (auth()->check()) {
                $event->created_by = auth()->id();
                $event->updated_by = auth()->id();
            }
        });

        static::updating(function (Events $event) {
            if (auth()->check()) {
                $event->updated_by = auth()->id();
            }
        });

        static::deleting(function (Events $event) {
            if (auth()->check()) {
                $event->deleted_by = auth()->id();
                $event->save();
            }
        });
    }

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Relasi ke tabel images
    public function images()
    {
        return $this->hasMany(EventImage::class, 'event_id');
    }

    // Relasi ke tabel topics
    public function topics()
    {
        return $this->hasMany(EventTopic::class, 'event_id');
    }

    /**
     * Query Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Route binding with slug
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $key = 'events_slug_attempts:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw new TooManySlugAttemptsException();
        }

        RateLimiter::hit($key, 60);

        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    /**
     * Slug setter auto unique
     */
    public function setSlugAttribute($value): void
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $this->attributes['slug'] = $slug;
    }
}
