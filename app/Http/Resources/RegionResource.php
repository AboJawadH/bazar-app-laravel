<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            "ar_name" => $this->ar_name,
            "en_name" => $this->en_name,
            "tr_name" => $this->tr_name,
            "parent_region_id" => $this->parent_region_id,
            "parent_region_name" => $this->whenLoaded('parentRegion', function () {
                return $this->parentRegion->ar_name;
            }),
            "is_active" => $this->is_active,
        ];
    }
}
