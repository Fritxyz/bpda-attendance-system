<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrder extends Model
{
    //
    use SoftDeletes; 

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id', 'to_number', 'destination', 'purpose', 
        'date_from', 'date_to', 
    ];

    // Relation para madaling makuha kung kaninong TO ito
    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Helper scope para makuha ang mga active travels sa specific date
    public function scopeIsOnTravel($query, $employeeId, $date) {
        return $query->where('employee_id', $employeeId)
                    ->where('status', 'approved')
                    ->whereDate('date_from', '<=', $date)
                    ->whereDate('date_to', '>=', $date);
    }

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
    ];
}
