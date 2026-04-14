<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $primaryKey = 'employee_id';   // importante 'to
    public $incrementing = false;            // dahil string, hindi auto-increment
    protected $keyType = 'string';           // string ang key

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'bureau',
        'position',
        'division',
        'salary',
        'leave_credits',
        'employment_type',
        'is_active',
        'profile_picture'
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'leave_credits' => 'decimal:3',
    ];

    public function getRouteKeyName()
    {
        return 'employee_id';
    }

    public function getFullNameAttribute() 
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    protected static function booted()
    {
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }
}
