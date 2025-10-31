<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'donor_id',
        'program_id',
        'tenant_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'unique_code',
        'total_amount',
        'admin_fee',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_response',
        'status',
        'is_anonymous',
        'message',
        'receipt_number',
        'receipt_pdf',
        'paid_at',
        'expired_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'unique_code' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donation) {
            if (empty($donation->order_id)) {
                $donation->order_id = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });

        static::updated(function ($donation) {
            if ($donation->wasChanged('status') && $donation->status === 'success') {
                $donation->program->increment('collected_amount', $donation->amount);
                $donation->program->increment('total_donors');
                
                if ($donation->donor_id) {
                    $donation->generateReceipt();
                }
            }
        });
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function notifications()
    {
        return $this->hasMany(DonationNotification::class);
    }

    public function generateReceipt(): void
    {
        if (empty($this->receipt_number)) {
            $this->receipt_number = 'RCP-' . date('Ymd') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->save();
        }
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'waiting_payment']);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
