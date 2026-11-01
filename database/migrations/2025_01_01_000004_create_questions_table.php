<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_set_id')->constrained('question_sets')->cascadeOnDelete();
            $table->text('content');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('position');
            $table->timestamps();

            $table->unique(['question_set_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};