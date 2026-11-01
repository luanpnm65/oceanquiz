<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->unsignedInteger('start_offset');
            $table->unsignedInteger('end_offset');
            $table->text('selected_text');
            $table->enum('color', ['yellow', 'green', 'blue', 'pink'])->default('yellow');
            $table->timestamps();

            $table->index(['user_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highlights');
    }
};