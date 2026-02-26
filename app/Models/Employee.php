<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        'role',
        'is_active',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
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
         static::creating(function ($employee) {
            $employee->username = str_replace('-', '', $employee->employee_id);

            $password_plain = Str::random(12);
            $employee->password = Hash::make($password_plain);
        });
    }
}
