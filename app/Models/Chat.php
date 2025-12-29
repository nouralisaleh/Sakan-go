<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = [];
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
