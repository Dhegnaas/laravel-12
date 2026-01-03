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
    // Scope: status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

        // ✅ Scope: country
    public function scopeCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // ✅ Scope: search (district, location, area)
    public function scopeSearch($query, $terms)
    {
        return $query->where(function ($q) use ($terms) {
            $q->where('district', 'LIKE', "%{$terms}%")
              ->orWhere('location', 'LIKE', "%{$terms}%")
              ->orWhere('area', 'LIKE', "%{$terms}%");
        });
    }
}

