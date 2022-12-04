<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'question_viewer');
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'question_viewer');
    }

    public function medals()
    {
        return $this->belongsToMany(Medal::class, 'medal_questions', 'question_id', 'medal_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
