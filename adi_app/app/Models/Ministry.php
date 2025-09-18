<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use App\Exceptions\TooManySlugAttemptsException;

class Ministry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ministry';

    protected $fillable = [
        'title',
        'content',
        'category',
        'slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ministry $ministry) {
            if (empty($ministry->slug)) {
                $ministry->slug = $ministry->title;
            }

            if (auth()->check()) {
                $ministry->created_by = auth()->id();
                $ministry->updated_by = auth()->id();
            }
        });

        static::updating(function (Ministry $ministry) {
            if (auth()->check()) {
                $ministry->updated_by = auth()->id();
            }
        });

        static::deleting(function (Ministry $ministry) {
            if (auth()->check()) {
                $ministry->deleted_by = auth()->id();
                if (method_exists($ministry, 'saveQuietly')) {
                    $ministry->saveQuietly();
                } else {
                    $ministry->save();
                }
            }
        });
    }

    // relationships...
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
        return $this->hasMany(MinistryImage::class, 'ministry_id');
    }        


    // scopes
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

    // gunakan slug untuk route binding
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $key = 'ministry_slug_attempts:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw new TooManySlugAttemptsException();
        }

        RateLimiter::hit($key, 60);

        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

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
}
