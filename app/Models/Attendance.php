<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'am_in',
        'am_out',
        'pm_in',
        'pm_out',
        'ot_in',
        'ot_out',
        'remarks'
    ];

    protected $casts = [
        'am_in' => 'datetime',
        'am_out' => 'datetime',
        'pm_in' => 'datetime',
        'pm_out' => 'datetime',
        'ot_in' => 'datetime',
        'ot_out' => 'datetime',
        'attendance_date' => 'date',
    ];

    /**
     * Ang attendance record ay pag-aari ng isang Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // todo: fix validation sa attendance
    // also hingiin lahat kay maam ya ang lahat ng positions na meron sa bpda
}
