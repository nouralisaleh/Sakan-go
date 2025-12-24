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
            'city' => 'mzah',
            'governorate' => 'damascus',
            'rooms' => 1,
            'area' => 'Autostrad Al Mzah',
            'price' => 90000.00,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=>100,
           'user_id' =>  6,
        ]);

        // شقق Omar
        Apartment::create([
            'title' => 'Luxury Apartment',
            'description' => 'Spacious apartment in city center',
            'city' => 'Cairo',
            'governorate' => 'Cairo',
            'rooms' => 4,
            'area' => 'Madinaty',
            'price' => 400000.00,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=> 120,
            'user_id' => 6,
        ]);
    }
}
