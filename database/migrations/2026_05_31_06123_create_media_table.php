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
        Schema::create('media', function (Blueprint $table) {
           
            $table->id();

            $table->string('uuid')->unique();

            $table->string('disk')->default('public');

            $table->string('directory')->nullable();

            $table->string('filename');

            $table->string('original_path')
                ->nullable();

            $table->string('extension',20);

            $table->string('mime_type');

            $table->unsignedBigInteger('size')->default(0);

            $table->unsignedInteger('width')->nullable();

            $table->unsignedInteger('height')->nullable();

            $table->unsignedInteger('duration')->nullable();

            $table->string('type',20);

            $table->string('title')->nullable();

            $table->string('alt')->nullable();

            $table->text('caption')->nullable();

            $table->string('copyright')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->index('type');

            $table->index('uploaded_by');

            $table->foreignId('watermark_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();



            $table->string('cropped_path')->nullable();

            $table->string('watermarked_path')->nullable();

            $table->json('crop_data')->nullable();


            $table->index('mime_type');



            $table->string('hash')->nullable();

            $table->enum('visibility', [
                'public',
                'private',
            ])->default('public');

            $table->boolean('has_watermark')
                ->default(false);

            $table->string('watermark_type')
                ->nullable();

            $table->json('metadata')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
