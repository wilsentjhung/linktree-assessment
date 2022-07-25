<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait AddUuid
{
    /**
     * Model observer to add UUID.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}
