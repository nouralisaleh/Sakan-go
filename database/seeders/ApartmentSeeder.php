<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Apartment;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Apartment::create([
            'title' => 'small sudio',
            'description' => 'suitable for young',
            'city' => 'DAMASCUS',
            'governorate' => 'DAMASCUS',
            'rooms' => 3,
            'area' => 'BARZAH',
            'price' => 90000,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=>100,
            'user_id' =>  2,
        ])->images()->createMany([
           ['path'=>'apartments/1/small-juvenile-bedroom-arrangement (1).jpg'],
              ['path'=>'apartments/1/prydumano-design-t0uOEEuAy1M-unsplash.jpg'],
              ['path'=>'apartments/1/home-office-with-computer-shelf.jpg'],
              ['path'=>'apartments/1/prydumano-design-vYlmRFIsCIk-unsplash.jpg'],
              ['path'=>'apartments/1/prydumano-design-VZ2z8ozzy10-unsplash.jpg']
    ]);

        // شقق Omar
        Apartment::create([
            'title' => 'Luxury Apartment',
            'description' => 'Spacious apartment in city center',
            'city' => 'DAMASCUS',
            'governorate' => 'DAMASCUS',
            'rooms' => 4,
            'area' => 'MEZZEH',
            'price' => 40000,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=> 120,
            'user_id' => 2,
        ])->images()->createMany([
            ['path'=>'apartments/2/dining-area-comfortable-studio-flat-hotel-room.jpg'],
            ['path'=>'apartments/2/modern-laundry-room-with-white-appliances-storage.jpg'],
            ['path'=>'apartments/2/soft-pastel-hues-room-children.jpg'],
            ['path'=>'apartments/2/studio-arrangement-work.jpg']
        ]);

        Apartment::create([
            'title' => 'for honeymoon',
            'city' => 'DAMASCUS',
            'governorate' => 'DAMASCUS',
            'rooms' => 3,
            'area' => 'MALKI',
            'price' => 120000,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=> 130,
            'user_id' => 1,
        ])->images()->createMany([
            ['path'=>'apartments/3/frames-for-your-heart-MyeOnGcibCQ-unsplash.jpg'],
            ['path'=>'apartments/3/huy-nguyen-AB-q9lwCVv8-unsplash.jpg'],
            ['path'=>'apartments/3/small-juvenile-bedroom-arrangement (2).jpg'],
        ]);
        Apartment::create([
            'title' => 'for university life',
            'description' => 'a quiet area close to transportation',
            'city' => 'DAMASCUS',
            'governorate' => 'DAMASCUS',
            'rooms' => 3,
            'area' => 'KAFR_SOUSEH',
            'price' => 80000,
            'floor_number'=>3,
            'is_furnished'=>false,
            'size'=> 120,
            'user_id' => 1,
        ])->images()->createMany([
            ['path'=>'apartments/4/photo_3_2026-01-12_22-31-26.jpg'],
            ['path'=>'apartments/4/photo_4_2026-01-12_22-31-26.jpg'],
            ['path'=>'apartments/4/photo_6_2026-01-12_22-31-26.jpg']
        ]);

    }
}
