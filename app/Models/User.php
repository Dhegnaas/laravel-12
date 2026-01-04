<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'users';
    protected $guarded = [];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value),
        );
    }
    
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'action_id', 'id')->where('related_to', 'user')->with('user');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


}
