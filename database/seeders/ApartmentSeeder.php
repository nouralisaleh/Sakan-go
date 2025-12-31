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
           'user_id' =>  1,
        ])->images()->create([
            'path'=>'apartments/6/UivhVgexduFzBKngQAvgKi9azg8axYpKPqIpqO7p.jpg'
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
            'user_id' => 2,
        ])->images()->create([
            'path'=>'apartments/6/UivhVgexduFzBKngQAvgKi9azg8axYpKPqIpqO7p.jpg'
        ]);

        Apartment::create([
            'title' => 'for honeymoon',
            'city' => 'damascuse',
            'governorate' => 'damascuse',
            'rooms' => 4,
            'area' => 'Al malki',
            'price' => 1200000,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=> 130,
            'user_id' => 1,
        ])->images()->create([
            'path'=>'apartments/6/UivhVgexduFzBKngQAvgKi9azg8axYpKPqIpqO7p.jpg'
        ]);
        Apartment::create([
            'title' => 'for university life',
            'description' => 'a quiet area close to transportation',
            'city' => 'damascuse',
            'governorate' => 'damascuse',
            'rooms' => 4,
            'area' => 'kafersosa',
            'price' => 800000,
            'floor_number'=>3,
            'is_furnished'=>true,
            'size'=> 120,
            'user_id' => 1,
        ])->images()->create([
            'path'=>'apartments/5/eSvUOYYxRDxtSxxlNeiJ2VT94qGxVezYI6lp1c7m.jpg'
        ]);

    }
}
