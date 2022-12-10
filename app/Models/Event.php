<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->belongsToMany(Event::class, 'participate_user_events', 'event_id', 'user_id');
    }
    public function userType()
    {
        return $this->morphedByMany(User::class, 'event_viewer');
    }

    public function groupType()
    {
        return $this->morphedByMany(Group::class, 'event_viewer');
    }
}
