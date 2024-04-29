<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "rating_review" => $this->rating_review,
            "rating_value" => $this->rating_value,
            "user_name" => $this->user_name,

            "created_at" => $this->created_at->format('Y-m-d'),
        ];
    }
}
