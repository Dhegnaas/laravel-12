<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class AuditTrail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected function date(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn($value) => Carbon::parse($value)->format('d-m-Y'),
        );
    }
    public function getExtraAttribute($value)
    {
        return json_decode($value);
    }

    public function setExtraAttribute($value)
    {
        $this->attributes['extra'] = json_encode($value);
    }

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'user'); // 'user' is your foreign key
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
