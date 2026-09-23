<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtlDiseaseHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function diseaseHistory()
    {
        return $this->belongsTo(DiseaseHistory::class, 'disease_history_id');
    }
}
