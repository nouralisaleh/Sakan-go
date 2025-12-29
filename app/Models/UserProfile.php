<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
