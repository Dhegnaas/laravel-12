<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosts extends Model
{
        protected $table = 'job_posts';
    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'action_id', 'id')->where('related_to', 'job')->with('user');
    }
}
