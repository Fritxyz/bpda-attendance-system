<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    //
    protected $fillable = [
        'employee_id', 'to_number', 'destination', 'purpose', 
        'date_from', 'date_to', 
    ];

    // Relation para madaling makuha kung kaninong TO ito
    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    // Helper scope para makuha ang mga active travels sa specific date
    public function scopeIsOnTravel($query, $employeeId, $date) {
        return $query->where('employee_id', $employeeId)
                    ->where('status', 'approved')
                    ->whereDate('date_from', '<=', $date)
                    ->whereDate('date_to', '>=', $date);
    }
}
