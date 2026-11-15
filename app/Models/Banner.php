<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    public const PLACEMENTS = ['admin', 'homepage'];

    /** Bundled images that live in /public/image (copy your files there). */
    public const DEFAULTS = [
        'admin' => [
            ['path' => 'image/promote1.jpg', 'alt' => 'promote1'],
            ['path' => 'image/promote2.jpg', 'alt' => 'promote2'],
            ['path' => 'image/promote3.jpg', 'alt' => 'promote3'],
            ['path' => 'image/announce-esmeri.jpg', 'alt' => 'ESMERI: the employees of St. Mary Magdalene School'],
            ['path' => 'image/announce-team.jpg', 'alt' => 'Meet our dedicated team: nurse, instructor, office staff, groundskeeper, and administrator'],
            ['path' => 'image/announce-magdalenean-team.jpg', 'alt' => 'Join the Magdalenean team'],
            ['path' => 'image/announce-foundation.jpg', 'alt' => 'St. Mary Magdalene School 63rd Foundation Anniversary'],
            ['path' => 'image/announce-faculty.jpg', 'alt' => 'Join our faculty: St. Mary Magdalene School is hiring'],
            ['path' => 'image/announce-elementary.jpg', 'alt' => 'Join our team: hiring elementary teachers for school year 2026 to 2027'],
            ['path' => 'image/announce-high-school.jpg', 'alt' => 'Join our team: hiring high school teachers for school year 2026 to 2027'],
            ['path' => 'image/announce-senior-high.jpg', 'alt' => 'Join our team: hiring senior high school teachers'],
        ],
        'homepage' => [
            ['path' => 'image/announce-team.jpg', 'alt' => 'Meet our dedicated team: nurse, instructor, office staff, groundskeeper, and administrator'],
            ['path' => 'image/announce-elementary.jpg', 'alt' => 'Join our team: hiring elementary teachers for school year 2026 to 2027'],
            ['path' => 'image/announce-high-school.jpg', 'alt' => 'Join our team: hiring high school teachers for school year 2026 to 2027'],
            ['path' => 'image/announce-foundation.jpg', 'alt' => 'St. Mary Magdalene School 63rd Foundation Anniversary'],
            ['path' => 'image/announce-senior-high.jpg', 'alt' => 'Join our team: hiring senior high school teachers'],
            ['path' => 'image/announce-faculty.jpg', 'alt' => 'Join our faculty: St. Mary Magdalene School is hiring'],
            ['path' => 'image/announce-esmeri.jpg', 'alt' => 'ESMERI: the employees of St. Mary Magdalene School'],
            ['path' => 'image/announce-magdalenean-team.jpg', 'alt' => 'Join the Magdalenean team'],
        ],
    ];

    protected $fillable = ['placement', 'path', 'alt', 'is_default', 'sort_order', 'uploaded_by'];

    protected $casts = ['is_default' => 'boolean'];

    public function getUrlAttribute(): string
    {
        if ($this->is_default) {
            $default = self::DEFAULTS[$this->placement][$this->sort_order - 1] ?? null;

            return asset($default['path'] ?? $this->path);
        }

        return $this->isPublicPath()
            ? asset($this->path)
            : Storage::url($this->path);
    }

    /**
     * Some built-in banners ship as two extra files so the people in them can
     * be animated separately from the background:
     *   image/name-bg.jpg       the banner with the people painted out
     *   image/name-people.webp  only the people, on a transparent background
     * Returns their URLs, or null when the banner is a single flat image.
     */
    public function layers(): ?array
    {
        if (! $this->is_default) {
            return null;
        }

        $path = self::DEFAULTS[$this->placement][$this->sort_order - 1]['path'] ?? $this->path;
        $base = preg_replace('/\.[a-z0-9]+$/i', '', $path);

        if (! File::exists(public_path($base.'-bg.jpg')) || ! File::exists(public_path($base.'-people.webp'))) {
            return null;
        }

        return [
            'background' => asset($base.'-bg.jpg'),
            'people' => asset($base.'-people.webp'),
        ];
    }

    public function deleteImage(): void
    {
        if ($this->is_default) {
            return;
        }

        if ($this->isPublicPath()) {
            File::delete(public_path($this->path));

            return;
        }

        Storage::disk('public')->delete($this->path);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeForPlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Slides in the shape the Blade view and JS expect: [{id, url, alt}].
     * If a placement has no rows at all, the defaults are created first.
     */
    public static function slidesFor(string $placement): array
    {
        if (! static::where('placement', $placement)->exists()) {
            static::restoreDefaults($placement);
        }

        return static::forPlacement($placement)->get()
            ->map(fn (self $banner) => [
                'id'  => $banner->id,
                'url' => $banner->url,
                'alt' => $banner->alt ?: 'Banner',
                'layers' => $banner->layers(),
            ])
            ->values()
            ->all();
    }

    /** Alias so both Banner::slides() and Banner::slidesFor() work. */
    public static function slides(string $placement): array
    {
        return static::slidesFor($placement);
    }

    /** Delete uploads (and their files), then re-create the default rows. */
    public static function restoreDefaults(string $placement): void
    {
        static::where('placement', $placement)
            ->where('is_default', false)
            ->get()
            ->each(fn (self $banner) => $banner->deleteImage());

        static::where('placement', $placement)->delete();

        foreach (self::DEFAULTS[$placement] as $i => $default) {
            static::create([
                'placement'  => $placement,
                'path'       => $default['path'],
                'alt'        => $default['alt'],
                'is_default' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    /** Uploads saved straight into /public/banner (vs. the "public" storage disk). */
    private function isPublicPath(): bool
    {
        return str_starts_with($this->path, 'banner/');
    }
}