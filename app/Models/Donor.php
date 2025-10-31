<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'kyc_level',
        'tax_id',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'date_of_birth',
        'gender',
        'preferences',
        'asset_profile',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => 'array',
            'asset_profile' => 'array',
            'verified_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function reminders()
    {
        return $this->hasMany(DonorReminder::class);
    }

    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()->where('status', 'success')->sum('amount');
    }

    public function getLastDonationAttribute()
    {
        return $this->donations()->where('status', 'success')->latest('paid_at')->first();
    }
}
