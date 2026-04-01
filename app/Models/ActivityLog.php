<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'ip_address',
    ];

    // each log belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
