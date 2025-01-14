<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        "section_id",

        //
        "the_price",
        "currency",
        "title",
        "description",

        "images",
        "is_active",
        "is_special",
        "special_level",
        "is_favored",
        "is_closed",
        //
        "user_id",
        "user_name",
        "user_phone_number",
        //
        "region_id",
        "location_description",
        "location_text",
        "longitude",
        "latitude",
        //
        "is_car_new",
        "is_gear_automatic",
        "gas_type",
        "car_distanse",
        //
        "is_realestate_for_sale",
        "is_realestate_for_family",
        "is_realestate_furnitured",
        "is_there_elevator",
        "realestate_type",
        "number_of_rooms",
        "number_of_toiltes",
        "floor_number",
        //
        "search_word",
    ];

    //=======================//
    //=======================// casts
    //=======================//
    protected $casts = [
        'is_active' => 'boolean',
        'is_special' => 'boolean',
        'is_favored' => 'boolean',
        'is_closed' => 'boolean',
        'is_car_new' => 'boolean',
        'is_gear_automatic' => 'boolean',
        'is_realestate_for_sale' => 'boolean',
        'is_realestate_for_family' => 'boolean',
        'is_realestate_furnitured' => 'boolean',
        'is_there_elevator' => 'boolean',
        'created_at' => 'datetime',
    ];

    //=======================//
    //=======================// relationships
    //=======================//
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //=======================//

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
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
    //=======================//
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    //=======================//
    public function medias()
    {
        return $this->hasMany(PostMedia::class);
    }
    //=======================//
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    //=======================//
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    //=======================//
    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favorable', 'favorites', 'post_id', 'user_id');
    }
}
