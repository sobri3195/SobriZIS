<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo',
        'description',
        'email',
        'phone',
        'address',
        'npwp',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
        'amil_percentage',
        'approval_threshold',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'amil_percentage' => 'decimal:2',
            'approval_threshold' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function mustahik()
    {
        return $this->hasMany(Mustahik::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
