<?php

namespace App\Service\Apartment;

use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Service\Favorite\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ApartmentListResource;

class ApartmentFilteringService{

 public function filter(array $filters)
    {
 
    $query = Apartment::query()->with('images');

    $query->when($filters['city'] ?? null, fn($q, $v) => $q->where('city', $v));
    $query->when($filters['governorate'] ?? null, fn($q, $v) => $q->where('governorate', $v));
    $query->when($filters['area'] ?? null, fn($q, $v) => $q->where('area', $v));

    if (array_key_exists('is_furnished', $filters)) {
        $query->where('is_furnished', $filters['is_furnished']);
    }

    if (!empty($filters['rooms'])) {
        $query->where('rooms', $filters['rooms']);
    }

    if (!empty($filters['min_price'])) {
        $query->where('price', '>=', $filters['min_price'])
              ->orderBy('price', 'asc');
    }

    if (!empty($filters['max_price'])) {
        $query->where('price', '<=', $filters['max_price'])
              ->orderBy('price', 'desc');
    }

    if (!empty($filters['size'])) {
        $query->where('size', $filters['size']); 
    }

    return ApartmentListResource::collection($query->get());
}
public function showLatest()
{
    return ApartmentListResource::collection(Apartment::with('images')->latest()->limit(8)->get());
}
public function home($user)
{
    return [
        'latest apartments:'=>[
            'title'=>'latest apartments added:',
            'apartments'=>$this->showLatest(),
        ],
        'apartments in Damascuse'=>[
            'title'=>'apartments in damascuse:',
             'apartments'=>$this->filter(['city'=>'damascuse'])
        ],
        'apartments at reasonable price :'=>[
            'title'=>'apartments in damascuse:',
             'apartments'=>$this->filter(['min_price'=>600000]),
        ], 
        'furnished apartments:'=>[
            'title'=>'furnished apartments:',
            'apartments:'=>$this->filter(['is_furnished'=> true])
        ],
        'favorite apartments:'=>[
            'title'=>'your favorite apartments:',
            'apartments'=>$user->favoriteApartments()->pluck('apartment_id'),
        ],


    ];
}

    

}