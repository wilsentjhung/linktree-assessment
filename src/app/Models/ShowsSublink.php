<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowsSublink extends Model
{
    use HasFactory;

    /**
     * Status on sale
     */
    public const STATUS_ON_SALE = 'on-sale';

    /**
     * Status not on sale
     */
    public const STATUS_NOT_ON_SALE = 'not-on-sale';

    /**
     * Status sold out
     */
    public const STATUS_SOLD_OUT = 'sold-out';

    /**
     * Status list
     */
    public const STATUS_LIST = [
        self::STATUS_ON_SALE,
        self::STATUS_NOT_ON_SALE,
        self::STATUS_SOLD_OUT,
    ];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'status',
        'date',
        'venue',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * Get the sublink for the shows sublink source.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublink()
    {
        return $this->morphOne(Sublink::class, 'linkable');
    }
}
