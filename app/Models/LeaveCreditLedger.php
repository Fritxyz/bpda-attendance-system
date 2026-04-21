<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCreditLedger extends Model
{
    //
    protected $table = 'leave_credit_ledger';

    protected $fillable = [
        'employee_id',
        'transaction_date',
        'period',
        'type',
        'amount',
        'description',
        'reference_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:4',
    ];

     public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // -------------------------------------------------------
    // Scopes para mas madaling mag-query
    // -------------------------------------------------------

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeBeforePeriod($query, string $period)
    {
        return $query->where('period', '<', $period);
    }

    public function scopeUpToPeriod($query, string $period)
    {
        return $query->where('period', '<=', $period);
    }
}
