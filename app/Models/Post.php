<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        "parent_section_id",
        "parent_section_name",
        "parent_category_id",
        "parent_category_name",
        "subcategory_id",
        "subcategory_name",
        //
        "post_type",
        "the_price",
        "title",
        "description",

        "images",
        "is_active",
        "is_special",
        "special_level",
        "is_favored",
        //
        "user_id",
        "user_name",
        "user_phone_number",
        //
        "country_id",
        "country_name",
        "city_id",
        "city_name",
        "city_ar_name",
        "city_en_name",
        "city_tr_name",
        //
        "is_car_forSale",
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
        'is_car_forSale' => 'boolean',
        'is_car_new' => 'boolean',
        'is_gear_automatic' => 'boolean',
        'is_realestate_for_sale' => 'boolean',
        'is_realestate_for_family' => 'boolean',
        'is_realestate_furnitured' => 'boolean',
        'is_there_elevator' => 'boolean',
    ];

    //=======================//
    //=======================// relationships
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
}
