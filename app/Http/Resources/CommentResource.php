<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "post_id" => $this->post_id,
            "user_id" => $this->user_id,
            "user_name" =>
            // $this->user->name,
            $this->whenLoaded('user', function () {
                return $this->user->name;
            }),
            "comment_message" => $this->comment_message,
            "created_at" => $this->created_at,
        ];
    }
}
