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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("post_publisher_id")->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->String("post_publisher_name");
            $table->String("post_title");
            //
            $table->foreignId("rporter_id")->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->String("reporter_name");
            $table->String("report_title");
            $table->String("report_message");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
