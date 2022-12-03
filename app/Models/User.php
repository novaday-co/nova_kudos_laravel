<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile',
        'first_name',
        'last_name',
        'avatar',
        'otp_code',
        'expiration_otp',
        'activation_date',
    ];

    //protected $with = ['groups', 'questions'];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function questionTypes()
    {
        return $this->morphToMany(Question::class, 'question_viewer');
    }

    public function eventTypes()
    {
        return $this->morphToMany(Event::class, 'event_viewer');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'participate_user_events', 'user_id', 'event_id');
    }

}
