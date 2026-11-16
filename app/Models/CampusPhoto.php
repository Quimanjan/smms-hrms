<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

/** A photo in "The campus" section of the homepage. Files live under /public. */
class CampusPhoto extends Model
{
    public const UPLOAD_DIR = 'campus';

    protected $fillable = ['path', 'full_path', 'caption', 'sort_order', 'uploaded_by'];

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    public function getFullUrlAttribute(): string
    {
        return asset($this->full_path ?: $this->path);
    }

    /** Removes the uploaded file (the photos that came with the site are kept). */
    public function deleteFile(): void
    {
        foreach (array_unique(array_filter([$this->path, $this->full_path])) as $p) {
            if (str_starts_with($p, self::UPLOAD_DIR.'/')) {
                File::delete(public_path($p));
            }
        }
    }
}
