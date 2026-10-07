<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_related', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('related_news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['news_id', 'related_news_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_related');
    }
};
