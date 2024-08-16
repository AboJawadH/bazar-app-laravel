<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            "flag" => $this->flag,
            "phone_code" => $this->phone_code,
            "country_code" => $this->country_code,
            "currency" => $this->currency,
            "is_active" => $this->is_active,
            'cities' => $this->whenLoaded('cities', fn() => $this->cities->isNotEmpty() ? $this->cities : null),
            'regions' => $this->whenLoaded('regions', fn() => $this->regions->isNotEmpty() ? $this->regions : null),
        ];
    }
}
