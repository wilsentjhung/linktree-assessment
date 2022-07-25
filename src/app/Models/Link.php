<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    /**
     * Type classic link
     */
    public const TYPE_CLASSIC_LINK = 'classic';

    /**
     * Type music link
     */
    public const TYPE_MUSIC_LINK = 'music';

    /**
     * Type shows link
     */
    public const TYPE_SHOWS_LINK = 'shows';

    /**
     * Type list
     */
    public const TYPE_LIST = [
        self::TYPE_CLASSIC_LINK,
        self::TYPE_MUSIC_LINK,
        self::TYPE_SHOWS_LINK,
    ];

    /**
     * Type list with sublinks
     */
    public const TYPE_LIST_WITH_SUBLINKS = [
        self::TYPE_MUSIC_LINK,
        self::TYPE_SHOWS_LINK,
    ];

    /**
     * Get the user that owns the link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all the sublinks for the link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublinks()
    {
        return $this->hasMany(Sublink::class);
    }

    /**
     * Get the linkable model that owns the link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * Scope links by user uuid.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userUuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserUuid($query, string $userUuid)
    {
        return $query->whereHas('user', function ($q) use ($userUuid) {
            $q->byUuid($userUuid);
        });
    }

    /**
     * Model observer.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Observer before deleting the link
        static::deleting(function (Link $link) {
            $link->sublinks()->delete();
            $link->linkable()->delete();
        });
    }
}
