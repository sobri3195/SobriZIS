<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'program_id',
        'tenant_id',
        'title',
        'description',
        'images',
        'event_date',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'event_date' => 'date',
            'is_published' => 'boolean',
        ];
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
