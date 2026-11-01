<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRateLimitOverride extends Model
{
    protected $fillable = [
        'user_id',
        'daily_limit',
        'unlimited',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unlimited' => 'boolean',
            'daily_limit' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
