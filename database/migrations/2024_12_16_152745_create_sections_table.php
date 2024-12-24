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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string("ar_name");
            $table->string("en_name")->nullable();
            $table->string("tr_name")->nullable();
            $table->string("image")->nullable();
            $table->foreignId("parent_section_id")->nullable();
            $table->string("parent_section_name")->nullable();
            $table->integer("order_number");
            $table->string("type");
            $table->boolean("is_active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
