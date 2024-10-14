<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'keywords',
        'categories',
        'content',
        'published_date',
        'source_id',
    ];
    
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'keywords' => 'array',
        ];
    }
}
