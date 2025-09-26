<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
    use HasFactory;
    protected $fillable = ['news_id', 'image'];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}
