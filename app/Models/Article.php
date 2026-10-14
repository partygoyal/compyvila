<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $timestamps = false;

    protected $fillable = ['slug', 'title', 'excerpt', 'body', 'category', 'coin', 'image', 'sources', 'status', 'featured', 'published_at', 'updated_at'];

    protected function casts(): array
    {
        return ['featured' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now()->format('Y-m-d H:i:s'));
    }
}
