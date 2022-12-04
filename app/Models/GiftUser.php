<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function giftSender()
    {
        return $this->belongsTo(User::class);
    }

    public function giftRecipient()
    {
        return $this->belongsTo(User::class);
    }
}
