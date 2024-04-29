<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "post_publisher_id",
        "post_publisher_name",
        "post_title",
        //
        "rporter_id",
        "reporter_name",
        "report_title",
        "report_message",
    ];
}
