<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowsLink extends Model
{
    use HasFactory;

    /**
     * Get the link for the shows link source.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function link()
    {
        return $this->morphOne(Link::class);
    }
}
