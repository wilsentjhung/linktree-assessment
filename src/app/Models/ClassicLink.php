<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassicLink extends Model
{
    use HasFactory;

    /**
     * Length of title
     */
    public const LEN_TITLE = 144;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'url',
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
     * Get the link for the classic link source.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function link()
    {
        return $this->morphOne(Link::class, 'linkable');
    }
}
