<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'description',
        'price',
        'featured_image',
        'featured_image_organizational_name',
    ];      



    public function user()
    {
        return $this->belongsTo(User::class);
    }
        
}
