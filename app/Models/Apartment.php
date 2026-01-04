<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
        public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public  function favoriteByUsers()
    {
        return $this->belongsToMany(User::class,'favorites');
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
  
}
