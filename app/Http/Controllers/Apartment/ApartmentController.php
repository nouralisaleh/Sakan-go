<?php

namespace App\Http\Controllers\Apartment;
use App\Http\Controllers\Controller;

use App\Http\Requests\Apartment\ApartmentFilteringRequest;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\searchWithCityRequest;
use App\Http\Requests\Apartment\SearchWithPriceRequest;
use App\Http\Requests\Apartment\searchWithGovernorateRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;
use App\Http\Requests\Apartment\DeleteApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Service\Apartment\ApartmentFilteringService;
use App\Service\Apartment\ApartmentFilteringService as ApartmentApartmentFilteringService;
use App\Service\Apartment\ApartmentService;
use Exception;


class ApartmentController extends Controller
{
    public function show(ApartmentService $apartmentService)
    {
     $apartments=$apartmentService->show();
      return response()->json([
        'status' => true,
        'data'=> ApartmentResource::collection($apartments),
    
    ]);
     
    }
public function store(StoreApartmentRequest $request, ApartmentService $service)
{
    $user = auth('user_api')->user();

    $validated = $request->safe()->except('images');
    $images = $request->file('images', []);

    $apartment = $service->store(
        $validated,
        $images,
        $user->id
    );

    return response()->json([
        'status' => true,
        'data' => new ApartmentResource($apartment),
    ], 201);
}


    public function update(Apartment $apartment,UpdateApartmentRequest $request,ApartmentService $apartmentService)
    {
     $validated =$request->safe()->except('images');
     $images = $request->file('images',[]);

     $updatedapartment=$apartmentService->update($apartment,$validated,$images);
     return response()->json([
        'status'=>true,
        'message'=>__('apartments.updated_successful'),
        'data'=>new ApartmentResource($updatedapartment),
    ],200);
    }

    public function delete($apartment,ApartmentService $apartmentService)
    {
        $apartment=Apartment::find($apartment);
        if(!$apartment)
          return response()->json([
           'status'=>false,
           'message'=>__('apartments.deletion_failed')

        ]);
        $deletedapartment=$apartmentService->delete($apartment); 
        return response()->json([
            'status'=>true,
            'message'=>__('apartments.deletion_successful'),
        ],200);
    } 
    
    public function filter(ApartmentFilteringRequest $request,ApartmentFilteringService $apartmentFilteringService)
    {
      $validated=$request->validated();
      $filter=$apartmentFilteringService->filter($validated);
      if($filter->isEmpty()) 
        return response()->json([
          'status'=>false,
          'message'=>__('apartments.no_contant'),
        ] ,200);
        return response()->json([
            'status'=>true,
            'data' =>apartmentResource::collection($filter),
 
    ]);
    
    }
    public function apartmentOwner(Apartment $apartment,ApartmentService $apartmentService)
    {
     return response()->json([
       'status'=>true,
       'data'=>$apartmentService->apartmentOwner($apartment),
     ]);
    }
    public function showLatest(ApartmentFilteringService $apartmentFilteringService)
    {
        $latest=$apartmentFilteringService->showLatest();
        return response()->json([
            'status'=>true,
            'date'=>apartmentResource::collection($latest),
        ]);
    }




    
}
