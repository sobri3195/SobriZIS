<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonorReminder extends Model
{
    protected $fillable = [
        'donor_id', 'type', 'is_active', 'last_sent_at', 'next_send_at'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
            'next_send_at' => 'datetime',
        ];
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
