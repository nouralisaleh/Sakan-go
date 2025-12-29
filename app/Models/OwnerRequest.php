<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerRequest extends Model
{
    protected $guarded = [];
    protected $casts = [
    'request_rejected_reason' => 'array',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
