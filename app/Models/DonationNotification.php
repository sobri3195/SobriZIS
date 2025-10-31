<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationNotification extends Model
{
    protected $fillable = [
        'donation_id',
        'type',
        'event',
        'content',
        'status',
        'sent_at',
        'error_message',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
