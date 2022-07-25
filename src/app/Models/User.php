<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
    ];

    /**
     * Get all the links for the user.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    /**
     * Model observer.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Observer before deleting the user
        static::deleting(function (User $user) {
            $user->links()->delete();
        });
    }
}
