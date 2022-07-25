<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowsLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Length of title
     */
    public const LEN_TITLE = 144;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * @TODO Add more cols related to shows base link (if any)
     */
    protected $fillable = [
        'title',
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
     * Get the link for the shows link source.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function link()
    {
        return $this->morphOne(Link::class, 'linkable');
    }
}
