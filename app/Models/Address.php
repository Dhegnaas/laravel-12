<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
        protected $fillable = [
        'user_id',
        'country',
        'district',
        'location',
        'area',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
