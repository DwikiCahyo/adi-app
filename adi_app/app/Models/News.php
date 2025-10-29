<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use App\Exceptions\TooManySlugAttemptsException;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'content',
        'url',
        'slug',
        'publish_at',
        'status',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = $news->title;
            }

            if (auth()->check()) {
                $news->created_by = auth()->id();
                $news->updated_by = auth()->id();
            }
        });

        static::updating(function (News $news) {
            if (auth()->check()) {
                $news->updated_by = auth()->id();
            }
        });

        static::deleting(function (News $news) {
            if (auth()->check()) {
                $news->deleted_by = auth()->id();
                if (method_exists($news, 'saveQuietly')) {
                    $news->saveQuietly();
                } else {
                    $news->save();
                }
            }
        });
    }

    // Relationships
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }

    public function images() { return $this->hasMany(NewsImage::class, 'news_id'); }

    // Query Scopes
    public function scopeActive($query) { return $query->whereNull('deleted_at'); }
    public function scopeByCreator($query, $userId) { return $query->where('created_by', $userId); }
    public function scopeRecent($query, $days = 30) { return $query->where('created_at', '>=', now()->subDays($days)); }

    /**
     * Scope untuk published content
     * Hanya menampilkan content yang:
     * - Status = 'published'
     * - publish_at sudah lewat atau sama dengan waktu sekarang
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('publish_at', '<=', now('Asia/Jakarta'));
    }
    
    /**
     * Scope untuk scheduled content
     * Menampilkan content yang dijadwalkan di masa depan
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->where('publish_at', '>', now('Asia/Jakarta'));
    }
    
    /**
     * Scope untuk draft
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function getRouteKeyName(): string { return 'slug'; }

    public function resolveRouteBinding($value, $field = null)
    {
        $key = 'news_slug_attempts:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw new TooManySlugAttemptsException();
        }

        RateLimiter::hit($key, 60);

        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    /**
     * Mutator untuk slug – pastikan unik (cek juga soft-deleted)
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

    // Helper Methods
    
    /**
     * Cek apakah news sudah published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->publish_at && 
               $this->publish_at->lte(now('Asia/Jakarta'));
    }
    
    /**
     * Cek apakah news dijadwalkan untuk publish
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && 
               $this->publish_at && 
               $this->publish_at->gt(now('Asia/Jakarta'));
    }
    
    /**
     * Cek apakah news masih draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
    
    /**
     * Get formatted publish date
     */
    public function getFormattedPublishDate(): string
    {
        if (!$this->publish_at) {
            return '-';
        }
        
        return $this->publish_at->format('d M Y, H:i') . ' WIB';
    }
    
    /**
     * Get status badge HTML
     */
    public function getStatusBadge(): string
    {
        return match($this->status) {
            'published' => '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Published</span>',
            'scheduled' => '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Scheduled</span>',
            'draft' => '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Draft</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Unknown</span>',
        };
    }
}