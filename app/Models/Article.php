<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'wiki_page_id',
        'title',
        'slug',
        'lang',
        'summary',
        'content',
        'thumbnail_url',
        'source_url',
        'fetched_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'wiki_page_id' => 'integer',
            'fetched_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questionSets(): HasMany
    {
        return $this->hasMany(QuestionSet::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    public function aiGenerationLogs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class);
    }
}