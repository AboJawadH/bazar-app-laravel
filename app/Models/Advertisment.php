<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisment extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "importance",
        "image",
        "region_id",
        "section_id",
        "region_name",
        "post_id",
        "post_title",
        "ads_link",
        "is_active",
        "is_general",
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_general' => 'boolean',
    ];

    //=======================//
    //=======================// relationships
    //=======================//
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    //=======================//
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
