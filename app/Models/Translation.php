<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'locale',
        'content',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];


    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }


    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }


    public function scopeKeySearch(Builder $query, string $search): Builder
    {
        return $query->where('key', 'LIKE', "%{$search}%");
    }


    public function scopeContentSearch(Builder $query, string $search): Builder
    {
        return $query->where('content', 'LIKE', "%{$search}%");
    }
}
