<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $guarded = [];


    protected function pages(): Attribute
    {
        return Attribute::make(
            set: fn($value) => json_encode($value),
            get: fn($value) => json_decode($value),
        );
    }
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'action_id', 'id')->where('related_to', 'role')->with('user');
    }
}
