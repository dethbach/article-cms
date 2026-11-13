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
        Schema::create('thumbnail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            // Thumbnail title styling
            $table->string('text_color')->default('#FFFFFF');
            $table->string('text_position')->default('center');
            $table->string('overlay_color')->nullable();
            $table->unsignedTinyInteger('overlay_opacity')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnail_templates');
    }
};
