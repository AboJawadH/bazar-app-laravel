<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            //
            "section_id" => $this->section_id,
            "section_name" => $this->whenLoaded('section', function () {
                return $this->section->ar_name;
            }),
            //
            "title" => $this->title,
            "description" => $this->description,
            "post_type" => $this->whenLoaded('section', function () {
                return $this->section->type;
            }),
            "the_price" => $this->the_price,
            "currency" => $this->currency,
            "is_active" => $this->is_active,
            "is_special" => $this->is_special,
            "special_level" => $this->special_level,
            "is_favored" => (bool) $this->is_favored,
            "is_closed" => (bool) $this->is_closed,
            //
            "user_id" => $this->user_id,
            "user_name" => $this->whenLoaded('user', function () {
                return $this->user->name;
            }),
            "user_phone_number" => $this->whenLoaded('user', function () {
                return $this->user->phone_number;
            }),

            //

            "region_id" => $this->region_id,
            "region_name" => $this->whenLoaded('region', function () {
                return $this->region->ar_name;
            }),
            // "region_ar_name" => $this->whenLoaded('region', function () {
            //     return $this->region->ar_name;
            // }),
            "region_en_name" => $this->whenLoaded('region', function () {
                return $this->region->en_name;
            }),
            "region_tr_name" => $this->whenLoaded('region', function () {
                return $this->region->tr_name;
            }),
            "location_text" => $this->location_text,
            "location_description" => $this->location_description,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            //
            "is_car_new" => $this->is_car_new,
            "is_gear_automatic" => $this->is_gear_automatic,
            "gas_type" => $this->gas_type,
            "car_distanse" => $this->car_distanse,
            //
            "is_realestate_for_sale" => $this->is_realestate_for_sale,
            "is_realestate_for_family" => $this->is_realestate_for_family,
            "is_realestate_furnitured" => $this->is_realestate_furnitured,
            "is_there_elevator" => $this->is_there_elevator,
            // "realestate_type" => $this->realestate_type,
            "number_of_rooms" => $this->number_of_rooms,
            "number_of_toiltes" => $this->number_of_toiltes,
            "floor_number" => $this->floor_number,
            //
            "created_at" => $this->created_at,
            //
            'medias' => $this->whenLoaded('medias', fn() => $this->medias->isNotEmpty() ? PostMediaResource::collection($this->medias) : null),
            // 'comments' => $this->whenLoaded('comments', fn () => $this->comments->isNotEmpty() ? $this->comments : null),
        ];
    }
}
