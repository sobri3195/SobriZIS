<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payout_number',
        'program_id',
        'mustahik_id',
        'tenant_id',
        'amount',
        'description',
        'payment_method',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'proof_files',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'disbursed_by',
        'disbursed_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'proof_files' => 'array',
            'approved_at' => 'datetime',
            'disbursed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_number)) {
                $payout->payout_number = 'PYT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });

        static::updated(function ($payout) {
            if ($payout->wasChanged('status') && $payout->status === 'disbursed') {
                $payout->program->increment('distributed_amount', $payout->amount);
                $payout->mustahik->increment('total_received', $payout->amount);
                $payout->mustahik->increment('distribution_count');
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function mustahik()
    {
        return $this->belongsTo(Mustahik::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function disburser()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function approvals()
    {
        return $this->hasMany(PayoutApproval::class);
    }

    public function needsApproval(): bool
    {
        $threshold = config('sobrizis.approval_threshold', 10000000);
        return $this->amount >= $threshold;
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }
}
