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
             * image => مسیر فایل
             * video => آپارات video_id
             */
            $table->text('path');

            /*
             * فقط برای ویدیو (اختیاری)
             * aparat, youtube, etc (برای آینده)
             */
            $table->string('provider')->nullable();

            // کپشن تصویر یا توضیح ویدیو
            $table->text('caption')->nullable();

            // ترتیب نمایش (گالری خیلی مهمه)
            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_featured')->default(false);

            $table->softDeletes();
            $table->timestamps();
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
