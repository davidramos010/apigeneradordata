<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsageCounter extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'usage_date',
        'request_count',
    ];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
            'request_count' => 'integer',
        ];
    }
}
