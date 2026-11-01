<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'is_correct',
        'shown_at',
        'answered_at',
        'response_ms',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'shown_at' => 'datetime',
            'answered_at' => 'datetime',
            'response_ms' => 'integer',
            'score' => 'integer',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }
}