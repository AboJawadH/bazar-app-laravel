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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("parent_region_id")->nullable();
            $table->string("ar_name");
            $table->string("en_name")->nullable();
            $table->string("tr_name")->nullable();
            $table->string("flag")->nullable();
            $table->string("currency")->nullable();
            $table->string("country_code")->nullable();
            $table->string("phone_code")->nullable();
            $table->boolean("is_active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
