<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Booking::create([
        'apartment_id'=>1,
        'user_id'=>1,
        'start_date'=>date('Y-m-d', strtotime('2025-1-1')),
        'end_date'=>date('Y-m-d', strtotime('2025-1-5')),
        'total_price'=>500,
        'status'=>'confirmed',
        'longitude'=>31.2357,
        'latitude'=>30.0444,

       ]);
        Booking::create([
        'apartment_id'=>1,
        'user_id'=>2,
        'start_date'=>date('Y-m-d', strtotime('2026-1-1')),
        'end_date'=>date('Y-m-d', strtotime('2026-1-2')),
        'total_price'=>500,
        'status'=>'confirmed',
        'longitude'=>31.2357,
        'latitude'=>30.0444,
       ]);
    }
}
