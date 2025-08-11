<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'image', 'slug', 'category'];

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug) && ! empty($article->title)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
