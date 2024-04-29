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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("ar_name");
            $table->string("en_name");
            $table->string("tr_name");
            $table->string("image")->nullable();
            $table->string("parent_section_id");
            $table->string("parent_section_name");
            $table->integer("order_number");
            $table->boolean("is_active");
            $table->boolean("is_main_category")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
