<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditTrail extends Model
{
    //
    protected $fillable = [
        'user_id', 
        'event', 
        'auditable_type', 
        'auditable_id', 
        'old_values', 
        'new_values', 
        'remarks', 
        'ip_address'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo(null, null, null, 'id')->withTrashed();
    }
}
