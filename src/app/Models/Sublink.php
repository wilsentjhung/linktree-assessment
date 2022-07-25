<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sublink extends Model
{
    use SoftDeletes;

    /**
     * Suffix for sublink type
     */
    public const SUFFIX_SUBLINK_TYPE = 'Sublink';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'link_id',
        'linkable_id',
        'linkable_type',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'link_id',
        'linkable_id',
        'linkable_type',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array<int, string>
     */
    protected $with = [
        'linkable',
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
