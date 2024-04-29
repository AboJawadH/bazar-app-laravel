<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            //
            $table->string("parent_section_id");
            $table->string("parent_section_name");
            $table->integer("parent_category_id");
            $table->string("parent_category_name");
            $table->integer("subcategory_id")->nullable();
            $table->string("subcategory_name")->nullable();
            //
            $table->string("title");
            $table->string("description");
            $table->json("images")->nullable();
            $table->string("post_type");
            $table->string("the_price")->nullable();
            $table->boolean("is_active");
            $table->boolean("is_special");
            $table->string("special_level")->nullable();
            $table->boolean("is_favored");
            //
            $table->foreignId("user_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("user_name");
            $table->string("user_phone_number");
            //
            $table->foreignId("country_id");
            $table->string("country_name");
            $table->foreignId("city_id");
            $table->string("city_name");
            $table->string("city_ar_name");
            $table->string("city_en_name");
            $table->string("city_tr_name");
            //
            $table->boolean("is_car_forSale")->nullable();
            $table->boolean("is_car_new")->nullable();
            $table->boolean("is_gear_automatic")->nullable();
            $table->string("gas_type")->nullable();
            $table->string("car_distanse")->nullable();
            //
            $table->boolean("is_realestate_for_sale")->nullable();
            $table->boolean("is_realestate_for_family")->nullable();
            $table->boolean("is_realestate_furnitured")->nullable();
            $table->boolean("is_there_elevator")->nullable();
            $table->string("realestate_type")->nullable();
            $table->integer("number_of_rooms")->nullable();
            $table->integer("number_of_toiltes")->nullable();
            $table->integer("floor_number")->nullable();
            //
            // $table->string("search_word")->nullable();
            //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
