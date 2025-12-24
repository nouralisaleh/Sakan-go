<?php

namespace App\Service\Apartment;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApartmentFilteringService{

 public function filter(array $filters)
    {
 
    $query = Apartment::query();

    $query->when($filters['city'] ?? null, fn($q, $v) => $q->where('city', $v));
    $query->when($filters['governorate'] ?? null, fn($q, $v) => $q->where('governorate', $v));
    $query->when($filters['area'] ?? null, fn($q, $v) => $q->where('area', $v));

    if (array_key_exists('is_furnished', $filters)) {
        $query->where('is_furnished', $filters['is_furnished']);
    }
    if (!empty($filters['latest'])) {
        $query->latest()->limit(5); 
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

    return $query->get();
}
public function showLatest()
{
    return Apartment::latest()->limit(8)->get();
}
    

}