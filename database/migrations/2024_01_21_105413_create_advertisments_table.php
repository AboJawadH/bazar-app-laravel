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
            $table->foreignId("region_id")->nullable();
            $table->foreignId("section_id")->nullable();
            $table->string("image");
            $table->string("title")->nullable();
            $table->string("importance")->nullable();
            $table->string("ads_link")->nullable();
            $table->foreignId("post_id")->nullable();
            $table->string("post_title")->nullable();
            $table->boolean("is_active")->nullable();
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
