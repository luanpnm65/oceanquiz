<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('question_set_id')->nullable()->constrained('question_sets')->nullOnDelete();
            $table->foreignId('admin_id')->constrained('users');
            $table->string('model', 100);
            $table->unsignedTinyInteger('requested_count');
            $table->enum('status', ['pending', 'success', 'failed']);
            $table->text('error_message')->nullable();
            $table->longText('raw_response')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_logs');
    }
};