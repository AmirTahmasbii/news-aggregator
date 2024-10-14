<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'keyword',
        'category',
        'content',
        'published_date',
        'source_id',
    ];
    
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
