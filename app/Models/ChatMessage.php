<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        "chat_id",
        "sender_id",
        "sender_name",
        "image",
        "voice_message",
        "voice_duration",
        "text",
        "is_read",
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    //=======================// relationships

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
