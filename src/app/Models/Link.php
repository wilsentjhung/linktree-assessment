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
    public const TYPE_CLASSIC_LINK = 'classicLink';

    /**
     * Type music link
     */
    public const TYPE_MUSIC_LINK = 'musicLink';

    /**
     * Type shows link
     */
    public const TYPE_SHOWS_LINK = 'showsLink';

    /**
     * Type list
     */
    public const TYPE_LIST = [
        self::TYPE_CLASSIC_LINK,
        self::TYPE_MUSIC_LINK,
        self::TYPE_SHOWS_LINK,
    ];

    /**
     * Get the user that owns the link.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all the sublinks for the link.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublinks()
    {
        return $this->hasMany(Sublink::class);
    }

    /**
     * Get the linkable model that owns the link.
     */
    public function linkable()
    {
        return $this->morphTo();
    }
}
