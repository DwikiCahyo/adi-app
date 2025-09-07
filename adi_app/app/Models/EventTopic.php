<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'topic', 'content'
    ];

    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
