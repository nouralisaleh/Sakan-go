<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::create([
            'user_id'=>1,
            'booking_id'=>1,
            'amount'=>30000,
            'payment_method'=>'wallet',
            'payment_status'=>'completed'

        ]);
          Payment::create([
            'user_id'=>2,
            'booking_id'=>2,
            'amount'=>25000,
            'payment_method'=>'wallet',
            'payment_status'=>'completed'

        ]);
    }
}
