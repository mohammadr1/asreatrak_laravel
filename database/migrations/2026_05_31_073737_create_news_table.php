<?php

use App\Enums\NewsStatus;
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
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('featured_media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();


            $table->string('title');
            $table->text('lead')->nullable();
            $table->string('uptitle')->nullable();
            $table->string('slug')->unique(); 
            $table->longText('content')->nullable();

                
            $table->string('news_code')->unique(); // BN-140315-0042

            // خبرنگار
            $table->foreignId('reporter_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // آمار
            $table->unsignedBigInteger('views_count')->default(0);

            // وضعیت انتشار
            $table->enum('status', [
                NewsStatus::Draft->value,       // پیش‌نویس خبرنگار
                NewsStatus::Pending->value,     // ارسال شده برای سردبیر
                NewsStatus::Approved->value,    // تایید سردبیر
                NewsStatus::Rejected->value,    // رد شده
                NewsStatus::Published->value,   // منتشر شده
                NewsStatus::Scheduled->value
            ])->default(NewsStatus::Draft->value);

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->timestamp('approved_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->string('short_link')->nullable();

            $table->string('report_type')->nullable();

            $table->enum('production_method', [
                'بازنشری',
                'پوششی',
                'تولیدی',
                'دریافتی'
            ])->nullable();

            $table->string('meta_title')->nullable();

            $table->text('meta_description')->nullable();

            // زمان انتشار
            $table->timestamp('published_at')->nullable();


            $table->boolean('is_breaking')->default(false);
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
        Schema::dropIfExists('news');
    }
};
