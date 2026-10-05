<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('news', 'post_type')) {
            Schema::table('news', fn (Blueprint $table) => $table->enum('post_type', ['article', 'video'])->default('article'));
        }
        if (! Schema::hasColumn('news', 'aparat_video_id')) {
            Schema::table('news', fn (Blueprint $table) => $table->string('aparat_video_id', 100)->nullable());
        }
    }

    public function down(): void
    {
        // These columns also exist in the initial migration. Preserve existing video data on rollback.
    }
};
