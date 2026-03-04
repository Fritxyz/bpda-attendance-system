<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; //
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'employee_id',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
        // o belongsTo kung mas gusto mong i-treat as "belongs to"
        // return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Optional: para mas madaling gamitin sa Blade
    public function getFullNameAttribute()
    {
        return $this->employee 
            ? trim("{$this->employee->first_name} {$this->employee->middle_name} {$this->employee->suffix}")
            : 'Unknown Employee';
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            // Kung walang nilagay na password sa form, tsaka lang tayo gagawa ng random
            if (!$user->password) {
                $user->password = Hash::make(Str::random(12));
            } else {
                // Kung may nilagay sa form (halimbawa Admin ang nag-set), i-hash natin ito
                // Pero siguraduhin na hindi pa ito naka-hash para hindi ma-double hash
                $user->password = Hash::make($user->password);
            }
        });
    }
}
