<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait AddUuid
{
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
