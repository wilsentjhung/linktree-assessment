<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sublink extends Model
{
    use HasFactory;

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
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * Get the linkable model that owns the sublink.
     */
    public function linkable()
    {
        return $this->morphTo();
    }
}
