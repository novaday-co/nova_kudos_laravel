<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $with = ['users'];
    protected $fillable = ['name', 'avatar', 'activation_date'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }

    public function questionTypes()
    {
        return $this->morphToMany(Question::class, 'question_viewer');
    }

    public function eventTypes()
    {
        return $this->morphToMany(Event::class, 'event_viewer');
    }

    public function events()
    {
        return $this->belongsToMany(Group::class, 'participate_group_events', 'group_id', 'event_id');
    }
}
