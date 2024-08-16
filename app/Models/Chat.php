<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_one_id",
        "post_publisher_id",
    ];


    //=======================// relationships
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'post_publisher_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

}
