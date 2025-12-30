<?php
namespace App\Service\Favorite;

use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use App\Models\Favorite;

class FavoriteService{
    public function toggel($apartment_id,$user_id)
    {
       $apartment=Apartment::where('id',$apartment_id)->first();
       if(!$apartment)
       {
          throw new ModelNotFoundException('APARTMENT_NOT_FOUND');
       }
       $user =User::find($user_id);
       if($user->favoriteApartments()->where('apartment_id',$apartment_id)->exists())
       {
         $user->favoriteApartments()->detach($apartment_id);
         return 'removed';
       }

       $user->favoriteApartments()->attach($apartment_id);

       return 'added';
    }
    public function favoriteList($user)
    {
        return $user->favoriteApartments()->with('images')->get();
    }
}