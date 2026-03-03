<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable; //
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'employee_id',
        'username',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
    ];

    // IMPORTANTE: Ito ang magko-connect sa employees table
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            
            // Gawa ng random password at i-hash
            $user->password = Hash::make(Str::random(12));
        });
    }
}
