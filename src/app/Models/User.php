<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * @TODO Add more user cols as this is only a simplistic model
     * @TODO Add authentication capabilities
     */
    protected $fillable = [
        'username',
        'email',
    ];

    /**
     * Get all the links for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    /**
     * Scope users by user uuid (case insensitive).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $uuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Model observer.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Observer before creating the user
        static::creating(function (User $user) {
            $user->uuid = Str::uuid()->toString();
        });

        // Observer before deleting the user
        static::deleting(function (User $user) {
            $user->links()->delete();
        });
    }
}
