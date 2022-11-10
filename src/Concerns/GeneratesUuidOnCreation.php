<?php

namespace Lnch\LaravelToolkit\Concerns;

use Illuminate\Support\Str;

trait GeneratesUuidOnCreation
{
    public static function bootGeneratesUuidOnCreation()
    {
        static::creating(function ($model) {
            // Ensure new models have a UUID when created
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }
}