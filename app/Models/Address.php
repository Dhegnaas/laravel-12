<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country',
        'district',
        'location',
        'area',
        'status',
    ];

    /**
     * Xiriirka: Cinwaanku wuxuu ka tirsan yahay hal User.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Professional Scope: Waxay kuu sahlaysaa inaad tiri Address::status('submitted')->get();
     */
    public function scopeStatus($query, $status) {
        return $query->where('status', $status);
    }
}

