<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        "country_id",
        "city_id",
        "ar_name",
        "en_name",
        "tr_name",
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
