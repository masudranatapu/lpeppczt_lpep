<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioEnergy extends Model
{
    use HasFactory;
    protected $fillable=[

    'vist_date',
    'client_name',
    'client_number',
    'district',
    'upazila',
    'union',
    'livestock details',
    'size',
    'start_date',
    'end_date',
    'organizer_name',
    'condition'





    ];
}
