<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Notifications\Notifiable;

class User  extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;


    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $hidden = [
        'remember_token',
    ];
    protected $casts = [
        'rejected_reason' => 'array',
    ];
    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
        ];
    }


    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
    public function ownerRequest()
    {
        return $this->hasOne(OwnerRequest::class);
    }
    public function chatsAsTenant()
    {
        return $this->hasMany(Chat::class, 'tenant_id');
    }
    public function chatsAsOwner()
    {
        return $this->hasMany(Chat::class, 'owner_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    
}
