<?php

namespace App\Http\Controllers\Favorite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentListResource;
use App\Service\Favorite\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggel($apartment_id,FavoriteService $service)
    {

      $action=$service->toggel($apartment_id,auth('user_api')->id());
      return response()->json([
        'status'=>true,
        'code'=>200,
        'message'=> $action==='added'?
        'favorite':'un_favorite',
      ],200);
    }


    public function favoriteList(FavoriteService $service)
    {
       $favorite= $service->favoriteList(auth('user_api')->user());
      return response()->json([
        'status'=>true,
        'code'=>200,
        'data'=> $favorite ?ApartmentListResource::collection($favorite):__('apartments.no_contant'),
      ],200);
    }
}






