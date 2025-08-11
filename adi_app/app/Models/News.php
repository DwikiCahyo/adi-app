<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'content',
        'url',
        'slug'
    ];

    protected $cast = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted():void{

         static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }

            if(auth()->check()){
                $news -> created_by = auth() -> id();
                $news -> updated_by = auth() -> id();
            }
        });

        static::updating(function (News $news) {
            if ($news->isDirty('title')) {
                $news->slug = Str::slug($news->title);
            }

            if(auth() -> check()){
                $news -> updated_by = auth() -> id();
            }
        });

        static::deleting(function (News $news) {
            if (auth()->check()) {
                $news->deleted_by = auth()->id();
                $news->save();
            }
        });

        
    }


    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater():BelongsTo {
        return $this->belongsTo(User::class , 'updated_by');
    }

    public function deleter():BelongsTo {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query) {
        return $query -> whereNull('deleted_at');
    }
    
    public function scopeByCreator($query, $userId) {
        return $query->where('created_by', $userId);
    }

    public function scopeRecent($query, $days = 30){
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getRouteKeyName(): string {
        return 'slug';
    }

    public function setSlugAttribute($value): void {
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
