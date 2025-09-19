<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinistryImage extends Model
{
    use HasFactory;

    protected $table = 'ministryimages'; // karena nama tabel tidak jamak standar
    protected $fillable = ['ministry_id', 'image'];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id');
    }
}
