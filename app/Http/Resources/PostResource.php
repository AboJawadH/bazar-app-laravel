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
            "parent_section_id" => $this->parent_section_id,
            "parent_section_name" => $this->parent_section_name,
            "parent_category_id" => $this->parent_category_id,
            "parent_category_name" => $this->parent_category_name,
            "subcategory_id" => $this->subcategory_id,
            "subcategory_name" => $this->subcategory_name,
            //
            "title" => $this->title,
            "description" => $this->description,
            "post_type" => $this->post_type,
            "the_price" => $this->the_price,
            "is_active" => $this->is_active,
            "is_special" => $this->is_special,
            "special_level" => $this->special_level,
            "is_favored" => $this->is_favored,
            //
            "user_id" => $this->user_id,
            "user_name" => $this->user_name,
            "user_phone_number" => $this->user_phone_number,
            //
            "country_id" => $this->country_id,
            "country_name" => $this->country_name,
            "city_id" => $this->city_id,
            "city_name" => $this->city_name,
            "city_ar_name" => $this->city_ar_name,
            "city_en_name" => $this->city_en_name,
            "city_tr_name" => $this->city_tr_name,
            //
            "is_car_forSale" => $this->is_car_forSale,
            "is_car_new" => $this->is_car_new,
            "is_gear_automatic" => $this->is_gear_automatic,
            "gas_type" => $this->gas_type,
            "car_distanse" => $this->car_distanse,
            //
            "is_realestate_for_sale" => $this->is_realestate_for_sale,
            "is_realestate_for_family" => $this->is_realestate_for_family,
            "is_realestate_furnitured" => $this->is_realestate_furnitured,
            "is_there_elevator" => $this->is_there_elevator,
            "realestate_type" => $this->realestate_type,
            "number_of_rooms" => $this->number_of_rooms,
            "number_of_toiltes" => $this->number_of_toiltes,
            "floor_number" => $this->floor_number,
            //
            "created_at" => $this->created_at,
            //
            'medias' => $this->whenLoaded('medias', fn () => $this->medias->isNotEmpty() ? PostMediaResource::collection($this->medias) : null),
            'comments' => $this->whenLoaded('comments', fn () => $this->comments->isNotEmpty() ? $this->comments : null),
        ];
    }
}
