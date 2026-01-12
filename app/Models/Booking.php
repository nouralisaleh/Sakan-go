<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];
    public function review()
    {
        return $this->hasOne(Review::class,'');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function bookingUpdateRequests()
    {
        return $this->hasMany(BookingUpdateRequest::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
