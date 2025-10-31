<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mustahik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mustahik';

    protected $fillable = [
        'tenant_id',
        'name',
        'asnaf',
        'nik_masked',
        'phone',
        'address',
        'city',
        'province',
        'description',
        'verification_docs',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'total_received',
        'distribution_count',
    ];

    protected function casts(): array
    {
        return [
            'verification_docs' => 'array',
            'verified_at' => 'datetime',
            'total_received' => 'decimal:2',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeByAsnaf($query, string $asnaf)
    {
        return $query->where('asnaf', $asnaf);
    }
}
