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
            $table->string('name');

            // slug برای URL
            $table->string('slug')->unique();

            // زبان (اگر چندزبانه نگه می‌داری)
            $table->string('language')->default('fa');

            // ویژه بودن دسته
            $table->boolean('is_featured')->default(false);

            // توضیح سئو
            $table->text('meta_description')->nullable();

            // کلمات کلیدی سئو (اگر خواستی نگه داری)
            $table->string('meta_keywords')->nullable();

            // ترتیب نمایش
            $table->unsignedInteger('sort_order')->default(0);

            // اخبار برتر این دسته (اگر لازم داشتی بعداً)
            // $table->unsignedBigInteger('top_news_id')->nullable();

            $table->timestamps();

            $table->softDeletes();
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
