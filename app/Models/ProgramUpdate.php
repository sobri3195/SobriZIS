<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramUpdate extends Model
{
    protected $fillable = [
        'program_id',
        'user_id',
        'title',
        'content',
        'images',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
