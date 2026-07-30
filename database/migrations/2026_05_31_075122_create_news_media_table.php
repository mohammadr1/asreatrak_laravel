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
        Schema::create('news_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('news_id')
                ->constrained()
                ->cascadeOnDelete();

            // نوع رسانه
            $table->enum('type', [
                'image',
                'video',
                'audio',
                'document'
            ]);
            /*
            |--------------------------------------------------------------------------
            | Media Source
            |--------------------------------------------------------------------------
            | image    => uploads/news/....
            | video    => aparat video id
            | audio    => uploads/audio/...
            | document => uploads/docs/...
            */
            $table->text('path')->nullable();;


            $table->text('video_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Provider
            |--------------------------------------------------------------------------
            | aparat
            | youtube
            | upload
            */
            $table->string('provider')->nullable();

            /*
            |--------------------------------------------------------------------------
            | عنوان رسانه
            |--------------------------------------------------------------------------
            */
            $table->string('title')->nullable();

            
            /*
            |--------------------------------------------------------------------------
            | کپشن
            |--------------------------------------------------------------------------
            */
            $table->text('caption')->nullable();

            
            /*
            |--------------------------------------------------------------------------
            | ترتیب نمایش
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('sort_order')->default(0);

            
            /*
            |--------------------------------------------------------------------------
            | تصویر شاخص
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_featured')->default(false);


            /*
            |--------------------------------------------------------------------------
            | فعال یا غیرفعال
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['news_id', 'sort_order']);
            $table->index(['type']);
            $table->index(['is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_media');
    }
};
