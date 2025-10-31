<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutApproval extends Model
{
    protected $fillable = [
        'payout_id',
        'user_id',
        'action',
        'notes',
        'level',
    ];

    public function payout()
    {
        return $this->belongsTo(Payout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
