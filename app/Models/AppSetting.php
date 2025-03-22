<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'is_maintenance_on',
    // ];
    protected $guarded = [];

    protected $casts = [
        'is_maintenance_on' => 'boolean',
        'is_posts_release_automatic' => 'boolean',
    ];

}
