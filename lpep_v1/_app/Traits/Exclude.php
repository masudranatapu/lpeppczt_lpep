<?php

namespace App\Traits;

trait Exclude
{
    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->fillable, (array) $value));
    }
}
