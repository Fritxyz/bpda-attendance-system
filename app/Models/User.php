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
