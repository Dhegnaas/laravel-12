<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model {
    protected $table = 'addresses';
    protected $guarded = [];

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function auditTrails() {
        return $this->hasMany(AuditTrail::class, 'action_id', 'id')->where('related_to', 'address')->with('user');
    }
}

