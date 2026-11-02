<?php
namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/demo_sets.json');

        if (! File::exists($jsonPath)) {
            throw new RuntimeException("Không tìm thấy file dữ liệu mẫu: {$jsonPath}");
        }

        $items = json_decode(File::get($jsonPath), true);

        if (! is_array($items)) {
            throw new RuntimeException('File demo_sets.json không đúng định dạng JSON.');
        }

        $admin = User::where('role', 'admin')->firstOrFail();

        DB::transaction(function () use ($items, $admin) {
            foreach ($items as $item) {
                $category = Category::where('slug', $item['category_slug'] ?? 'khac')->first();

                $article = Article::updateOrCreate(
                    ['wiki_page_id' => $item['wiki_page_id']],
                    [
                        'category_id' => $category?->id,
                        'title' => $item['title'],
                        'slug' => $item['slug'],
                        'lang' => 'vi',
                        'summary' => $item['summary'],
                        'content' => $item['content'],
                        'thumbnail_url' => $item['thumbnail_url'] ?? null,
                        'source_url' => $item['source_url'],
                        'fetched_at' => now(),
                        'created_by' => $admin->id,
                    ]
                );

                $setData = $item['question_set'];

                $questionSet = QuestionSet::updateOrCreate(
                    [
                        'article_id' => $article->id,
                        'title' => $setData['title'],
                    ],
                    [
                        'reading_seconds' => $setData['reading_seconds'] ?? 180,
                        'question_seconds' => $setData['question_seconds'] ?? 20,
                        'source' => 'import',
                        'status' => 'published',
                        'published_at' => now(),
                        'created_by' => $admin->id,
                    ]
                );

                // Xóa câu hỏi cũ của bộ này (nếu chạy seed lại) để tránh trùng position
                $questionSet->questions()->delete();

                foreach ($setData['questions'] as $qIndex => $qData) {
                    $question = Question::create([
                        'question_set_id' => $questionSet->id,
                        'content' => $qData['content'],
                        'explanation' => $qData['explanation'] ?? null,
                        'position' => $qIndex + 1,
                    ]);

                    foreach ($qData['options'] as $optIndex => $optText) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'position' => $optIndex,
                            'content' => $optText,
                            'is_correct' => ($optIndex === (int) $qData['correct_index']),
                        ]);
                    }
                }
            }
        });
    }
}