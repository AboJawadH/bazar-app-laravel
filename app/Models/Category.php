<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        "ar_name",
        "en_name",
        "tr_name",
        "image",
        "parent_section_id",
        "parent_section_name",
        "order_number",
        "is_main_category",
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main_category' => 'boolean',
    ];

    //=======================// relationships
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
