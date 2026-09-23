<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasFactory;

    /**
     * Support both legacy JSON-encoded picture paths and newly stored paths.
     */
    public function getPictureStoragePathAttribute(): ?string
    {
        if (!$this->picture) {
            return null;
        }

        $decodedPath = json_decode($this->picture, true);

        return is_string($decodedPath) ? $decodedPath : $this->picture;
    }

    public function getPictureUrlAttribute(): ?string
    {
        $path = $this->picture_storage_path;

        if (!$path || !Storage::disk('public_uploads')->exists($path)) {
            return null;
        }

        return asset(ltrim(str_replace('\\', '/', $path), '/'));
    }
}
