<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicSublink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
    ];

    /**
     * Get the sublink for the music sublink source.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublink()
    {
        return $this->morphOne(Sublink::class);
    }
}
