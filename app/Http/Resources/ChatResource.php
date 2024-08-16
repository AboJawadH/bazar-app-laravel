<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_one_id" => $this->user_one_id,
            "user_one_name" => $this->whenLoaded('userOne', function () {
                return $this->userOne->name;
            }),
            "post_publisher_id" => $this->post_publisher_id,
            // "image" => $this->image,
            "user_two_name" => $this->whenLoaded('userTwo', function () {
                return $this->userTwo->name;
            }),
            'messages' => $this->whenLoaded('messages', fn () => $this->messages->isNotEmpty() ? $this->messages : null),
            "created_at" => $this->created_at,
        ];
    }
}
