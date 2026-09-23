<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait AutoFileDelete
{
    protected static function bootAutoFileDelete()
    {
        static::deleting(function ($model) {
            foreach (static::autoFileDeleteConfig() ?? [] as $arr) {
                Storage::disk($arr['disk'])->delete($model->{$arr['property']});
            }
        });
    }
}
