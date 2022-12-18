<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyUserTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exchangeStatus($status)
    {
        switch ($status)
        {
            case(0):
                $status = 'pending';
                break;
            case(1) :
                $status = 'done';
                break;
            case(2) :
                $status = 'failed';
        }
        return $status;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
