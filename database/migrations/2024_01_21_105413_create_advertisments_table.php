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
        Schema::create('advertisments', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->string("importance")->nullable();
            $table->string("ad_type")->nullable();
            $table->foreignId("city_id")->nullable();
            $table->string("city_name")->nullable();
            $table->foreignId("country_id")->nullable();
            $table->string("country_name")->nullable();
            $table->string("image")->nullable();
            $table->string("ads_link")->nullable();
            $table->boolean("is_active")->nullable();
            $table->boolean("is_general")->nullable();
            $table->foreignId("post_id")->nullable();
            $table->string("post_title")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisments');
    }
};
