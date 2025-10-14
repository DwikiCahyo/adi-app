<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use App\Exceptions\TooManySlugAttemptsException;
use App\Models\User;
use Carbon\Carbon;

class ResourceFile extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'resourcefile';
    
    protected $fillable = [
        'title',
        'refleksi_diri',
        'pengakuan_iman',
        'bacaan_alkitab',
        'content',
        'slug',
        'publish_at',
        'status',
        'created_by',
        'updated_by',
        'created_at',
    ];
    
    protected $casts = [
        'publish_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    protected static function booted(): void
    {
        static::creating(function (ResourceFile $resourceFile) {
            if (empty($resourceFile->slug)) {
                $resourceFile->slug = $resourceFile->title;
            }
            if (auth()->check()) {
                if (empty($resourceFile->created_by)) {
                    $resourceFile->created_by = auth()->id();
                }
                if (empty($resourceFile->updated_by)) {
                    $resourceFile->updated_by = auth()->id();
                }
            }
        });
        
        static::updating(function (ResourceFile $resourceFile) {
            if (auth()->check()) {
                $resourceFile->updated_by = auth()->id();
            }
        });
        
        static::deleting(function (ResourceFile $resourceFile) {
            if (auth()->check()) {
                $resourceFile->deleted_by = auth()->id();
                if (method_exists($resourceFile, 'saveQuietly')) {
                    $resourceFile->saveQuietly();
                } else {
                    $resourceFile->save();
                }
            }
        });
    }
    
    // Relationships
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
    
    // Query scopes
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
    
    public function getRouteKeyName(): string 
    { 
        return 'slug'; 
    }
    
    public function resolveRouteBinding($value, $field = null)
    {
        $key = 'resourcefile_slug_attempts:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw new TooManySlugAttemptsException();
        }
        RateLimiter::hit($key, 60);
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
    
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
    
    // Helper methods
    
    /**
     * Cek apakah resource sudah published
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->publish_at && 
               $this->publish_at->lte(now('Asia/Jakarta'));
    }
    
    /**
     * Cek apakah resource dijadwalkan untuk publish
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && 
               $this->publish_at && 
               $this->publish_at->gt(now('Asia/Jakarta'));
    }
    
    /**
     * Cek apakah resource masih draft
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
    
    /**
     * Get formatted publish date
     * @return string
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
     * @return string
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