<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicLink extends Model
{
    use HasFactory;

    /**
     * Get the link for the music link source.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function link()
    {
        return $this->morphOne(Link::class);
    }
}
