<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
