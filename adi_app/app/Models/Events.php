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
        'url',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ========== BOOT EVENTS ==========
    protected static function booted(): void
    {
        static::creating(function (Events $event) {
            if (empty($event->slug)) {
                $event->slug = $event->title;
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
                if (method_exists($event, 'saveQuietly')) {
                    $event->saveQuietly();
                } else {
                    $event->save();
                }
            }
        });
    }

    // ========== RELATIONSHIPS ==========
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

    public function images() 
    { 
        return $this->hasMany(EventImage::class, 'event_id'); 
    }

    public function topics() 
    { 
        return $this->hasMany(EventTopic::class, 'event_id'); 
    }

    // ========== SCOPES ==========
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

    // ========== ROUTE BINDING ==========
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

    // ========== MUTATORS ==========
    
    /**
     * Mutator untuk slug — pastikan unik (cek juga soft-deleted)
     */
    public function setSlugAttribute($value): void
    {
        $original = Str::slug($value ?: $this->title ?: 'item');
        $slug = $original;
        $counter = 1;
        $maxAttempts = 10;

        while (static::withTrashed()
                ->where('slug', $slug)
                ->where('id', '!=', $this->id ?? 0)
                ->exists()) {

            if ($counter > $maxAttempts) {
                $slug = $original . '-' . substr(md5(uniqid((string) time(), true)), 0, 6);
                break;
            }

            $slug = $original . '-' . $counter;
            $counter++;
        }

        $this->attributes['slug'] = $slug;
    }

    // ========== ACCESSORS ==========
    
    /**
     * Get video thumbnail URL from YouTube/Vimeo
     * Automatically available as $event->thumbnail_url
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (empty($this->url)) {
            return asset('images/default-thumbnail.jpg');
        }
        
        // YouTube - format: https://youtu.be/VIDEO_ID atau https://youtu.be/VIDEO_ID?si=xxx
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)(?:\?|&|$)/', $this->url, $matches)) {
            $videoId = $matches[1];
            // hqdefault lebih stabil daripada maxresdefault
            return 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        // YouTube - format: https://youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        // YouTube - format: https://youtube.com/embed/VIDEO_ID
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        // Vimeo - format: https://vimeo.com/VIDEO_ID
        if (preg_match('/vimeo\.com\/(\d+)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://vumbnail.com/' . $videoId . '.jpg';
        }

        // Jika URL tidak cocok dengan pattern, return default
        return asset('images/default-thumbnail.jpg');
    }

    /**
     * Get embeddable video URL
     * Automatically available as $event->embed_url
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (empty($this->url)) {
            return null;
        }
        
        // YouTube - format: https://youtu.be/VIDEO_ID atau https://youtu.be/VIDEO_ID?si=xxx
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)(?:\?|&|$)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://www.youtube.com/embed/' . $videoId;
        }

        // YouTube - format: https://youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://www.youtube.com/embed/' . $videoId;
        }

        // YouTube - format: https://youtube.com/embed/VIDEO_ID
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $this->url, $matches)) {
            return $this->url; // Already in embed format
        }

        // Vimeo - format: https://vimeo.com/VIDEO_ID
        if (preg_match('/vimeo\.com\/(\d+)/', $this->url, $matches)) {
            $videoId = $matches[1];
            return 'https://player.vimeo.com/video/' . $videoId;
        }

        // Jika sudah dalam format embed, return as is
        return $this->url;
    }

    /**
     * Check if event has video URL
     */
    public function hasVideo(): bool
    {
        return !empty($this->url) && filter_var($this->url, FILTER_VALIDATE_URL);
    }

    /**
     * Check if event has uploaded images
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Get primary display image (thumbnail or first uploaded image)
     */
    public function getPrimaryImageAttribute(): string
    {
        // Prioritas: Video thumbnail dulu, baru uploaded image
        if ($this->hasVideo()) {
            return $this->thumbnail_url;
        }

        // Jika ada uploaded images, ambil yang pertama
        if ($this->hasImages()) {
            return asset('storage/events/' . $this->images->first()->image);
        }

        // Default thumbnail
        return asset('images/default-thumbnail.jpg');
    }
}