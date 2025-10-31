<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'category',
        'asnaf',
        'target_amount',
        'collected_amount',
        'distributed_amount',
        'total_donors',
        'status',
        'start_date',
        'end_date',
        'region',
        'meta',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'distributed_amount' => 'decimal:2',
            'meta' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            if (empty($program->slug)) {
                $program->slug = Str::slug($program->title);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function updates()
    {
        return $this->hasMany(ProgramUpdate::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(100, ($this->collected_amount / $this->target_amount) * 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->collected_amount);
    }

    public function getAvailableBalanceAttribute(): float
    {
        return max(0, $this->collected_amount - $this->distributed_amount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAsnaf($query, string $asnaf)
    {
        return $query->where('asnaf', $asnaf);
    }
}
