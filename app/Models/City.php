<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        "country_id",
        "ar_name",
        "en_name",
        "tr_name",
        "flag",
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

     //=======================// relationships
     public function regions()
     {
         return $this->hasMany(Region::class);
     }
}
