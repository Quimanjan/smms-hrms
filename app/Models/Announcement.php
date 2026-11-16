<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** A text announcement on the public homepage, posted by HR or Admin. */
class Announcement extends Model
{
    protected $fillable = ['title', 'body', 'posted_on', 'is_published', 'created_by'];

    protected $casts = ['posted_on' => 'date', 'is_published' => 'boolean'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereDate('posted_on', '<=', now(config('app.display_timezone'))->toDateString());
    }
}
