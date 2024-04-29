<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        "post_id",
        "user_id",
        "user_name",
        "rating_value",
        "rating_review",
    ];

    // protected $casts = [
    //     'rating_value' => 'string',
    // ];
    
}
