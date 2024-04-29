<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        "ar_name",
        "en_name",
        "tr_name",
        "flag",
        "phone_code",
        "country_code",
        "currency",
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //=======================// relationships
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
