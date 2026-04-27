<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SiteAnnouncement extends Model
{
    protected $table = 'site_announcements';

    protected $fillable = [
        'message',
        'body',
        'image',
        'link_slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function forHeader(): Collection
    {
        return static::query()->active()->ordered()->get(['id', 'message', 'link_slug']);
    }

    public static function listForAnnouncementsPage(string $pageSlug, string $requestSlug): Collection
    {
        if (! Schema::hasTable('site_announcements')) {
            return collect();
        }
        $checkSlug = strtolower($pageSlug !== '' ? $pageSlug : $requestSlug);
        if (! in_array($checkSlug, ['announcements', 'announcement'], true)) {
            return collect();
        }

        return static::query()->active()->ordered()->get();
    }

    public function publicUrl(): string
    {
        $slug = trim((string) $this->link_slug);
        if ($slug === '') {
            $slug = 'announcements';
        }

        return route('showPage', ['slug' => $slug]);
    }
}
