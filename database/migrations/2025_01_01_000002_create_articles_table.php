<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->unsignedInteger('wiki_page_id')->unique();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('lang', 5)->default('vi');
            $table->text('summary');
            $table->longText('content');
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('source_url', 500);
            $table->timestamp('fetched_at')->useCurrent();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};