<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
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
        'employment_type',
        'is_active',
        'profile_picture'
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
    ];

    public function getFullNameAttribute() 
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    protected static function booted()
    {
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
