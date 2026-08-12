<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'old_status',
        'new_status',
        'notes',
        'metadata',
        'ip_address',
        'properties',
    ];

    protected $casts = [
        'metadata' => 'array',
        'properties' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
