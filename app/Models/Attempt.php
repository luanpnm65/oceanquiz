<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    protected $fillable = [
        'user_id',
        'question_set_id',
        'status',
        'reading_started_at',
        'quiz_started_at',
        'finished_at',
        'total_score',
        'correct_count',
        'total_questions',
    ];

    protected function casts(): array
    {
        return [
            'reading_started_at' => 'datetime',
            'quiz_started_at' => 'datetime',
            'finished_at' => 'datetime',
            'total_score' => 'integer',
            'correct_count' => 'integer',
            'total_questions' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questionSet(): BelongsTo
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}