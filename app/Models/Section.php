<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        "ar_name",
        "en_name",
        "tr_name",
        "image",
        "type",
        "parent_section_id",
        "parent_section_name",
        "order_number",
        "is_active",
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //=======================//
    //=======================// relationships
    //=======================//

    public function parentSection()
    {
        return $this->belongsTo(Section::class, 'parent_section_id', 'id');
    }
    //=======================//
    public function subSections()
    {
        return $this->hasMany(Section::class, 'parent_section_id', 'id');
    }
    //=======================//
}
