<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationLog extends Model
{
    protected $fillable = [
        'article_id',
        'question_set_id',
        'admin_id',
        'model',
        'requested_count',
        'status',
        'error_message',
        'raw_response',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'requested_count' => 'integer',
            'duration_ms' => 'integer',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function questionSet(): BelongsTo
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}