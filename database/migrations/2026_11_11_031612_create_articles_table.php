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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            // Relationships
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();
            // Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            // Thumbnail
            $table->enum('thumbnail_type', ['upload', 'template'])
                ->default('upload');
            $table->string('thumbnail_path')->nullable();
            $table->foreignId('template_article_id')
                ->nullable()
                ->constrained('thumbnail_templates')
                ->nullOnDelete();
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            // Search Engine Directives
            $table->boolean('allow_indexing')->default(true);
            $table->boolean('allow_following')->default(true);
            // GEO / Structured Content
            $table->json('key_takeaways')->nullable();
            $table->json('faq')->nullable();
            $table->string('schema_type')->default('Article');
            $table->json('schema_data')->nullable();
            $table->json('source_references')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            // Publishing
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Indexes
            $table->index(['category_id', 'is_published']);
            $table->index(['user_id', 'is_published']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
