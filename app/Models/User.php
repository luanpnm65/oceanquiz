<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_locked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_locked' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by');
    }

    public function questionSets(): HasMany
    {
        return $this->hasMany(QuestionSet::class, 'created_by');
    }

    public function aiGenerationLogs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class, 'admin_id');
    }
}