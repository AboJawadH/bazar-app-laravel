<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        "parent_region_id",
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
    //=======================//
    //=======================// relationships
    //=======================//

    public function parentRegion()
    {
        return $this->belongsTo(Region::class, 'parent_region_id', 'id');
    }
    //=======================//
    public function subRegions()
    {
        return $this->hasMany(Region::class, 'parent_region_id', 'id');
    }
    //=======================//

}
