<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'medal_users', 'medal_id', 'user_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'medal_questions', 'medal_id', 'question_id');
    }
}
