<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sublink extends Model
{
    /**
     * Type music sublink
     */
    public const TYPE_MUSIC_SUBLINK = 'musicSublink';

    /**
     * Type shows sublink
     */
    public const TYPE_SHOWS_SUBLINK = 'showsSublink';

    /**
     * Type list
     */
    public const TYPE_LIST = [
        self::TYPE_MUSIC_SUBLINK,
        self::TYPE_SHOWS_SUBLINK,
    ];

    /**
     * Get the link that owns the sublink.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * Get the linkable model that owns the sublink.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * Model observer.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Observer before deleting the sublink
        static::deleting(function (Sublink $sublink) {
            $sublink->linkable()->delete();
        });
    }
}
