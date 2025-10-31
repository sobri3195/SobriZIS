<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'program_id',
        'tenant_id',
        'name',
        'slug',
        'description',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'views',
        'clicks',
        'conversions',
        'total_raised',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'total_raised' => 'decimal:2',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name);
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
