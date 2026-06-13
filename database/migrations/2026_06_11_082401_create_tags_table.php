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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            
            // نام تگ
            $table->string('name');

            // slug برای URL و SEO
            $table->string('slug')->unique();

            // تعداد استفاده (اختیاری ولی مفید برای سئو و ترند)
            $table->unsignedBigInteger('usage_count')->default(0);

            // فعال/غیرفعال بودن تگ
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
        Schema::dropIfExists('tags');
    }
};
