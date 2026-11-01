<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionSet extends Model
{
    protected $fillable = [
        'article_id',
        'title',
        'reading_seconds',
        'question_seconds',
        'source',
        'status',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'reading_seconds' => 'integer',
            'question_seconds' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}