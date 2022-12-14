<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id')
            ->withPivot('first_name', 'last_name', 'job_position', 'avatar','profile_complete', 'coin_amount',
                'currency_amount', 'notification_unread', 'role_id')->withTimestamps();
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function coin_value_history()
    {
        return $this->hasMany(CoinValueHistory::class);
    }

    public function coin(): HasOne
    {
        return $this->hasOne(CoinValue::class);
    }

    public function companyUserTransactions(): HasMany
    {
        return $this->hasMany(CompanyUserTransaction::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function giftCards(): HasMany
    {
        return $this->hasMany(GiftCard::class);
    }

    /**
     * gift card transaction
     */
    public function companyUserGiftTransaction(): HasMany
    {
        return $this->hasMany(GiftUserTransaction::class);
    }

    public function medals(): HasMany
    {
        return $this->hasMany(Medal::class);
    }

    public function companyUserProductTransaction(): HasMany
    {
        return $this->hasMany(CompanyUserProductTransaction::class);
    }
}
