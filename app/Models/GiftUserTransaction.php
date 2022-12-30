<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftUserTransaction extends Model
{
    use HasFactory;

    protected $table = 'gift_user_transactions';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fromId()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function toId()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
}
