<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('question_set_id')->constrained('question_sets')->cascadeOnDelete();
            $table->enum('status', ['reading', 'playing', 'completed', 'abandoned']);
            $table->timestamp('reading_started_at')->useCurrent();
            $table->timestamp('quiz_started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedTinyInteger('correct_count')->default(0);
            $table->unsignedTinyInteger('total_questions');
            $table->timestamps();

            $table->index(['question_set_id', 'status', 'total_score']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};