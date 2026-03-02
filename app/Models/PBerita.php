<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PBerita extends Model
{
    protected $table = 'p_beritas';

    protected $fillable = [
        'type',
        'title',
        'slug',
        'excerpt',
        'cover',
        'category_label',
        'content',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $m) {
            // auto slug bila kosong
            if (!is_string($m->slug) || trim($m->slug) === '') {
                $m->slug = Str::slug($m->title ?? '');
            }
        });
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover) return null;
        return asset('storage/' . $this->cover);
    }

    public function getCardDateAttribute(): string
    {
        $dt = $this->published_at ?? $this->created_at;
        return optional($dt)->translatedFormat('d M Y') ?? '';
    }
}
